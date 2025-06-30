<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会社参加申請</title>
</head>
<body>
    <h1>会社参加申請</h1>

    @if (session('message'))
        <p style="color: green">{{ session('message') }}</p>
    @endif

    @if ($request && $request->status === 'pending')
        <p>申請中：{{ $request->target_company_id }}</p>
    @elseif ($request && $request->status === 'rejected')
        <p style="color: red;">申請が拒否されました</p>
    @else
        <form method="POST" action="{{ route('user.company.request.store') }}">
            @csrf
            <label>会社IDを入力してください：</label>
            <input type="text" name="company_id" required>
            <button type="submit">申請する</button>
        </form>
    @endif

    <p><a href="{{ route('user.dashboard') }}">← マイページへ戻る</a></p>
</body>
</html>

