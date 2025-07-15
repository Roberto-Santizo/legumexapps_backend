<?php

namespace App\Services;

use Beta\Microsoft\Graph\Model\EmailAddress;
use Beta\Microsoft\Graph\Model\Message;
use Beta\Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Graph;

class ReturnPackingMaterialNotificationService
{
    public function sendNotification($task_production)
    {
        $this->sendEmailNotification($task_production);
    }

    private function sendEmailNotification($task_production)
    {
        $accessToken = $this->getAccessToken();

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $userId = 'noreply@legumex.net';

        $recipient1 = new Recipient();
        $recipient1->setEmailAddress(new EmailAddress(['address' => 'soportetecnico.tejar@legumex.net']));

        $recipient2 = new Recipient();
        $recipient2->setEmailAddress(new EmailAddress(['address' => 'sara.xoyon@legumex.net']));

        $recipient3 = new Recipient();
        $recipient3->setEmailAddress(new EmailAddress(['address' => 'bodega.empaque@legumex.net']));

        $message = new Message();
        $message->setSubject('Devolución de material de empaque en línea ' . $task_production->line_sku->line->name . ' - ' . $task_production->line_sku->sku->code);
        $message->setBody([
            'content' => $this->buildMessageBody($task_production),
            'contentType' => 'HTML'
        ]);
        $message->setToRecipients([$recipient1]);
        $message->setToRecipients([$recipient2]);
        $message->setToRecipients([$recipient3]);

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


    private function buildMessageBody($task_production)
    {
        $url = 'https://legumexapps.com/planes-produccion/' . $task_production->weeklyPlan->id . '/' . $task_production->line->id . '?devolutionTaskId=' . $task_production->id;

        $rows = '';
        $line = $task_production->line_sku->line->name;
        $sku = $task_production->line_sku->sku->code;
        $product_name = $task_production->line_sku->sku->product_name;
        $operation_date = $task_production->operation_date->format('d-m-Y');
        $difference = $task_production->total_lbs - $task_production->total_lbs_bascula;
        $recipe = $task_production->line_sku->sku->items;

        foreach ($recipe as $item) {
            $name = $item->item->name;
            $code = $item->item->code;
            $quantity = $difference / $item->lbs_per_item;
            $rows .= <<<HTML
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;">{$code}</td>
                    <td style="padding: 10px; border: 1px solid #ccc;">{$name}</td>
                    <td style="padding: 10px; border: 1px solid #ccc;">{$quantity}</td>
                </tr>
            HTML;
        }

        $message = <<<HTML
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Legumex</title>
                </head>
                <body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:16px;line-height:1.6;color:#333;background-color:#f9fafb;">
                    <table role="presentation" style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td align="center" style="padding:30px 10px;">
                                <table role="presentation" style="width:600px; max-width:100%; border-collapse:collapse;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                    <tr>
                                        <td style="background-color:#2563eb;padding:25px;text-align:center;">
                                            <h1 style="color:#ffffff;margin:0;font-size:26px;">Material de Empaque Sobrante</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:25px;">
                                            <h2 style="color:#111827;font-size:20px;margin:0 0 10px 0;">
                                                Línea: <strong>{$line}</strong>
                                            </h2>
                                            <h2 style="color:#111827;font-size:20px;margin:0 0 10px 0;">
                                                <strong>SKU: {$sku} ({$product_name})</strong>
                                            </h2>
                                            <h2 style="color:#111827;font-size:20px;margin:0;">
                                                <strong>Fecha de Operación: {$operation_date}</strong>
                                            </h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 25px 25px 25px;">
                                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #d1d5db; border-radius:6px; overflow:hidden;">
                                                <thead style="background-color: #e5e7eb;">
                                                    <tr>
                                                        <th style="border: 1px solid #d1d5db; padding: 10px; text-transform: uppercase; font-weight: 600; font-size: 14px; color: #374151;">Código</th>
                                                        <th style="border: 1px solid #d1d5db; padding: 10px; text-transform: uppercase; font-weight: 600; font-size: 14px; color: #374151;">Descripción del producto</th>
                                                        <th style="border: 1px solid #d1d5db; padding: 10px; text-transform: uppercase; font-weight: 600; font-size: 14px; color: #374151;">Cantidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {$rows}
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                       <tr>
                                            <td style="padding: 20px; text-align: center;">
                                                <a href="{$url}" style="background-color:#2563eb;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;font-size:16px;display:inline-block;margin-top:10px;">
                                                    Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    <tr>
                                        <td style="background-color:#f3f4f6;padding:20px;text-align:center;font-size:14px;color:#6b7280;">
                                            <p style="margin:0;">
                                                Este correo ha sido enviado automáticamente con fines informativos.
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
