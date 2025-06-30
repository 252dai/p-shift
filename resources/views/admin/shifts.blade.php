<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>提出されたシフト一覧</title>
</head>
<body>
    <h1>提出されたシフト一覧</h1>

    @if ($shifts->isEmpty())
        <p>シフトはまだ提出されていません。</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>ユーザー名</th>
                    <th>日付</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shifts as $shift)
                    <tr>
                        <td>{{ $shift->user->name }}</td>
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->start_time }}</td>
                        <td>{{ $shift->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="{{ route('admin.dashboard') }}">← 戻る</a></p>
</body>
</html>
