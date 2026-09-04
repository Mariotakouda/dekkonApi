<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Réinitialisation de mot de passe</h2>
    <p>Bonjour {{ $user->name }},</p>
    <p>Voici votre code de réinitialisation :</p>
    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px;">{{ $token }}</p>
    <p>Ce code expire dans 60 minutes. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
</body>
</html>
