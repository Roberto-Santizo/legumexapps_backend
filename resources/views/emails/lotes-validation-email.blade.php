<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificación de Síntoma</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
<tr>
<td align="center" style="padding: 30px 15px;">
    <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
        <tr>
            <td style="background:#2f855a; padding:25px; text-align:center;">
                <h1 style="margin:0; color:#ffffff; font-size:22px;">
                    Notificación de Validación de Lote
                </h1>
            </td>
        </tr>
        <tr>
            <td style="padding:30px; color:#333333; font-size:15px;">

                <p style="margin-top:0;">
                    Se ha detectado un <strong>síntoma en un lote agrícola</strong> que requiere su atención.
                </p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:25px 0; border:1px solid #e2e8f0; border-radius:6px;">
                    <tr>
                        <td style="padding:15px; background:#f7fafc;">
                            <strong>Información del Lote</strong>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:15px;">
                            
                            <p style="margin:5px 0;">
                                <strong>Lote:</strong> {{ $cdp->lote->name }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Cultivo:</strong> {{  $cdp->crop->name  }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Síntoma Detectado:</strong> {{ $condition->symptom->symptom }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Nivel:</strong> {{  $condition->level  }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Observaciones:</strong> {{ $condition->observations }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Realizado por: </strong> {{ $condition->checklist->user->name }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>

</body>
</html>
