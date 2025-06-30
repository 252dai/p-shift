<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会社ID設定</title>
</head>
<body>
    <h1>会社IDを設定</h1>
    
    <form method="POST" action="{{ route('admin.company.update') }}">
        @csrf
        <label for="company_id">会社ID：</label>
        <input type="text" name="company_id" id="company_id" required value="{{ old('company_id', Auth::user()->company_id) }}">
        <button type="submit">更新</button>
    </form>

    <p><a href="{{ route('admin.dashboard') }}">← 戻る</a></p>
</body>
</html>
