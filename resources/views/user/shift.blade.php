<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    
    <title>シフト提出</title>
</head>
<body>
    <h1>シフト提出フォーム</h1>

    <form method="POST" action="{{ route('shift.store') }}">
        @csrf
        <label for="date">日付：</label>
        <input type="date" name="date" id="date" required><br>

        <label for="start_time">開始時間：</label>
        <input type="time" name="start_time" id="start_time" required><br>

        <label for="end_time">終了時間：</label>
        <input type="time" name="end_time" id="end_time" required><br>

        <button type="submit">提出</button>
    </form>

    <p><a href="{{ route('user.dashboard') }}">← 戻る</a></p>
</body>
</html>
