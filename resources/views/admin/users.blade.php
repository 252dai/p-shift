<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
   <link rel="stylesheet" href="{{ asset('css/admin_users.css') }}">
    <title>ユーザー一覧</title>
</head>
<body>
    <h1>会社内ユーザー一覧</h1>

    @if (session('message'))
        <p style="color:green">{{ session('message') }}</p>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>名前</th>
                <th>メール</th>
                <th>会社ID</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->company_id }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 戻る</a></p>
</body>
</html>
