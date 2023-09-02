<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];
header('content-type:application/json');

$mailbox = imap_open($server, $username, $password);
$mails = FALSE;

if (FALSE !== $mailbox) {
    $info = imap_check($mailbox);
    if (FALSE !== $info) {
        $nbMessages = min(500, $info->Nmsgs);
        $mails = imap_fetch_overview($mailbox, '1:' . $nbMessages, 0);
    }
}

if (FALSE !== $mails) {
    foreach ($mails as $mail) {
        $decodedSubject = imap_utf8($mail->subject);

        // Vérifiez si la conversion en UTF-8 a réussi
        if ($decodedSubject === false) {
            // Si la conversion échoue, utilisez le sujet brut
            $decodedSubject = $mail->subject;
        }

        $mail->subject = $decodedSubject;
        // Nettoyer d'autres champs si nécessaire
    }

   // var_dump($mails);
    // Renvoyer la liste des e-mails au format JSON
    echo json_encode($mails);
} else {
    echo json_encode([]);
}

imap_close($mailbox);

