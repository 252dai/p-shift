<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チャット</title>
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
</head>
<body>
    <h1>会社内チャット</h1>

    <form method="POST" action="{{ route('chat.store') }}">
        @csrf
        <textarea name="message" rows="3" cols="50" placeholder="メッセージを入力" required></textarea><br>
        <button type="submit">送信</button>
    </form>

    <hr>

    @foreach ($chats as $chat)
        <p><strong>{{ $chat->user->name }}</strong>（{{ $chat->created_at->format('Y-m-d H:i') }}）</p>
        <p>{{ $chat->message }}</p>
        <hr>
    @endforeach

    <p><a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">← 戻る</a></p>
</body>
</html>
