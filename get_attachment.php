<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];
if (isset($_GET['email_id']) && isset($_GET['attachment_number'])) {
    $emailId = (int)$_GET['email_id'];
    $partNumber = (int)$_GET['attachment_number'];

    $mailbox = imap_open($server, $username, $password);

    if (FALSE !== $mailbox) {
        $structure = imap_fetchstructure($mailbox, $emailId);
var_dump($structure);
        if ($structure->parts && isset($structure->parts[$partNumber])) {
            $attachment = $structure->parts[$partNumber];
            $attachmentContent = imap_fetchbody($mailbox, $emailId, $partNumber + 1);
            $attachmentName = '';

            if (isset($attachment->dparameters[0])) {
                $attachmentName = $attachment->dparameters[0]->value;
            }

            // Définir les en-têtes pour le téléchargement du fichier
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $attachmentName . '"');
            echo base64_decode($attachmentContent);
        } else {
            echo 'Pièce jointe introuvable.';
        }
    } else {
        echo 'Erreur de connexion à la boîte aux lettres.';
    }

    imap_close($mailbox);
} else {
    echo 'Informations manquantes pour télécharger la pièce jointe.';
}
?>
