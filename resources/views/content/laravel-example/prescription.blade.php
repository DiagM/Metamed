<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        /* Add your custom CSS styles here for the prescription layout */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        .prescription-header {
            margin-bottom: 20px;
        }

        .medication {
            margin-bottom: 10px;
        }

        .doctor-info {
            float: left;
        }

        .patient-info {
            float: right;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .title {
            text-align: center;
            clear: both;
        }

        .date {
            text-align: left;
            margin-top: 20px;
        }

        hr {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="prescription-header clearfix">
        <div class="doctor-info">
            <p>Doctor: {{ $doctorName }}</p>
            <p>Department: {{ $doctorDepartment }}</p>
            <p>Hospital: {{ $doctorHospital }}</p>
        </div>
        <div class="title">
            <h1>Prescription</h1>
        </div>
        <div class="date">
            <p>Created at: {{ $currentDate }}</p>
        </div>
        <div class="patient-info">
            <p>Patient Name: {{ $patientName }}</p>
            <p>Patient Age: {{ $patientAge }} years</p>
        </div>
    </div>

    @foreach ($medications as $index => $medication)
        <div class="medication">
            <h3>Medication: {{ $medication }}</h3>
            <p>Dosage: {{ $dosages[$index] }}</p>
            <p>Instructions: {{ $instructions[$index] }}</p>
        </div>
        <hr>
    @endforeach

</body>

</html>
