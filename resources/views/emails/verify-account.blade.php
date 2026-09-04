<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Vérifiez votre compte</h2>
    <p>Bonjour {{ $user->name }},</p>
    <p>Votre code de vérification est :</p>
    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px;">{{ $code }}</p>
    <p>Ce code expire dans 15 minutes.</p>
</body>
</html>
