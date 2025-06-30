<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" href="{{ asset('css/fixed_shift_calendar.css') }}"> -->
    <title>確定シフトカレンダー</title>
</head>
<body>
    @php
        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();
        $daysInMonth = $startDate->daysInMonth;
        $year = $startDate->year;
        $month = $startDate->month;
    @endphp

    <div>
        <a href="{{ route(Route::currentRouteName(), ['ym' => $prevMonth->format('Y-m')]) }}">← 前月</a>
        <span>{{ $year }}年 {{ $month }}月</span>
        <a href="{{ route(Route::currentRouteName(), ['ym' => $nextMonth->format('Y-m')]) }}">翌月 →</a>
    </div>
    <h1>{{ $year }}年{{ $month }}月 確定シフト一覧</h1>

    <table border="1" >
        <thead>
            <tr>
                <th>日付</th>
                <th>シフト者と時間帯</th>
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
                        @if (isset($shiftsByDate[$date]))
                            @foreach ($shiftsByDate[$date] as $shift)
                                ・{{ $shift->user->name }}：{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}〜{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}

                                <form method="GET" action="{{ route('admin.fixed.edit', $shift->id) }}" >
                                    <button type="submit">編集</button>
                                </form>

                                <form method="POST" action="{{ route('admin.fixed.delete', $shift->id) }}"  onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">削除</button>
                                </form>
                                <br>
                            @endforeach
                        @else
                            なし
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
