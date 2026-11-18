<!doctype html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #1B2333; line-height: 1.5">
    <h2 style="color: #0F1E45">New website enquiry</h2>
    <p><strong>Name:</strong> {{ $enquiry->name }}</p>
    @if ($enquiry->organisation)<p><strong>Organisation:</strong> {{ $enquiry->organisation }}</p>@endif
    <p><strong>Email:</strong> {{ $enquiry->email }}</p>
    @if ($enquiry->phone)<p><strong>Phone:</strong> {{ $enquiry->phone }}</p>@endif
    @if ($enquiry->interest)<p><strong>Interested in:</strong> {{ $enquiry->interest }}</p>@endif
    @if ($enquiry->subject)<p><strong>Regarding:</strong> {{ $enquiry->subject }}</p>@endif
    <p><strong>Message:</strong><br>{!! nl2br(e($enquiry->message)) !!}</p>
    <p style="color: #4A5568; font-size: 13px">Reply to this email to answer {{ $enquiry->name }} directly.</p>
</body>
</html>
