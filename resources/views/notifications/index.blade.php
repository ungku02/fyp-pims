@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Notifications</h1>
    <ul>
        @foreach ($notifications as $notification)
            <li class="{{ $notification->read ? '' : 'font-weight-bold' }}">
                {{ json_decode($notification->data)->message }}
                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Mark as read</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection
