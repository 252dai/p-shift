<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    
    <title>ユーザーダッシュボード</title>
    <link rel="stylesheet" href="{{ asset('css/user_dashboard.css') }}"> <!-- 必要ならCSS作成 -->
    <script src="{{ asset('js/home.js') }}"></script>
</head>
<body>
    <h1>ようこそ、{{ Auth::user()->name }} さん</h1>
    <p>会社ID：{{ Auth::user()->company_id ?? '未所属' }}</p>
    
    <main>
        <div class="container">
         <nav>
         <ul>
            <li><a href="{{ route('calendar.shift.create') }}">シフトをカレンダーで一括提出する</a></li>
            <li><a href="{{ route('user.shifts.edit') }}">提出済み希望シフトの編集・削除</a></li>
            <li><a href="{{ route('fixed.user.view') }}">確定シフトを確認する</a></li>
            <li><a href="{{ route('user.salary.index') }}">給料確認</a></li>
            <li><a href="{{ route('chat.index') }}">チャットを利用する</a></li>
        </ul>
    </nav>
    </div>
</main>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</body>
</html>