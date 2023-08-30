<?php

// Include the Composer-generated autoloader
require 'vendor/autoload.php';

use Google_Client;
use Google_Service_Drive;

$client = new Google_Client();
// Get your credentials from the console
$client->setClientId('<YOUR_CLIENT_ID>');
$client->setClientSecret('<YOUR_CLIENT_SECRET>');
$client->setRedirectUri('<YOUR_REGISTERED_REDIRECT_URI>');
$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));

session_start();

if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
    } else {
        $client->setAccessToken($_SESSION['access_token']);
    }

    $service = new Google_Service_Drive($client);

    // Insert a file
    $file = new Google_Service_Drive_DriveFile();
    $file->setName(uniqid() . '.jpg');
    $file->setDescription('A test document');
    $file->setMimeType('image/jpeg');

    $data = file_get_contents('a.jpg');

    $createdFile = $service->files->create($file, array(
        'data' => $data,
        'mimeType' => 'image/jpeg',
        'uploadType' => 'multipart'
    ));

    print_r($createdFile);
} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
