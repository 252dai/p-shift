<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $year }}年{{ $month }}月 給料</title>
    <link rel="stylesheet" href="{{ asset('css/salary.css') }}">
</head>
<body>
    <h1>{{ $year }}年{{ $month }}月 給料</h1>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>勤務時間（合計）</th>
            <th>時給</th>
            <th>残業時間</th>
            <th>深夜時間</th>
            <th>給料（円）</th>
        </tr>
        <tr>
            <td>{{ $regular_hours }}時間</td>
            <td>{{ number_format($user->hourly_wage) }}円</td>
            <td>{{ $overtime_hours }}時間</td>
            <td>{{ $night_hours }}時間</td>
            <td>{{ number_format($salary) }}円</td>
        </tr>
    </table>

    <p><a href="{{ route('user.dashboard') }}">← 戻る</a></p>
</body>
</html>
