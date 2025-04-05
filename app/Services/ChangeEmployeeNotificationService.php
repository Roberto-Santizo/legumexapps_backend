<?php

namespace App\Services;

use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\Message;
use Beta\Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Graph;

class ChangeEmployeeNotificationService
{
    public function sendNotification($assignment, $change, $transfer)
    {
        $this->sendEmailNotification($assignment, $change, $transfer);
    }

    private function sendEmailNotification($assignment, $change, $transfer)
    {
        $accessToken = $this->getAccessToken();

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply.tic@legumex.net';

        $recipient1 = new Recipient();
        $recipient1->setEmailAddress(new EmailAddress(['address' => 'soportetecnico.tejar@legumex.net']));

        $message = new Message();
        $message->setSubject('Cambio de empleado en linea ' . $assignment->TaskProduction->line->code . ' ' . $assignment->TaskProduction->line_sku->sku->name);
        $message->setBody([
            'content' => $this->buildMessageBody($assignment, $change, $transfer),
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


    private function buildMessageBody($assignment, $change, $transfer)
    {
        $line = $assignment->TaskProduction->line_sku->line->code;
        $link = 'http://localhost:5173/permisos-empleados/' . $transfer->id;
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
                                            <h2 style="color: #333333; font-size: 20px;">La persona orginalmente asignada $change->original_name con la posición $change->original_position fue reemplazada.</h2>
                                            <p style="margin-bottom: 20px;">La persona que lo sustituye es $change->new_name con la posicion $change->new_position.</p>

                                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: center;">
                                                <tr>
                                                    <td>
                                                        <a href="$link" style="background-color: #4a90e2; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Click aquí para validar el cambio</a>
                                                    </td>
                                                </tr>
                                            </table>

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
