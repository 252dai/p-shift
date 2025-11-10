<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}"> <!-- 必要ならCSS作成 -->
</head>
<body>
    <h1>新規登録</h1>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- 名前 -->
        <div class="form-group">
            <label for="name">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        </div>

        <!-- メールアドレス -->
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        </div>

        <!-- パスワード -->
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
        </div>

        <!-- パスワード確認 -->
        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <!-- ユーザー区分 -->
        <div class="form-group">
            <label for="role">ユーザー区分</label>
            <select name="role" id="role" required>
                <option value="user">アルバイト</option>
                <option value="admin">社員</option>
            </select>
        </div>

        <!-- すでに登録済みリンク -->
        <div class="form-group">
            <a href="{{ route('login') }}">すでに登録済みですか？</a>
        </div>

        <!-- 登録ボタン -->
        <div class="form-group">
            <button type="submit">登録</button>
        </div>
    </form>
</body>
</html>
