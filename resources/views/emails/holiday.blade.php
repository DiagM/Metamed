<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Holiday Confirmed</title>
    <style>
        /* Reset default styles */
        body,
        html {
            margin: 0;
            padding: 0;
        }

        /* Custom CSS styles for email layout */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .email-header h1 {
            color: #333333;
            font-size: 24px;
            margin: 0;
        }

        .email-content {
            margin-bottom: 20px;
        }

        .email-content p {
            color: #555555;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .email-footer {
            text-align: center;
            color: #777777;
        }

        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Holiday confirmed</h1>
            <p>Hello {{ $doctorName }},</p>
        </div>

        <div class="email-content">
            <p>Please find attached your holiday confirmation.</p>
        </div>

        <div class="email-footer">
            <p>Regards,</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
