<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>希望シフトカレンダー</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .calendar-nav {
            text-align: center;
            margin-bottom: 20px;
        }
        .calendar-nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            width: 14.2%;
            height: 140px;
            vertical-align: top;
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .date {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .shift-entry {
            margin-bottom: 6px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .shift-time {
            white-space: nowrap;
        }
        .confirm-button {
            padding: 2px 6px;
            font-size: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
        }
        .confirm-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();
        $year = $date->year;
        $month = $date->month;
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0:日, 1:月,...
        $daysInMonth = $date->daysInMonth;
    @endphp

    <div class="calendar-nav">
        <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
        <span>{{ $year }}年 {{ $month }}月</span>
        <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月 →</a>
    </div>

    <h2 style="text-align:center">{{ $year }}年{{ $month }}月 希望シフト一覧</h2>

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
                                {{-- 空白セル --}}
                            @else
                                @php
                                    $ymd = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                @endphp
                                <div class="date">{{ $day }}</div>

                                @if (isset($shifts[$ymd]))
                                    @foreach ($shifts[$ymd] as $shift)
                                        <div class="shift-entry">
                                            <div>
                                                ・{{ $shift->user->name }}:
                                                <span class="shift-time">{{ $shift->start_time }}〜{{ $shift->end_time }}</span>
                                            </div>
                                            <form method="POST" action="{{ route('admin.calendar.fix') }}">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $shift->user->id }}">
                                                <input type="hidden" name="date" value="{{ $ymd }}">
                                                <input type="hidden" name="start_time" value="{{ $shift->start_time }}">
                                                <input type="hidden" name="end_time" value="{{ $shift->end_time }}">
                                                <button type="submit" class="confirm-button">確定</button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="font-size: 11px; color: #999;">希望なし</span>
                                @endif

                                @php $day++; @endphp
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <p style="text-align: center;"><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
