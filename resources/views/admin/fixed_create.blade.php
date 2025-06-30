<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確定シフト作成</title>
    
</head>
<body>
    <h1>確定シフト登録</h1>

    <form method="POST" action="{{ route('fixed.store') }}">
        @csrf
        <label>ユーザー選択：</label>
        <select name="user_id">
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select><br>

        <label>日付：</label><input type="date" name="date" required><br>
        <label>開始：</label><input type="time" name="start_time" required><br>
        <label>終了：</label><input type="time" name="end_time" required><br>

        <button type="submit">確定</button>
    </form>

    <p><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
