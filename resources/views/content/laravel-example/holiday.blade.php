<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Holiday Confirmation</title>
    <style>
        /* Define your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .confirmation {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Holiday Confirmation</h2>
        </div>
        <div class="details">
            <p><strong>Doctor Name:</strong> {{ $doctorName }}</p>
            <p><strong>Confirmed by:</strong> {{ $departmentName }}</p>
            <p><strong>Hospital:</strong> {{ $doctorHospital }}</p>
            <p><strong>Current Date:</strong> {{ $currentDate }}</p>
            <p><strong>Reason:</strong> {{ $reason }}</p>
            <p><strong>Start Date:</strong> {{ $start_date }}</p>
            <p><strong>End Date:</strong> {{ $end_date }}</p>
        </div>
        <div class="confirmation">
            <p><strong>Confirmation:</strong> Your holiday request has been accepted.</p>
        </div>
    </div>
</body>

</html>
