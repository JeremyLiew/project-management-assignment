<!DOCTYPE html>
<!-- Jeremy -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Overdue Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #1b142d !important;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            font-size: 28px;
            color: #00413f;
            margin-top: 20px;
        }

        p {
            text-align: center;
            font-size: 15px;
            color: #333333;
            line-height: 1.5;
            margin: 10px 0;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #065C66;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: bold;
            border-radius: 5px;
        }

        .content-row {
            display: flex;
            height: 100%;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="content-row">
        <div class="container">
            <h1>Task Overdue Alert</h1>
            <p>Hi {{ $assignee }},</p>
            <p>The task <strong>{{ $task }}</strong> is overdue. Please review it as soon as possible.</p>
            <p>Log in to your account to view and manage your tasks.</p>

            <div class="button-container">
                <a href="{{ url('/tasks') }}" class="button">View Tasks</a>
            </div>
        </div>
    </div>
</body>
</html>
