<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>
<body>
    <div id="bg-wrapper">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <h1>ログイン</h1>

            @if (session('status'))
                <div class="flash-message">
                    {{ session('status') }}
                </div>
            @endif

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember">
                    ログイン状態を保持する
                </label>
            </div>

            <div class="form-group">
                <button type="submit">ログイン</button>
            </div>
        </form>
    </div>
</body>
</html>
