<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確定シフト一覧（編集・削除）</title>
</head>
<body>
    @php
    $current = \Carbon\Carbon::parse($yearMonth . '-01');
    $prev = $current->copy()->subMonth()->format('Y-m');
    $next = $current->copy()->addMonth()->format('Y-m');
    @endphp

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.fixed.index', ['ym' => $prev]) }}">← 前の月</a>
        <span style="margin: 0 15px;">{{ $current->format('Y年m月') }}</span>
        <a href="{{ route('admin.fixed.index', ['ym' => $next]) }}">次の月 →</a>
    </div>
    <h1>今月の確定シフト 編集・削除</h1>

    @if (session('message'))
        <p style="color: green">{{ session('message') }}</p>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>日付</th>
                <th>名前</th>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shifts as $shift)
                <tr>
                    <form method="POST" action="{{ route('admin.fixed.update', $shift->id) }}">
                        @csrf
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->user->name }}</td>
                        <td><input type="time" name="start_time" value="{{ $shift->start_time }}"></td>
                        <td><input type="time" name="end_time" value="{{ $shift->end_time }}"></td>
                        <td>
                            <button type="submit">更新</button>
                    </form>
                            <form method="POST" action="{{ route('admin.fixed.delete', $shift->id) }}" style="display:inline" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        </td>
                </tr>
            @empty
                <tr><td colspan="5">今月の確定シフトはまだありません</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><a href="{{ route('admin.dashboard') }}">← 管理者マイページに戻る</a></p>
</body>
</html>
