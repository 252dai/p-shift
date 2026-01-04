<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>シフト編集 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/user_edit_shift.css') }}">
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();
        $year = $date->year;
        $month = $date->month;
    @endphp

    <!-- サイドバー -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📅</span>
                <span class="logo-text">p-shift</span>
            </div>
            <div class="user-badge">従業員</div>
        </div>

        <div class="summary-info">
            <p class="section-title">提出状況</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">対象期間</span>
                    <span class="summary-value small">{{ $year }}年{{ $month }}月</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">提出済み</span>
                    <span class="summary-value">{{ count($shifts) }}件</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <p class="section-title">編集について</p>
            <div class="info-card">
                <div class="info-item">
                    <span class="info-icon">✏️</span>
                    <span class="info-text">時間を変更して更新ボタン</span>
                </div>
                <div class="info-item">
                    <span class="info-icon">🗑️</span>
                    <span class="info-text">削除ボタンで取り消し</span>
                </div>
                <div class="info-item">
                    <span class="info-icon">⚠️</span>
                    <span class="info-text">確定後は編集できません</span>
                </div>
            </div>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('user.dashboard') }}" class="back-btn">
                <span>← ダッシュボードへ</span>
            </a>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content">
        <header class="content-header">
            <h1 class="page-title">希望シフト編集</h1>
            <p class="page-subtitle">提出済みの希望シフトを編集・削除できます</p>
        </header>

        <!-- カレンダーナビゲーション -->
        <div class="calendar-nav">
            <a href="{{ route(Route::currentRouteName(), ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="nav-btn">
                ← 前月
            </a>
            <span class="current-month">{{ $year }}年 {{ $month }}月</span>
            <a href="{{ route(Route::currentRouteName(), ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="nav-btn">
                翌月 →
            </a>
        </div>

        <!-- 成功メッセージ -->
        @if (session('message'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if ($shifts->isEmpty())
            <!-- 空の状態 -->
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>この月の希望シフトはありません</h3>
                <p>シフトを提出すると、ここで編集・削除ができます</p>
                <div class="empty-actions">
                    <a href="{{ route('calendar.shift.create') }}" class="btn-primary">
                        <span>📝</span>
                        <span>シフトを提出</span>
                    </a>
                </div>
            </div>
        @else
            <!-- シフトリスト -->
            <div class="shifts-container">
                @foreach ($shifts as $shift)
                    @php
                        $shiftDate = Carbon::parse($shift->date);
                        $dayOfWeek = $shiftDate->isoFormat('ddd');
                        $isWeekend = $shiftDate->isWeekend();
                    @endphp
                    
                    <div class="shift-item {{ $isWeekend ? 'weekend' : '' }}">
                        <div class="shift-date">
                            <div class="date-number">{{ $shiftDate->day }}</div>
                            <div class="date-info">
                                <span class="date-weekday {{ $isWeekend ? 'weekend-text' : '' }}">{{ $dayOfWeek }}</span>
                                <span class="date-full">{{ $shiftDate->format('Y/m/d') }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('user.shifts.update', $shift->id) }}" class="shift-form">
                            @csrf
                            <div class="shift-times">
                                <div class="time-input-group">
                                    <label class="time-label">開始</label>
                                    <input type="time" name="start_time" value="{{ $shift->start_time }}" class="time-input" required>
                                </div>
                                <span class="time-separator">〜</span>
                                <div class="time-input-group">
                                    <label class="time-label">終了</label>
                                    <input type="time" name="end_time" value="{{ $shift->end_time }}" class="time-input" required>
                                </div>
                            </div>

                            <div class="shift-actions">
                                <button type="submit" class="btn-update">
                                    <span>✏️</span>
                                    <span>更新</span>
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('user.shifts.delete', $shift->id) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">
                                <span>🗑️</span>
                                <span>削除</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <script>
        // 更新確認
        document.querySelectorAll('.shift-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const dateText = this.closest('.shift-item').querySelector('.date-full').textContent;
                const startTime = this.querySelector('input[name="start_time"]').value;
                const endTime = this.querySelector('input[name="end_time"]').value;
                
                if (!confirm(`${dateText} のシフトを更新しますか？\n\n時間: ${startTime} 〜 ${endTime}`)) {
                    e.preventDefault();
                }
            });
        });

        // 削除確認
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const dateText = this.closest('.shift-item').querySelector('.date-full').textContent;
                
                if (!confirm(`${dateText} のシフトを削除しますか？\n\nこの操作は取り消せません。`)) {
                    e.preventDefault();
                }
            });
        });

        // 時間入力のバリデーション
        document.querySelectorAll('.shift-form').forEach(form => {
            const startInput = form.querySelector('input[name="start_time"]');
            const endInput = form.querySelector('input[name="end_time"]');
            
            form.addEventListener('submit', function(e) {
                if (startInput.value >= endInput.value) {
                    e.preventDefault();
                    alert('終了時間は開始時間より後にしてください。');
                }
            });
        });

        // 成功メッセージの自動非表示
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300);
            }, 5000);
        }

        // シフトアイテムのアニメーション
        window.addEventListener('load', function() {
            const items = document.querySelectorAll('.shift-item');
            items.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.5s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 50);
            });
        });
    </script>
</body>
</html>