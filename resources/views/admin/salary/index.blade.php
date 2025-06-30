<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $year }}年{{ $month }}月 給料一覧</title>
</head>
<body>
    <h1>{{ $year }}年{{ $month }}月 給料一覧</h1>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>名前</th>
                <th>時給</th>
                <th>勤務時間（合計）</th>
                <th>給料（円）</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salaryData as $data)
                <tr>
                    <td>{{ $data['user']->name }}</td>
                    <td>{{ number_format($data['user']->hourly_wage) }}円</td>
                    <td>{{ $data['total_hours'] }}時間</td>
                    <td>{{ number_format($data['salary']) }}円</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align:right"><strong>合計給料</strong></td>
                <td><strong>{{ number_format($totalSalary) }}円</strong></td>
            </tr>
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
