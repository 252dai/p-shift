<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確定シフト</title>
    <link rel="stylesheet" href="{{ asset('css/user_fixed_shift.css') }}">
</head>
<body>
    <h1>あなたの確定シフト</h1>

    @if ($shifts->isEmpty())
        <p>まだ確定されたシフトはありません。</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>日付</th><th>開始</th><th>終了</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shifts as $shift)
                    <tr>
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->start_time }}</td>
                        <td>{{ $shift->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="{{ route('user.dashboard') }}">← マイページに戻る</a></p>
</body>
</html>
