{{-- resources/views/admin/fixed_edit.blade.php --}}
<form method="POST" action="{{ route('admin.fixed.update', $shift->id) }}">
    @csrf
    @method('PUT')

    <p>{{ $shift->user->name }} さんのシフト編集</p>
    <label>日付: {{ $shift->date }}</label><br>
    <label>開始時間</label>
    <input type="time" name="start_time" value="{{ old('start_time', $shift->start_time) }}" required><br>
    <label>終了時間</label>
    <input type="time" name="end_time" value="{{ old('end_time', $shift->end_time) }}" required><br>

    <button type="submit">更新</button>
</form>
<a href="{{ route('admin.fixed.index') }}">戻る</a>
