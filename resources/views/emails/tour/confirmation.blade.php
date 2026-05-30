{{-- Transactional email template — rendered into an outbound email body.
     Email clients strip <link>/<script> and most don't honour external
     CSS classes reliably, so styling stays inline + a small <style> for
     things clients DO honour. Palette matches dev-ui teal primary. --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tour confirmation</title>
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Inter, Roboto, Arial, sans-serif; line-height: 1.6; color: #0f172a; background: #f8fafc; }
        a { color: #0d9488; }
        table { border-collapse: collapse; }
    </style>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Inter,Roboto,Arial,sans-serif; color:#0f172a; line-height:1.6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;">
        <tr>
            <td align="center" style="padding: 24px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f766e 0%,#0d9488 60%,#115e59 100%); padding:28px 24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:22px; font-weight:600; letter-spacing:-0.01em;">Tour confirmation</h1>
                            <p style="margin:6px 0 0 0; font-size:13px; color:rgba(255,255,255,0.85);">Your booking with eetstravel</p>
                        </td>
                    </tr>

                    {{-- Greeting + intro --}}
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 12px 0; font-size:18px; font-weight:600; color:#0f172a;">
                                Dear {{ $tour->client->name ?? 'Valued Customer' }},
                            </h2>
                            <p style="margin:0; font-size:15px; color:#334155;">
                                Your tour has been confirmed — we're excited to have you join us. The details below are the final, agreed version of your booking.
                            </p>
                        </td>
                    </tr>

                    {{-- Details table --}}
                    <tr>
                        <td style="padding:0 24px 8px 24px;">
                            <h3 style="margin:0 0 12px 0; font-size:14px; font-weight:600; color:#0f172a; text-transform:uppercase; letter-spacing:.05em;">Tour details</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden;">
                                <tr>
                                    <th align="left" style="width:40%; padding:10px 14px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:13px; color:#475569; font-weight:500;">Tour name</th>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">{{ $tour->name }}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding:10px 14px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:13px; color:#475569; font-weight:500;">Start date</th>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">{{ $tour->start_date ?? 'TBD' }}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding:10px 14px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:13px; color:#475569; font-weight:500;">End date</th>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">{{ $tour->end_date ?? 'TBD' }}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding:10px 14px; background:#f8fafc; font-size:13px; color:#475569; font-weight:500;">Status</th>
                                    <td style="padding:10px 14px; font-size:14px; color:#0f172a;">{{ $tour->status->name ?? 'Confirmed' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Optional custom content (note / itinerary excerpt etc.) --}}
                    @if($customContent)
                        <tr>
                            <td style="padding:8px 24px 16px 24px;">
                                <div style="padding:14px 16px; background:#f0fdfa; border-left:4px solid #0d9488; border-radius:0 6px 6px 0; font-size:14px; color:#0f172a;">
                                    {!! $customContent !!}
                                </div>
                            </td>
                        </tr>
                    @endif

                    {{-- CTA + closing --}}
                    <tr>
                        <td style="padding:8px 24px 24px 24px;">
                            <p style="margin:0 0 20px 0; font-size:14px; color:#334155;">
                                If you have any questions, please don't hesitate to contact us — we're happy to help.
                            </p>
                            <p style="margin:0; text-align:center;">
                                <a href="{{ url('/tour/' . $tour->id) }}"
                                   style="display:inline-block; padding:11px 22px; background:#0d9488; color:#ffffff; text-decoration:none; border-radius:6px; font-size:14px; font-weight:500; letter-spacing:.01em;">
                                    View tour details
                                </a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:16px 24px; border-top:1px solid #e2e8f0; background:#f8fafc; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                            <p style="margin:4px 0 0 0; font-size:11px; color:#94a3b8;">This is an automated message — please don't reply directly to this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
