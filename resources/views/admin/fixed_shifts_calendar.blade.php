<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確定シフトカレンダー</title>
    <link rel="stylesheet" href="{{ asset('css/admin_fixed_shift.css') }}">
</head>
<body>
@php
    use Carbon\Carbon;
    $prevMonth = $startDate->copy()->subMonth();
    $nextMonth = $startDate->copy()->addMonth();
    $year = $startDate->year;
    $month = $startDate->month;
    $firstDayOfMonth = Carbon::create($year, $month, 1);
    $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Sunday
    $daysInMonth = $startDate->daysInMonth;
    $day = 1;
    $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
@endphp

<div class="calendar-nav">
    <a href="{{ route(Route::currentRouteName(), ['ym' => $prevMonth->format('Y-m')]) }}">← 前月</a>
    <span>{{ $year }}年 {{ $month }}月</span>
    <a href="{{ route(Route::currentRouteName(), ['ym' => $nextMonth->format('Y-m')]) }}">翌月 →</a>
</div>

<h2 style="text-align:center;">{{ $year }}年{{ $month }}月 確定シフトカレンダー</h2>

<table>
    <thead>
        <tr>
            <th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th>
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
                                        ・{{ $shift->user->name }}：{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}〜{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
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
