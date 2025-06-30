<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会社参加申請 承認</title>
</head>
<body>
    <h1>会社参加申請 承認</h1>

    @if (session('message'))
        <p style="color: green">{{ session('message') }}</p>
    @endif

    @forelse ($requests as $req)
        <div>
            <p>申請者: {{ $req->user->name }}（{{ $req->user->email }}）</p>
            <form method="POST" action="{{ route('admin.company.requests.approve', $req->id) }}" style="display:inline">
                @csrf
                <button type="submit">承認</button>
            </form>
            <form method="POST" action="{{ route('admin.company.requests.reject', $req->id) }}" style="display:inline">
                @csrf
                <button type="submit">拒否</button>
            </form>
        </div>
        <hr>
    @empty
        <p>申請はありません</p>
    @endforelse

    <p><a href="{{ route('admin.dashboard') }}">← 管理者ダッシュボードへ</a></p>
</body>
</html>

