<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $year }}年{{ $month }}月 給料一覧</title>
    <link rel="stylesheet" href="{{ asset('css/admin_salary.css') }}">
</head>
<body>
    <h1>{{ $year }}年{{ $month }}月 給料一覧</h1>

    @if(session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>名前</th>
                <th>時給</th>
                <th>勤務時間（合計）</th>
                <th>残業時間</th>
                <th>深夜時間</th>
                <th>給料（円）</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salaryData as $data)
                <tr>
                    <td>{{ $data['user']->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.salary.updateHourlyWage', $data['user']->id) }}">
                            @csrf
                            <input type="number" name="hourly_wage" value="{{ $data['user']->hourly_wage }}" min="0" style="width:80px;">
                            <p>円</p>
                            <button type="submit">更新</button>
                        </form>
                    </td>
                    <td>{{ $data['regular_hours'] }}時間</td>
                    <td>{{ $data['overtime_hours'] }}時間</td>
                    <td>{{ $data['night_hours'] }}時間</td>
                    <td>{{ number_format($data['salary']) }}円</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align:right"><strong>合計給料</strong></td>
                <td><strong>{{ number_format($totalSalary) }}円</strong></td>
            </tr>
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 戻る</a></p>
</body>
</html>
