<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" href="{{ asset('css/request_shift_calendar.css') }}"> -->
    <title>希望シフトカレンダー</title>
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
    <h1>{{ $year }}年{{ $month }}月 希望シフト一覧</h1>

    <table border="1">
        <thead>
            <tr>
                <th>日付</th>
                <th>希望者と時間帯</th>
            </tr>
        </thead>
        <tbody>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                @endphp
                <tr>
                    <td>{{ $date }}</td>
                    <td>
    @if (isset($shifts[$date]))
        @foreach ($shifts[$date] as $shift)
            ・{{ $shift->user->name }}：{{ $shift->start_time }}〜{{ $shift->end_time }}

            <form method="POST" action="{{ route('admin.calendar.fix') }}" style="display:inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $shift->user->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="start_time" value="{{ $shift->start_time }}">
                <input type="hidden" name="end_time" value="{{ $shift->end_time }}">
                <button type="submit">確定</button>
            </form>

            <br>
        @endforeach
    @else
        希望なし
    @endif
</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
