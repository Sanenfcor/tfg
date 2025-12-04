<?php
// Autoload Resend
require __DIR__ . '/../vendor/autoload.php';

use Resend\Client;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;
use Resend\Transporters\HttpTransporter;
use GuzzleHttp\Client as GuzzleClient;

function enviarEmail($para, $asunto, $html)
{
    // Configuración Resend
    $headers = new Headers([
        'Authorization' => 'Bearer re_2iR3nNpF_7eWaaZvb98kmURyDVaoTfYVp', 
        'Content-Type'  => 'application/json',
    ]);

    $baseUri = new BaseUri('https://api.resend.com');
    $guzzle = new GuzzleClient(['timeout' => 30]);
    $transporter = new HttpTransporter($guzzle, $baseUri, $headers);

    $resend = new Client($transporter);

    try {
        $resend->emails->send([
            'from' => 'KaiPets <onboarding@resend.dev>',
            'to' => ['empresa.prueba.correo.s25@gmail.com'],
            'subject' => $asunto,
            'html' => $html,
        ]);
    } catch (Exception $e) {
        error_log("ERROR ENVIANDO EMAIL: " . $e->getMessage());
    }
}
