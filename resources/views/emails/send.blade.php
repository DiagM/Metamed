<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0069d9;
        }

        .footer {
            font-size: 14px;
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Réinitialiser votre mot de passe</h1>
        <p>Vous Compte Metamed à été crée. Cliquez sur le bouton ci-dessous pour réinitialiser le mot de passe
            :</p>
        <p>
            <a href="{{ $url }}"
                style="padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none;">Réinitialiser
                le mot de passe</a>
        </p>
        <p class="footer">Merci,<br>{{ config('app.name') }}</p>
    </div>

</body>

</html>
