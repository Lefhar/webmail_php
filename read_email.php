<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lire l'e-mail</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Lire l'e-mail</h1>
<?php
$emailId = $_GET['id'];

$mailbox = imap_open($server, $username, $password);

if ($mailbox) {
    $overview = imap_fetch_overview($mailbox, $emailId, 0);
    $subject = $overview[0]->subject;
    $from = $overview[0]->from;
    $date = $overview[0]->date;
    $email = imap_fetchbody($mailbox, $emailId, 1);

    imap_close($mailbox);
} else {
    echo 'Erreur de connexion à la boîte aux lettres.';
}
?>
<table>
    <tr>
        <th>Sujet</th>
        <th>De</th>
        <th>Date</th>
    </tr>
    <tr>
        <td><?php echo $subject; ?></td>
        <td><?php echo $from; ?></td>
        <td><?php echo $date; ?></td>
    </tr>
</table>
<div>
    <?php echo $email; ?>
</div>
</body>
</html>
