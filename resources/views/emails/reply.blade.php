<!doctype html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #1B2333; line-height: 1.5">
    <p>Dear {{ $enquiry->name }},</p>
    <p>{!! nl2br(e($reply->body)) !!}</p>
    <p>{{ $reply->user?->name ?? 'The office' }}<br>{{ config('afroiso.name') }}<br>{{ config('afroiso.phone') }} | {{ config('afroiso.email') }}</p>
    <hr style="border: 0; border-top: 1px solid #D9DEE8; margin: 24px 0">
    <p style="color: #4A5568; font-size: 13px">Your message of {{ $enquiry->created_at->format('j F Y') }}:</p>
    <p style="color: #4A5568; font-size: 13px">{!! nl2br(e($enquiry->message)) !!}</p>
</body>
</html>
