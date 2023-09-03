<?php
require 'config.php';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boîte de réception</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid">
    <h1 class="mt-4">Boîte de réception</h1>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Sujet</th>

                </tr>
                </thead>
                <tbody>
                <?php

                $mailbox = imap_open($server, $username, $password);
                $mails = imap_search($mailbox, 'ALL');

                if ($mails) {
                    $mails = array_reverse($mails); // Inverser l'ordre des e-mails
                    foreach ($mails as $emailId) {
                        $overview = imap_fetch_overview($mailbox, $emailId);
                        $subject = imap_utf8($overview[0]->subject);

                        // Vérifiez si la conversion en UTF-8 a réussi
                        if ($subject === false) {
                            // Si la conversion échoue, utilisez le sujet brut
                            $subject = (iconv_mime_decode($overview[0]->subject,0, "ISO-8859-1"));
                        }

                        $from = imap_utf8($overview[0]->from);

                        // Vérifiez si la conversion en UTF-8 a réussi
                        if ($from === false) {
                            // Si la conversion échoue, utilisez le sujet brut
                            $from = (iconv_mime_decode($overview[0]->from,0, "ISO-8859-1"));
                        }
//var_dump($overview[0]);
                        $from = $overview[0]->from;
                // Récupérez la date au format actuel
                $dateString = $overview[0]->date;

                // Créez un objet DateTime à partir de la chaîne de date
                $date = new DateTime($dateString);

                // Définissez la zone horaire
                $date->setTimezone(new DateTimeZone($_ENV['TIMEZONE']));

                // Formatez la date en français
                $date = $date->format('d/m/Y H:i:s');
                        $emailId = (int)$emailId;?>
                        <tr class='email-row' data-email-id='<?=$emailId;?>'><td class='email-subject'><p>De : <?=$from;?> Le : <?=$date;?></p>
                                <p>Objet <?=$subject;?></p></td>

                   <?php }
                } else {
                    echo "<tr><td colspan='3'>Aucun e-mail trouvé.</td></tr>";
                }

                imap_close($mailbox);
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <div id="email-content">
                Sélectionnez un e-mail pour afficher son contenu.
            </div>
        </div>
    </div>
</div>

<script>
    const emailRows = document.querySelectorAll('.email-row');

    emailRows.forEach((row) => {
        row.addEventListener('click', () => {
            const emailId = row.getAttribute('data-email-id');
            fetch('get_content.php', {
                method: 'POST',
                body: JSON.stringify({ email_id: emailId }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('email-content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération du contenu de l\'e-mail :', error);
                });
        });
    });
</script>
</body>
</html>
