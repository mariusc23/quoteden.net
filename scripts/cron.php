<?php
$script_start = microtime(true);
chdir(dirname(__FILE__));

require_once('../application/config/config.php');
require_once('config.php');

// FORMATS:
require_once('emails/starlingtech.php');
require_once('emails/quotemaster.php'); // <-- TODO: fix this STUB
// END FORMATS

$formats = array(
    array('Starlingtech', 'process_content'),
    array('Quotemaster', 'process_content'),
);

try {
$db_link = new PDO("mysql:host=" . DB_KOHANA_HOST . ";dbname=" . DB_KOHANA_NAME, DB_KOHANA_USER, DB_KOHANA_PASS);

$inserted = 0;

$q = "
    INSERT INTO
        quotequeues (id, text, author, original, email_from, email_subject)
    VALUES (
        NULL, ?, ?, ?, ?, ?
    )";
$statement = $db_link->prepare($q);

/* try to connect to IMAP */
// these constants are defined in scripts/config.php
$connection = imap_open(QUOTEDEN_EMAIL_HOST, QUOTEDEN_EMAIL_ADDRESS,
                        QUOTEDEN_EMAIL_PASSWORD) or
              die('Cannot connect to Gmail: ' . imap_last_error());

// only lookup unread emails
$emails = imap_search($connection, 'UNSEEN');

/* if emails are returned, cycle through each... */
if ($emails) {
    /* put the newest emails on top */
    $num_emails = count($emails);
    rsort($emails);

    /* for every email... */
    foreach ($emails as $email_number) {
        /* get information specific to this email */
        $overview = imap_fetch_overview($connection, $email_number, 0);
        $content = imap_qprint(imap_body($connection, $email_number));

        foreach ($formats as $format) {
            $q_arr = call_user_func($format, $content, $overview);
            if ($q_arr) {
                // insert the quotes into the db
                foreach ($q_arr as $q) {
                    $statement->execute(array(
                        $q['text'], $q['author'], $q['original'],
                        $overview[0]->from, $overview[0]->subject));
                    $inserted += $statement->rowCount();
                }

                // mark this email as read, it was processed
                imap_setflag_full($connection, $email_number, "\\SEEN");
                break;
            }
        }
    }
}

/* close the connection */
imap_close($connection);


echo "$inserted quotes inserted.\n";
echo "$num_emails emails processed.\n";
} catch (PDOException $e) {
    die($e->getMessage() . "\n");
}

$script_end = microtime(true);
echo "Script executed in ".round($script_end - $script_start, 2)." seconds.\n";