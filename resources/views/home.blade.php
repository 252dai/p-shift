<!-- resources/views/home.blade.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>p-shift ホーム</title>
   
</head>
<body>
    <div class="container">
        <h1>ようこそ p-shift へ</h1>
        <p>ログインまたは新規登録して、シフト管理を始めましょう</p>
        <a href="{{ route('login') }}" class="button">ログインページへ</a>
        <a href="{{ route('register') }}" class="button">新規登録</a>
    </div>
</body>
</html>
