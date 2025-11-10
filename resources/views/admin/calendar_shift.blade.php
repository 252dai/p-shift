<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>希望シフトカレンダー</title>
    <link rel="stylesheet" href="{{ asset('css/cale_shift.css') }}">
</head>
<body>
@php
    use Carbon\Carbon;
    $prevMonth = $date->copy()->subMonth();
    $nextMonth = $date->copy()->addMonth();
    $year = $date->year;
    $month = $date->month;
    $firstDayOfMonth = Carbon::create($year, $month, 1);
    $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
    $daysInMonth = $date->daysInMonth;
@endphp

<div class="calendar-nav">
    <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
    <span>{{ $year }}年 {{ $month }}月</span>
    <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月 →</a>
</div>

<h2>{{ $year }}年{{ $month }}月 希望シフト一覧</h2>

<table>
    <thead>
        <tr>
            <th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th>
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

                            @if(isset($shifts[$ymd]))
                                <button class="toggle-btn" onclick="toggleShift('{{ $ymd }}')">
                                    シフトを見る ({{ count($shifts[$ymd]) }}人)
                                </button>
                                <div id="shift-{{ $ymd }}" class="shift-container" style="display:none">
                                    @foreach($shifts[$ymd] as $shift)
                                        <div class="shift-entry">
                                            <div>・{{ $shift->user->name }}:
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
                                </div>
                            @else
                                <span>希望なし</span>
                            @endif

                            @php $day++; @endphp
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>

<p style="text-align: center;"><a href="{{ route('admin.dashboard') }}">← 戻る</a></p>

<script>
function toggleShift(id) {
    const el = document.getElementById('shift-' + id);
    if (el.style.display === 'block') {
        el.style.display = 'none';
    } else {
        el.style.display = 'block';
    }
}
</script>
</body>
</html>
