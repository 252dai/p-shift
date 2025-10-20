<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カレンダーシフト提出</title>
    <link rel="stylesheet" href="{{ asset('css/user_cale_shift.css') }}">
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();
        $daysInMonth = $date->daysInMonth;
        $year = $date->year;
        $month = $date->month;
        $firstDay = Carbon::create($year, $month, 1);
        $startWeekday = $firstDay->dayOfWeek;
    @endphp

    <div class="container">
        <div class="calendar-nav">
            <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
            <span class="month-title">{{ $year }}年 {{ $month }}月</span>
            <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">次の月 →</a>
        </div>

        <form method="POST" action="{{ route('calendar.shift.store') }}" style="display: flex; flex-direction: column; height: 100%;">
            @csrf

            <div class="calendar-scroll">
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
                            $cellCount = 0;
                        @endphp

                        @while ($day <= $daysInMonth)
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @if ($cellCount < $startWeekday || $day > $daysInMonth)
                                        <td></td>
                                    @else
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                        @endphp
                                        <td>
                                            <div>{{ $day }}</div>
                                            <input type="time" name="shifts[{{ $dateStr }}][start_time]">
                                            <input type="time" name="shifts[{{ $dateStr }}][end_time]">
                                            @php $day++; @endphp
                                        </td>
                                    @endif
                                    @php $cellCount++; @endphp
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>
            </div>

            <div class="button-row">
                <a href="{{ route('user.dashboard') }}" class="back-link">← マイページに戻る</a>
                <button type="submit" class="submit-button">まとめて提出</button>
            </div>
        </form>
    </div>
</body>
</html>