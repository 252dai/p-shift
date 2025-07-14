<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}"> -->
    <title>管理者ダッシュボード</title>
</head>
<body>
    <h1>管理者ダッシュボード</h1>
    <p>ようこそ、{{ Auth::user()->name }} さん</p>
    <p>会社ID：{{ Auth::user()->company_id ?? '未設定' }}</p>

    <nav>
        <ul>
            <li><a href="{{ route('admin.calendar.shift') }}">希望シフト一覧（カレンダー形式）</a></li>
            <li><a href="{{ route('admin.fixed.index') }}">確定シフトの編集・削除</a></li>
            <li><a href="{{ route('admin.users') }}">ユーザー一覧・削除</a></li>
            <li><a href="{{ route('chat.index') }}">チャットを利用する</a></li>
            <li><a href="{{ route('admin.salary.index') }}">給料一覧を見る</a></li>
            <li><a href="{{ route('admin.users.search') }}">従業員招待</a></li>


        </ul>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</body>
</html>
