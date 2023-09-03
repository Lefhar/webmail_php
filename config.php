<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];

// Récupérer la valeur de la zone de temps depuis .env
$timezone = $_ENV['TIMEZONE'];

// Définir la zone de temps en fonction de la valeur depuis .env
date_default_timezone_set($timezone);

// Récupérer la valeur de la locale depuis .env
$locale = $_ENV['LOCALE'];

// Définir la locale en fonction de la valeur de .env
setlocale(LC_TIME, $locale,$_ENV['LANGAGE']);
// récupérez les variables
$server = $_ENV['IMAP_SERVER'];
$username = $_ENV['IMAP_USERNAME'];
$password = $_ENV['IMAP_PASSWORD'];