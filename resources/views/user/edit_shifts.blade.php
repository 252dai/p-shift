<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>シフト編集・削除</title>
</head>
<body>

    @php
    $prevMonth = $date->copy()->subMonth();
    $nextMonth = $date->copy()->addMonth();
    @endphp

    <a href="{{ route('user.shifts.edit', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}">← 前月</a>
    <a href="{{ route('user.shifts.edit', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月 →</a>
    <h1>今月の希望シフト 編集・削除</h1>

    @if (session('message'))
        <p style="color: green">{{ session('message') }}</p>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>日付</th>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shifts as $shift)
                <tr>
                    <form method="POST" action="{{ route('user.shifts.update', $shift->id) }}">
                        @csrf
                        <td>{{ $shift->date }}</td>
                        <td><input type="time" name="start_time" value="{{ $shift->start_time }}"></td>
                        <td><input type="time" name="end_time" value="{{ $shift->end_time }}"></td>
                        <td>
                            <button type="submit">更新</button>
                        </form>
                            <form method="POST" action="{{ route('user.shifts.delete', $shift->id) }}" style="display:inline" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        </td>
                </tr>
            @empty
                <tr><td colspan="4">今月のシフトはまだ提出されていません</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><a href="{{ route('user.dashboard') }}">← マイページに戻る</a></p>
</body>
</html>
