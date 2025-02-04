<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $type === 'new_task' ? 'New Task Assigned' : ($type === 'overdue' ? 'Task Overdue Alert' : 'Task Swap Notification') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #fff; border-radius: 5px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #ddd; }
        .header img { max-width: 120px; height: auto; }
        .content { padding: 20px 0; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header dengan Logo -->
        <div class="header">
<img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app\public\img\logo4.png'))) }}" alt="Project Logo" style="max-width: 120px; height: auto;">
        </div>

        <!-- Kandungan E-mel -->
        <div class="content">
            <h2>Hello, {{ $user->name }}</h2>

            @if ($type === 'new_task')
                <p>You have been assigned a new task: <strong>{{ $card->title }}</strong></p>
                <a href="{{ url('/dashboard') }}" class="button">View Task</a>
            @elseif ($type === 'swap_accepted')
                <p>Your task swap request has been accepted for task: <strong>{{ $card->title }}</strong></p>
            @elseif ($type === 'swap_rejected')
                <p>Your task swap request has been rejected for task: <strong>{{ $card->title }}</strong></p>
            @elseif ($type === 'overdue')
                <p>Your task is overdue: <strong>{{ $card->title }}</strong></p>
                <a href="{{ url('/dashboard') }}" class="button">View Task</a>
            @endif
        </div>

        <!-- Footer -->
        <p class="footer">Thank you for using our application!</p>
    </div>
</body>
</html>
