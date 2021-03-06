<?php
chdir(dirname(__FILE__));

require_once('config.php');

try {
$db_1_link = new PDO("mysql:host=" . DB_DRUPAL_HOST . ";dbname=" . DB_DRUPAL_NAME, DB_DRUPAL_USER, DB_DRUPAL_PASS);
$db_2_link = new PDO("mysql:host=" . DB_KOHANA_HOST . ";dbname=" . DB_KOHANA_NAME, DB_KOHANA_USER, DB_KOHANA_PASS);

/* get authors */
$query = '
    SELECT aid AS id, name, bio
    FROM quotes_authors
';
$statement = $db_1_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* migrate authors */
$statement = $db_2_link->prepare("INSERT IGNORE INTO authors(id, name, bio)
    VALUES (?, ?, ?);");
$count = 0;
foreach ($rows as $row) {
    $count += $statement->execute(array($row['id'], $row['name'], $row['bio']));
}
echo $count . " authors migrated \n";


/* get quotes */
$query = '
    SELECT node.nid AS nid, node.created AS created, node.changed AS changed, quotes.aid AS aid, node_revisions.body AS body
    FROM node, node_revisions, quotes
    WHERE
        node.nid = node_revisions.nid
        AND node.vid = node_revisions.vid
        AND node.nid = quotes.nid
        AND node.vid = quotes.vid
';
$statement = $db_1_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* migrate quotes */
$statement = $db_2_link->prepare("INSERT IGNORE INTO quotes(id, text, author_id, changed, created)
    VALUES (?, ?, ?, ?, ?);");

$count = 0;
foreach ($rows as $row) {
    $count += $statement->execute(array($row['nid'], $row['body'], $row['aid'], date('Y-m-d H:i:s', $row['changed']), date('Y-m-d H:i:s', $row['created'])));
}
echo $count . " quotes migrated \n";


/* get categories */
$query = '
    SELECT term_data.tid AS id, term_data.name AS name
    FROM term_data
';
$statement = $db_1_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* migrate quotes */
$statement = $db_2_link->prepare("INSERT IGNORE INTO categories(id, name)
    VALUES (?, ?);");

$count = 0;
foreach ($rows as $row) {
    $count += $statement->execute(array($row['id'], $row['name']));
}
echo $count . " categories migrated \n";

/* link quotes and categories */
$query = '
    SELECT term_node.tid AS category_id, term_node.nid as quote_id
    FROM term_node, node
    WHERE
        node.nid = term_node.nid
        AND node.vid = term_node.vid
';
$statement = $db_1_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* migrate quotes */
$statement = $db_2_link->prepare("INSERT IGNORE INTO quote_category(category_id, quote_id)
    VALUES (?, ?);");

$count = 0;
foreach ($rows as $row) {
    $count += $statement->execute(array($row['category_id'], $row['quote_id']));
}
echo $count . " quote categories linked\n";



/* votes */
$query = "
    SELECT content_id AS quote_id, value AS rating, timestamp AS changed, vote_source AS voter, uid AS user_id
    FROM votingapi_vote
    WHERE
        content_type = 'node'
        AND value_type = 'percent'
    ORDER BY
        changed
";
$statement = $db_1_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* migrate votes */
$votes = array();

/* clean up first */
$statement = $db_2_link->prepare("TRUNCATE TABLE voteaverages;");
$statement->execute();
$statement = $db_2_link->prepare("TRUNCATE TABLE votes;");
$statement->execute();

$statement = $db_2_link->prepare("INSERT IGNORE INTO votes(quote_id, voter, user_id, rating, changed)
    VALUES (?, ?, ?, ?, ?);");

$count = 0;
foreach ($rows as $row) {
    $votes[] = $row;
    $row['user_id'] = $row['user_id'] ? $row['user_id'] : null;
    $count += $statement->execute(array($row['quote_id'], $row['voter'], $row['user_id'], $row['rating'], date('Y-m-d H:i:s', $row['changed'])));
}
echo $count . " votes migrated\n";

/* link them to quotes */
$statement = $db_2_link->prepare("INSERT IGNORE INTO voteaverages(quote_id, average, count, changed)
    VALUES (?, ?, ?, ?);");
$statement_2 = $db_2_link->prepare("SELECT FROM voteaverages WHERE quote_id = ? LIMIT 1;");
$statement_3 = $db_2_link->prepare("UPDATE voteaverages SET average = ?, count = ?, changed = ?");

$count = 0;
foreach ($votes as $vote) {
    $statement_2->execute(array($vote['quote_id']));
    $rows = $statement_2->fetchAll();

    if ($rows) {
        // update existing row
        $row = $rows[0];
        $row['average'] = ($row['average'] * $row['count'] + $vote['rating']) / ($row['count'] + 1);
        $row['count']++;
        $count += $statement_3->execute(array($row['average'], $row['count'], date('Y-m-d H:i:s', $vote['changed'])));
    } else {
        // create new row
        $count += $statement->execute(array($vote['quote_id'], $vote['rating'], 1, date('Y-m-d H:i:s', $vote['changed'])));
    }
}
echo $count . " votes linked to quotes\n";


echo "Done.\n";
} catch (PDOException $e) {
    die($e->getMessage());
}
