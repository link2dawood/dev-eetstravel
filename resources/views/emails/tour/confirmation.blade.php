<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tour Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .button { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table td, table th { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table th { background: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tour Confirmation</h1>
        </div>

        <div class="content">
            <h2>Dear {{ $tour->client->name ?? 'Valued Customer' }},</h2>

            <p>Your tour has been confirmed! We're excited to have you join us.</p>

            <h3>Tour Details:</h3>
            <table>
                <tr>
                    <th>Tour Name</th>
                    <td>{{ $tour->name }}</td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{ $tour->start_date ?? 'TBD' }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ $tour->end_date ?? 'TBD' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $tour->status->name ?? 'Confirmed' }}</td>
                </tr>
            </table>

            @if($customContent)
                <div style="margin: 20px 0; padding: 15px; background: #e8f5e9; border-left: 4px solid #4CAF50;">
                    {!! $customContent !!}
                </div>
            @endif

            <p>If you have any questions, please don't hesitate to contact us.</p>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/tour/' . $tour->id) }}" class="button">View Tour Details</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
