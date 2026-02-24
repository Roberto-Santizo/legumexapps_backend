<?php

namespace App\Providers;

use App\Abstracts\EmailProvider as AbstractsEmailProvider;
use App\Messages\MessageBuilder;
use App\Models\LoteChecklistCondition;
use App\Models\PlantationControl;
use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Graph;


class EmailProvider extends AbstractsEmailProvider
{
    public static function getAccessToken()
    {
        $guzzle = new \GuzzleHttp\Client();

        $url = 'https://login.microsoftonline.com/' . env('MICROSOFT_TENANT_ID') . '/oauth2/v2.0/token';
        $tokenResponse = $guzzle->post($url, [
            'form_params' => [
                'client_id' => env('MICROSOFT_CLIENT_ID'),
                'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ],
        ]);

        $token = json_decode($tokenResponse->getBody()->getContents(), true);
        return $token['access_token'];
    }

    public function sendLotesValidationEmail(PlantationControl $cdp, LoteChecklistCondition $condition)
    {
        $accessToken = static::getAccessToken();
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply@legumex.net';

        $emails = explode(',', env('NOTIFY_EMAILS_AGRICOLA_NOTIFICATION'));

        $recipients = array_map(function ($email) {
            $recipient = new Recipient();
            $recipient->setEmailAddress(new EmailAddress(['address' => $email]));
            return $recipient;
        }, $emails);

        $body = [
            'message' => [
                'subject' => 'Notificación Validación de Lote',
                'body' => [
                    'contentType' => 'HTML',
                    'content' => MessageBuilder::lotesValidationBuild($cdp, $condition),
                ],
                'toRecipients' => $recipients,
            ],
            'saveToSentItems' => true,
        ];

        $graph->createRequest("POST", "/users/$userId/sendMail")->attachBody($body)->execute();
    }
}
