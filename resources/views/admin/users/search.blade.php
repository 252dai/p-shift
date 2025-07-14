<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー検索・招待</title>
</head>
<body>
    <h2>ユーザー検索・招待</h2>

    {{-- 成功・失敗メッセージ表示 --}}
    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    {{-- 検索フォーム --}}
    <form method="POST" action="{{ route('admin.users.search') }}">
        @csrf
        <input type="text" name="keyword" placeholder="名前かメールで検索" value="{{ old('keyword', $keyword ?? '') }}" required>
        <button type="submit">検索</button>
    </form>

    {{-- 検索結果表示 --}}
    @if(isset($users))
        <h3>検索結果（{{ count($users) }} 件）</h3>
        @if(count($users) > 0)
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>メール</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.invite', $user) }}">
                                @csrf
                                <button type="submit">招待する</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>該当するユーザーはいません。</p>
        @endif
    @endif

    <br>
    <a href="{{ route('admin.dashboard') }}">← 管理者ダッシュボードに戻る</a>
</body>
</html>
