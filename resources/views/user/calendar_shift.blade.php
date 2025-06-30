<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カレンダーシフト提出</title>
</head>
<body>
    @php
    $prevMonth = $date->copy()->subMonth();
    $nextMonth = $date->copy()->addMonth();
    $daysInMonth = $date->daysInMonth;
    $year = $date->year;
    $month = $date->month;
    @endphp

    <div>
        <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
        <span>{{ $date->year }}年 {{ $date->month }}月</span>
        <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月 →</a>
    </div>


    <form method="POST" action="{{ route('calendar.shift.store') }}">
        @csrf
        <table border="1">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                </tr>
            </thead>
            <tbody>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td><input type="time" name="shifts[{{ $date }}][start_time]"></td>
                        <td><input type="time" name="shifts[{{ $date }}][end_time]"></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <button type="submit">まとめて提出</button>
    </form>

    <p><a href="{{ route('user.dashboard') }}">← マイページに戻る</a></p>
</body>
</html>
