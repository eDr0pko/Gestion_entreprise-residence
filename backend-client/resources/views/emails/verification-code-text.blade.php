🏠 GESTION RESIDENCE - Code de verification

@if($userName)
Bonjour {{ $userName }},
@else
Bonjour,
@endif

Merci de votre inscription ! Pour finaliser votre compte invite, veuillez utiliser le code de verification suivant :

** {{ $code }} **

⚠️ IMPORTANT :
• Ce code expire dans 15 minutes
• Ne partagez jamais ce code avec qui que ce soit
• Si vous n'avez pas demande ce code, ignorez cet email

Une fois votre email verifie, vous pourrez acceder a tous les services de la residence.

Cordialement,
L'equipe de gestion de la residence

---
Cet email a ete envoye automatiquement, merci de ne pas y repondre.
© {{ date('Y') }} Gestion Residence - Tous droits reserves
