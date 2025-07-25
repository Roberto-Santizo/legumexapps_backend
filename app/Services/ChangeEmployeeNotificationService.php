<?php

namespace App\Services;

use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Graph;

class ChangeEmployeeNotificationService
{
    public function sendNotification($changes)
    {
        $this->sendEmailNotification($changes);
    }

    private function sendEmailNotification($changes)
    {
        $accessToken = $this->getAccessToken();

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply@legumex.net';

        $emails = explode(',', env('NOTIFY_EMAILS_CHANGES_EMPLOYEES'));

        $recipients = array_map(function ($email) {
            $recipient = new Recipient();
            $recipient->setEmailAddress(new EmailAddress(['address' => $email]));
            return $recipient;
        }, $emails);

        $body = [
            'message' => [
                'subject' => 'Cambios de Empleados',
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $this->buildMessageBody($changes),
                ],
                'toRecipients' => $recipients,
            ],
            'saveToSentItems' => true,
        ];

        $graph->createRequest("POST", "/users/$userId/sendMail")
            ->attachBody($body)
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


    private function buildMessageBody($changes)
    {
        $rows = '';
        foreach ($changes as $change) {
            $old = $change['old_employee'];
            $new = $change['new_employee'];

            $rows .= <<<HTML
            <tr>
                <td style="padding: 10px; border: 1px solid #ccc;">{$old['name']}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">{$old['code']}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">{$old['position']}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">{$new['name']}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">{$new['code']}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">{$new['position']}</td>
            </tr>
        HTML;
        }

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
                                    <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Cambios Realizados en Linea</h1>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 20px;">
                                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                        <thead>
                                            <tr style="background-color: #f0f0f0;">
                                                <th style="padding: 10px; border: 1px solid #ccc;">Empleado Anterior</th>
                                                <th style="padding: 10px; border: 1px solid #ccc;">Código</th>
                                                <th style="padding: 10px; border: 1px solid #ccc;">Posición</th>
                                                <th style="padding: 10px; border: 1px solid #ccc;">Empleado Nuevo</th>
                                                <th style="padding: 10px; border: 1px solid #ccc;">Código</th>
                                                <th style="padding: 10px; border: 1px solid #ccc;">Posición</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            $rows
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 20px; text-align: center;">
                                    <h1 style="margin: 0; font-size: 14px;">Validar los cambios en el apartado de permisos</h1>
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
