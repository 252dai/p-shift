<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>希望シフト提出</title>
    <link rel="stylesheet" href="{{ asset('css/user_cale_shift.css') }}">
</head>
<body>
@php
    use Carbon\Carbon;
    $prevMonth = $date->copy()->subMonth();
    $nextMonth = $date->copy()->addMonth();
    $year = $date->year;
    $month = $date->month;
    $firstDayOfMonth = Carbon::create($year, $month, 1);
    $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0:日曜
    $daysInMonth = $date->daysInMonth;
@endphp

<div class="calendar-nav">
    <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
    <span>{{ $year }}年 {{ $month }}月</span>
    <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月 →</a>
</div>

<h2 style="text-align:center">{{ $year }}年{{ $month }}月 希望シフト入力</h2>

<form method="POST" action="{{ route('calendar.shift.store') }}">
    @csrf
    <table>
        <thead>
        <tr>
            <th>日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
        </tr>
        </thead>
        <tbody>
        @php
            $day = 1;
            $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
        @endphp

        @for ($week = 0; $week < $weeks; $week++)
            <tr>
                @for ($i = 0; $i < 7; $i++)
                    <td>
                        @if (($week === 0 && $i < $startDayOfWeek) || $day > $daysInMonth)
                            {{-- 空白 --}}
                        @else
                            @php
                                $ymd = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            @endphp
                            <div class="date">{{ $day }}</div>
                            <div class="shift-inputs">
                                <label>開始:</label>
                                <input type="time" name="shifts[{{ $ymd }}][start_time]">
                                <label>終了:</label>
                                <input type="time" name="shifts[{{ $ymd }}][end_time]">
                            </div>
                            @php $day++; @endphp
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
        </tbody>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <button type="submit" class="submit-button">まとめて提出</button>
    </div>
</form>

<p style="text-align: center;"><a href="{{ route('user.dashboard') }}">← マイページに戻る</a></p>
</body>
</html>
