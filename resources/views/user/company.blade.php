<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会社ID入力</title>
</head>
<body>
    <h1>会社に参加する</h1>

    <form method="POST" action="{{ route('user.company.join') }}">
        @csrf
        <label for="company_id">会社ID：</label>
        <input type="text" name="company_id" id="company_id" required>
        <button type="submit">参加</button>
    </form>

    <p><a href="{{ route('user.dashboard') }}">← 戻る</a></p>
</body>
</html>
