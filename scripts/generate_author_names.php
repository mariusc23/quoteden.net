<?php
chdir(dirname(__FILE__));

require_once('config.php');
require_once('../application/config/config.php');

    /**
     * Shortens author name.
     * @param string $name full author name
     * @param string $actual_last_name compares to actual last
     *      obtained by build_last_name
     */
    function shorten_name($name, $actual_last_name) {
        // already short enough
        if (strlen($name) < AUTHOR_SHORT_NAME_LENGTH) {
            return $name;
        }

        // keep first name
        $keep = 0;
        $author_name = explode(' ', $name);
        $count = count($author_name);
        $last_name = trim($author_name[$count-1], " \t\n\r\0\x0B!@#$%^&*()_+-={}[]|\\:\";'<>?,./");
        unset($author_name[$count-1]);

        // make all names except last name initials
        foreach ($author_name as $k => $name) {
            if (false !== stripos($name, $actual_last_name)) {
                // keep last name, there is a suffix (jr, sr...)
                $keep = $k;
                continue;
            }
            $author_name[$k] = mb_eregi_replace("^\b([A-Za-z]).+\b(.*)$", "\\1.\\2", $name);
        }

        $short_name = implode(' ', $author_name) . ' ' . $last_name;
        // still not short enough? discard initials
        if (strlen($short_name) > AUTHOR_SHORT_NAME_LENGTH) {
            foreach($author_name as $k => $name) {
                if ($keep === $k) {
                    // keep $k
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

    function build_last_name($name) {
        $name = explode(' ', $name);
        $count = count($name)-1;
        $last_name = trim($name[$count],
            " \t\n\r\0\x0B!@#$%^&*()_+-={}[]|\\:\";'<>?,./");
        if (!strcasecmp($last_name, 'jr') || !strcasecmp($last_name, 'junior')
            || !strcasecmp($last_name, 'sr') || !strcasecmp($last_name, 'senior')) {
            unset($name[$count]);
            $count--;
            $last_name = trim($name[$count],
                " \t\n\r\0\x0B!@#$%^&*()_+-={}[]|\\:\";'<>?,./");
        }
        return $last_name;
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
$statement = $db_2_link->prepare(
"UPDATE
    authors
SET
    short_name = ?,
    last_name = ?
WHERE
    id = ?");
$count = 0;
foreach ($rows as $row) {
    $last_name = build_last_name($row['name']);
    $short_name = shorten_name($row['name'], $last_name);
    $count += $statement->execute(array($short_name, $last_name, $row['id']));
}
echo $count . " authors affected\n";

echo "Done.\n";
} catch (PDOException $e) {
    die($e->getMessage());
}
