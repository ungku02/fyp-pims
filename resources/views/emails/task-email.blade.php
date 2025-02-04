<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $type === 'new_task' ? 'New Task Assigned' : ($type === 'overdue' ? 'Task Overdue Alert' : ($type === 'swap_request' ? 'Task Swap Request' : 'Task Swap Notification')) }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease-in-out;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background: linear-gradient(45deg, #0056b3, #003c80);
            box-shadow: 0 6px 14px rgba(0, 123, 255, 0.4);
            transform: translateY(-2px);
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="https://scontent.fmkz1-2.fna.fbcdn.net/v/t39.30808-6/476557985_122123579378415463_8187080501621605482_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=127cfc&_nc_ohc=7Geprea6AiMQ7kNvgFkWBrs&_nc_zt=23&_nc_ht=scontent.fmkz1-2.fna&_nc_gid=AKNt3sgde5UdIrgQSuYMWCN&oh=00_AYCB-a6w3tN1QW7Blp6TyPBeNBdHU0UcuXyyqe_4kjvRvw&oe=67A77D48" alt="Project Logo">
        </div>

        <!-- Email Content -->
        <div class="content">
            <h2>Hello, {{ $user->name }}</h2>

            @if ($type === 'new_task')
                <p>You have been assigned a new task: <strong>{{ $card->title }}</strong></p>
             
            @elseif ($type === 'swap_request')
                <p>You have a new task swap request for task: <strong>{{ $card->title }}</strong></p>
            @elseif ($type === 'swap_accepted')
                <p>Your task swap request has been accepted for task: <strong>{{ $card->title }}</strong></p>
            @elseif ($type === 'swap_rejected')
                <p>Your task swap request has been rejected for task: <strong>{{ $card->title }}</strong></p>
            @elseif ($type === 'overdue')
                <p>Your task is overdue: <strong>{{ $card->title }}</strong></p>
            @endif
        </div>
        <a href="{{ url('/dashboard') }}" class="button"style="color: white;">View Task</a>
        <!-- Footer -->
        <p class="footer">Thank you for using our application! <br> © {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>
