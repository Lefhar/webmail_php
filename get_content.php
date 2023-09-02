<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];
$input = file_get_contents("php://input");
$data = json_decode($input);
if (isset($data->email_id)) {
    $emailId = (int)$data->email_id;

    $mailbox = imap_open($server, $username, $password);

    if (FALSE !== $mailbox) {
        $structure = imap_fetchstructure($mailbox, $emailId);

        if (!empty($structure->parts)) {
            foreach ($structure->parts as $partNumber => $part) {
                // Traiter les parties HTML en priorité s'il en existe
                if ($part->subtype === 'HTML') {
                    $emailContent = imap_fetchbody($mailbox, $emailId, $partNumber + 1);
                    $emailContent = quoted_printable_decode($emailContent);
                    echo htmlspecialchars_decode($emailContent);
                    exit; // Arrêter le traitement après avoir trouvé le contenu HTML
                }
            }

            // Parcourir les pièces jointes
            foreach ($structure->parts as $partNumber => $part) {
                $attachmentData = imap_fetchbody($mailbox, $emailId, $partNumber + 1);
                if ($attachmentData) {
                    $attachmentName = $part->parameters[0]->value; // Nom de la pièce jointe
                    $attachmentContent = base64_decode($attachmentData); // Décodez la pièce jointe

                    // Affichez un lien vers get_attachment.php pour télécharger la pièce jointe
                    echo "<a href='get_attachment.php?email_id=$emailId&attachment_number=$partNumber' target='_blank'>$attachmentName</a><br>";
                }
            }
        }

        // Si aucun contenu HTML n'a été trouvé, afficher le contenu texte brut
        $emailContent = imap_fetchbody($mailbox, $emailId, 1);
        $emailContent = quoted_printable_decode($emailContent);
        echo htmlspecialchars_decode($emailContent);
    } else {
        echo 'Erreur de connexion à la boîte aux lettres.';
    }

    imap_close($mailbox);
} else {
    echo 'ID de l\'e-mail manquant.';
}
?>
