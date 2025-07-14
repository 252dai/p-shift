<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    
    <title>ユーザーダッシュボード</title>
</head>
<body>
    </h1>ユーザーダッシュボード</h1>
    <p>ようこそ、{{ Auth::user()->name }} さん</p>
    <p>会社ID：{{ Auth::user()->company_id ?? '未所属' }}</p>

    <nav>
        <ul>
            <li><a href="{{ route('calendar.shift.create') }}">シフトをカレンダーで一括提出する</a></li>
            <li><a href="{{ route('user.shifts.edit') }}">提出済み希望シフトの編集・削除</a></li>
            <li><a href="{{ route('fixed.user.view') }}">確定シフトを確認する</a></li>
            <li><a href="{{ route('chat.index') }}">チャットを利用する</a></li>
        </ul>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</body>
</html>
