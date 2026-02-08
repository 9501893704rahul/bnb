<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cleaning Report</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.5; color: #374151; margin: 0; padding: 0; background-color: #f3f4f6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 24px; }
        .title { font-size: 24px; font-weight: 600; color: #111827; margin: 0; }
        .subtitle { font-size: 14px; color: #6b7280; margin-top: 8px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; }
        .button:hover { background-color: #1d4ed8; }
        .stats { display: flex; gap: 16px; margin: 24px 0; }
        .stat { flex: 1; text-align: center; padding: 16px; background: #f9fafb; border-radius: 6px; }
        .stat-value { font-size: 24px; font-weight: 700; color: #111827; }
        .stat-label { font-size: 12px; color: #6b7280; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1 class="title">Cleaning Report</h1>
                <p class="subtitle">{{ $session->property->name }} - {{ $session->scheduled_date->format('F j, Y') }}</p>
            </div>

            @if($customMessage)
                <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 16px; margin-bottom: 24px;">
                    <p style="margin: 0; color: #92400e;">{{ $customMessage }}</p>
                </div>
            @endif

            <table style="width: 100%; margin: 24px 0;">
                <tr>
                    <td style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 6px;">
                        <div style="font-size: 24px; font-weight: 700; color: #16a34a;">✓</div>
                        <div style="font-size: 12px; color: #166534;">Completed</div>
                    </td>
                </tr>
            </table>

            <p style="margin-bottom: 24px;">The cleaning session for <strong>{{ $session->property->name }}</strong> has been completed by {{ $session->housekeeper->name }}.</p>

            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ $report->share_url }}" class="button">View Full Report</a>
            </div>

            <p style="font-size: 14px; color: #6b7280;">This link will expire in 30 days. You can view all photos, task completion status, and any notes from the housekeeper.</p>
        </div>

        <div class="footer">
            <p>This email was sent automatically by the housekeeping management system.</p>
            <p>If you didn't request this report, you can safely ignore this email.</p>
        </div>
    </div>
</body>
</html>
