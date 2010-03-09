<?php
chdir(dirname(__FILE__));

require_once('config.php');
require_once('../application/config/config.php');

    function shorten_name($name) {
        if (strlen($name) < AUTHOR_SHORT_NAME_LENGTH) {
            return $name;
        }

        $author_name = explode(' ', $name);
        $count = count($author_name);
        $last_name = $author_name[$count-1];
        unset($author_name[$count-1]);
        foreach ($author_name as $k => $name) {
            $author_name[$k] = mb_eregi_replace("^\b([A-Za-z]).+\b(.*)$", "\\1.\\2", $name);
        }
        $short_name = implode(' ', $author_name) . ' ' . $last_name;
        if (strlen($short_name) > AUTHOR_SHORT_NAME_LENGTH) {
            $first = true;
            foreach($author_name as $k => $name) {
                if (1 == $count - $k || $first) {
                    $first = false;
                    continue;
                }
                unset($author_name[$k]);
                $short_name = implode(' ', $author_name) . ' ' . $last_name;
                if (strlen($short_name) <= AUTHOR_SHORT_NAME_LENGTH) {
                    break;
                }
            }
        }
        return $short_name;
    }

try {
$db_2_link = new PDO("mysql:host=" . DB_KOHANA_HOST . ";dbname=" . DB_KOHANA_NAME, DB_KOHANA_USER, DB_KOHANA_PASS);

/* get authors */
$query = '
    SELECT id, name
    FROM authors
';
$statement = $db_2_link->prepare($query);
$statement->execute();
$rows = $statement->fetchAll();

/* shorten author names */
$statement = $db_2_link->prepare("UPDATE authors SET short_name = ? WHERE id = ?");
$count = 0;
foreach ($rows as $row) {
    $short_name = shorten_name($row['name']);
    $count += $statement->execute(array($short_name, $row['id']));
}
echo $count . " author names shortened\n";

echo "Done.\n";
} catch (PDOException $e) {
    die($e->getMessage());
}
