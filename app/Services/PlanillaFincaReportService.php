<?php

namespace App\Services;

use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\FileAttachment;
use Beta\Microsoft\Graph\Model\ItemBody;
use Beta\Microsoft\Graph\Model\Message;
use Beta\Microsoft\Graph\Model\Recipient;
use GuzzleHttp\Client;
use Microsoft\Graph\Graph;

abstract class PlanillaFincaReportService
{
    static function sendEmailNotification($file, $weekly_plan, $user)
    {
        $accessToken = static::getAccessToken();

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply@legumex.net';

        $recipient = new Recipient();
        $recipient->setEmailAddress(new EmailAddress(['address' => $user->email]));

        $attachment = new FileAttachment();
        $attachment->setOdataType('#microsoft.graph.fileAttachment');
        $attachment->setName('Planilla ' . $weekly_plan->week . '.xlsx');
        $attachment->setContentBytes(base64_encode($file));
        $attachment->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $message = new Message();
        $message->setSubject('Reporte de Planilla ' . $weekly_plan->week . ' ' . $weekly_plan->finca->name);
        $message->setBody(new ItemBody([
            'contentType' => 'HTML',
            'content' => '<p>Tu reporte esta listo!</p>'
        ]));
        $message->setToRecipients([$recipient]);
        $message->setAttachments([$attachment]);

        $graph->createRequest("POST", "/users/{$userId}/sendMail")
            ->attachBody([
                'message' => $message,
                'saveToSentItems' => true
            ])
            ->execute();
    }

    private static function getAccessToken()
    {
        $guzzle = new Client();

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
}
