<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #4b5563;
        }
        .code-container {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }
        .code-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .warning p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .security-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecfdf5;
            border-radius: 8px;
            border: 1px solid #d1fae5;
        }
        .security-notice p {
            margin: 0;
            font-size: 13px;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion Residence</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                @if($userName)
                    Bonjour {{ $userName }},
                @else
                    Bonjour,
                @endif
            </div>
            
            <div class="message">
                Nous avons recu votre demande d'inscription a la plateforme de gestion de residence. 
                Pour finaliser votre inscription et securiser votre compte, veuillez utiliser le code de verification ci-dessous :
            </div>
            
            <div class="code-container">
                <div class="code">{{ $code }}</div>
                <div class="code-label">Code de verification</div>
            </div>
            
            <div class="warning">
                <p><strong>Important :</strong> Ce code expire dans 15 minutes et ne peut etre utilise qu'une seule fois.</p>
            </div>
            
            <div class="message">
                Si vous n'avez pas initie cette demande, vous pouvez ignorer cet email en toute securite. 
                Aucun compte ne sera cree sans la validation de ce code.
            </div>
            
            <div class="security-notice">
                <p><strong>Securite :</strong> Ne partagez jamais ce code avec quiconque. Notre equipe ne vous demandera jamais votre code de verification par telephone ou email.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Gestion Residence. Tous droits reserves.</p>
            <p>Cet email a ete envoye automatiquement, merci de ne pas y repondre.</p>
        </div>
    </div>
</body>
</html>
