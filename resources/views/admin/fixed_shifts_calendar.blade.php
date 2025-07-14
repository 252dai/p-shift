<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確定シフトカレンダー</title>
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
            table-layout: fixed;
        }
        th, td {
            width: 14.2%;
            height: 140px;
            vertical-align: top;
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 12px;
            word-wrap: break-word;
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
            flex-wrap: wrap;
        }
        .shift-time {
            white-space: nowrap;
        }
        form {
            display: inline-block;
            margin: 0;
        }
        button {
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .edit-button {
            background-color: #2196F3;
            color: white;
        }
        .edit-button:hover {
            background-color: #0b7dda;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
        }
        .delete-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();
        $year = $startDate->year;
        $month = $startDate->month;
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0:日曜日
        $daysInMonth = $startDate->daysInMonth;
        $day = 1;
        $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
    @endphp

    <div class="calendar-nav">
        <a href="{{ route(Route::currentRouteName(), ['ym' => $prevMonth->format('Y-m')]) }}">← 前月</a>
        <span>{{ $year }}年 {{ $month }}月</span>
        <a href="{{ route(Route::currentRouteName(), ['ym' => $nextMonth->format('Y-m')]) }}">翌月 →</a>
    </div>

    <h2 style="text-align:center;">{{ $year }}年{{ $month }}月 確定シフト一覧</h2>

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
            @for ($week = 0; $week < $weeks; $week++)
                <tr>
                    @for ($i = 0; $i < 7; $i++)
                        <td>
                            @if (($week === 0 && $i < $startDayOfWeek) || $day > $daysInMonth)
                                {{-- 空セル --}}
                            @else
                                @php
                                    $ymd = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                @endphp
                                <div class="date">{{ $day }}日</div>

                                @if (isset($shiftsByDate[$ymd]))
                                    @foreach ($shiftsByDate[$ymd] as $shift)
                                        <div class="shift-entry">
                                            <div>
                                                ・{{ $shift->user->name }}：
                                                <span class="shift-time">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}〜{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                                            </div>

                                            <form method="GET" action="{{ route('admin.fixed.edit', $shift->id) }}">
                                                <button type="submit" class="edit-button">編集</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.fixed.delete', $shift->id) }}" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-button">削除</button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="font-size: 11px; color: #999;">なし</span>
                                @endif
                                @php $day++; @endphp
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <p style="text-align:center;"><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
