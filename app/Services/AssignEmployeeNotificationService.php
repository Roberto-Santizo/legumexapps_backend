<?php

namespace App\Services;

use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\Message;
use Beta\Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Graph;

class AssignEmployeeNotificationService
{
    public function sendNotification($newAsignee)
    {
        $this->sendEmailNotification($newAsignee);
    }

    private function sendEmailNotification($newAsignee)
    {
        $accessToken = $this->getAccessToken();

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply.tic@legumex.net';

        $recipient1 = new Recipient();
        $recipient1->setEmailAddress(new EmailAddress(['address' => 'soportetecnico.tejar@legumex.net']));

        $message = new Message();
        $message->setSubject('Asignación de empleado nuevo empleado' . $newAsignee->TaskProduction->line_sku->line->name);
        $message->setBody([
            'content' => $this->buildMessageBody($newAsignee),
            'contentType' => 'HTML'
        ]);
        $message->setToRecipients([$recipient1]);

        $graph->createRequest("POST", "/users/$userId/sendMail")
            ->attachBody([
                'message' => $message,
                'saveToSentItems' => "true"
            ])
            ->execute();
    }

    private function getAccessToken()
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


    private function buildMessageBody($newAsignee)
    {
        $line = $newAsignee->TaskProduction->line_sku->line->code;
        $message = <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Legumex</title>
                </head>
                <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5; color: #333333; background-color: #f4f4f4;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td align="center" style="padding: 20px 0;">
                                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                    <tr>
                                        <td style="background-color: #4a90e2; padding: 20px; text-align: center;">
                                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Se ha realizado un cambio en linea $line</h1>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 20px;">
                                            <h2 style="color: #333333; font-size: 20px;">Se ha asignado la persona $newAsignee->name a la posición $newAsignee->position.</h2>
                                            <p style="margin-bottom: 20px;">La posición de comodin $newAsignee->old_position.</p>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="background-color: #f0f0f0; padding: 20px; text-align: center; font-size: 14px; color: #666666;">
                                            <p style="margin: 0;">
                                                Este correo ha sido enviado automáticamente y tiene como propósito notificarle.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                HTML;

        return $message;
    }
}
