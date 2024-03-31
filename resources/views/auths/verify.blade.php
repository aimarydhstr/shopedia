<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <p>Halo {{ $user->name }},</p>
    <p>Silakan klik tautan berikut untuk melakukan verifikasi email:</p>
    <a href="{{ route('auths.verify', $user->email_verified_token) }}">Verifikasi Email</a>
    <p>Jika Anda tidak meminta verifikasi email ini, abaikan pesan ini.</p>
</body>
</html>
