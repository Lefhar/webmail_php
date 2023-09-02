<?php
// load Dotenv
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];
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
                    <th>De</th>
                    <th>Date</th>
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
                        $subject = quoted_printable_decode($overview[0]->subject);
                        $from = $overview[0]->from;
                        $date = $overview[0]->date;
                        $emailId = (int)$emailId;
                        echo "<tr class='email-row' data-email-id='$emailId'>";
                        echo "<td class='email-subject'>$subject</td>";
                        echo "<td class='email-from'>$from</td>";
                        echo "<td class='email-date'>$date</td>";
                        echo "</tr>";
                    }
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
