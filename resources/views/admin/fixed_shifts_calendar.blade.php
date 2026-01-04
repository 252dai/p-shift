<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確定シフト管理 - p-shift</title>
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
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
        $daysInMonth = $startDate->daysInMonth;
        $day = 1;
        $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
    @endphp

    <!-- サイドバー -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📅</span>
                <span class="logo-text">p-shift</span>
            </div>
            <div class="user-badge">管理者</div>
        </div>

        <div class="summary-info">
            <p class="section-title">確定状況</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">確定済み</span>
                    <span class="summary-value" id="confirmedCount">0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">未確定</span>
                    <span class="summary-value text-gray" id="notConfirmedCount">0</span>
                </div>
            </div>
        </div>

        <div class="actions-section">
            <p class="section-title">クイック操作</p>
            <button type="button" class="action-btn" onclick="expandAll()">
                <span>📖</span>
                <span>全て展開</span>
            </button>
            <button type="button" class="action-btn" onclick="collapseAll()">
                <span>📕</span>
                <span>全て閉じる</span>
            </button>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <span>← ダッシュボードへ</span>
            </a>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content">
        <header class="content-header">
            <h1 class="page-title">確定シフト管理</h1>
            <p class="page-subtitle">確定済みシフトの編集・削除ができます</p>
        </header>

        <!-- カレンダーナビゲーション -->
        <div class="calendar-nav">
            <a href="{{ route(Route::currentRouteName(), ['ym' => $prevMonth->format('Y-m')]) }}" class="nav-btn">
                ← 前月
            </a>
            <span class="current-month">{{ $year }}年 {{ $month }}月</span>
            <a href="{{ route(Route::currentRouteName(), ['ym' => $nextMonth->format('Y-m')]) }}" class="nav-btn">
                翌月 →
            </a>
        </div>

        <!-- カレンダー -->
        <div class="calendar-wrapper">
            <table class="calendar">
                <thead>
                    <tr>
                        <th class="day-header sunday">日</th>
                        <th class="day-header">月</th>
                        <th class="day-header">火</th>
                        <th class="day-header">水</th>
                        <th class="day-header">木</th>
                        <th class="day-header">金</th>
                        <th class="day-header saturday">土</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($week = 0; $week < $weeks; $week++)
                        <tr>
                            @for ($i = 0; $i < 7; $i++)
                                @php
                                    $isWeekend = ($i === 0 || $i === 6);
                                    $cellClass = $isWeekend ? ($i === 0 ? 'sunday' : 'saturday') : '';
                                @endphp
                                
                                @if (($week === 0 && $i < $startDayOfWeek) || $day > $daysInMonth)
                                    <td class="{{ $cellClass }}"></td>
                                @else
                                    @php
                                        $ymd = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                        $hasShifts = isset($shiftsByDate[$ymd]) && count($shiftsByDate[$ymd]) > 0;
                                    @endphp
                                    <td class="{{ $cellClass }} {{ $hasShifts ? 'has-shifts' : 'no-shifts' }}" data-has-shifts="{{ $hasShifts ? '1' : '0' }}">
                                        <div class="day-cell">
                                            <div class="day-number">{{ $day }}</div>
                                            
                                            @if($hasShifts)
                                                <div class="shift-list-wrapper">
                                                    @if(count($shiftsByDate[$ymd]) > 1)
                                                        <div class="shift-count-badge">{{ count($shiftsByDate[$ymd]) }}人</div>
                                                    @endif
                                                    <div class="shift-slider" id="slider-{{ $ymd }}">
                                                        @foreach($shiftsByDate[$ymd] as $index => $shift)
                                                            <div class="shift-card {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                                                <div class="shift-info">
                                                                    <span class="employee-name">{{ $shift->user->name }}</span>
                                                                    <span class="shift-time">
                                                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} 〜 
                                                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                                                    </span>
                                                                </div>
                                                                <div class="shift-actions">
                                                                    <form method="GET" action="{{ route('admin.fixed.edit', $shift->id) }}" class="action-form">
                                                                        <button type="submit" class="edit-button">
                                                                            <span>✏️</span>
                                                                            <span>編集</span>
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST" action="{{ route('admin.fixed.delete', $shift->id) }}" class="action-form delete-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="delete-button">
                                                                            <span>🗑️</span>
                                                                            <span>削除</span>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @if(count($shiftsByDate[$ymd]) > 1)
                                                        <div class="slider-controls">
                                                            <button type="button" class="slider-btn prev" onclick="prevShift('{{ $ymd }}')">‹</button>
                                                            <span class="slider-indicator">
                                                                <span class="current-slide" id="current-{{ $ymd }}">1</span> / {{ count($shiftsByDate[$ymd]) }}
                                                            </span>
                                                            <button type="button" class="slider-btn next" onclick="nextShift('{{ $ymd }}')">›</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="no-shift-text">確定なし</span>
                                            @endif
                                        </div>
                                        @php $day++; @endphp
                                    </td>
                                @endif
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // スライダーの現在位置を管理
        const sliderPositions = {};

        // 次のシフトを表示
        function nextShift(date) {
            const slider = document.getElementById('slider-' + date);
            const cards = slider.querySelectorAll('.shift-card');
            const total = cards.length;
            
            if (!sliderPositions[date]) sliderPositions[date] = 0;
            
            cards[sliderPositions[date]].classList.remove('active');
            sliderPositions[date] = (sliderPositions[date] + 1) % total;
            cards[sliderPositions[date]].classList.add('active');
            
            updateIndicator(date);
        }

        // 前のシフトを表示
        function prevShift(date) {
            const slider = document.getElementById('slider-' + date);
            const cards = slider.querySelectorAll('.shift-card');
            const total = cards.length;
            
            if (!sliderPositions[date]) sliderPositions[date] = 0;
            
            cards[sliderPositions[date]].classList.remove('active');
            sliderPositions[date] = (sliderPositions[date] - 1 + total) % total;
            cards[sliderPositions[date]].classList.add('active');
            
            updateIndicator(date);
        }

        // インジケーターを更新
        function updateIndicator(date) {
            const current = sliderPositions[date] + 1;
            const indicator = document.getElementById('current-' + date);
            if (indicator) {
                indicator.textContent = current;
            }
        }

        // 全て展開（未実装 - 現在はスライダー形式のため不要）
        function expandAll() {
            alert('スライダー形式では展開機能は不要です');
        }

        // 全て閉じる（未実装 - 現在はスライダー形式のため不要）
        function collapseAll() {
            alert('スライダー形式では閉じる機能は不要です');
        }

        // 確定状況の集計
        function updateSummary() {
            const totalDays = document.querySelectorAll('.calendar td[data-has-shifts]').length;
            const withShifts = document.querySelectorAll('.calendar td[data-has-shifts="1"]').length;
            const noShifts = totalDays - withShifts;
            
            document.getElementById('confirmedCount').textContent = withShifts;
            document.getElementById('notConfirmedCount').textContent = noShifts;
        }

        // 削除ボタンのクリック時に確認
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const name = this.closest('.shift-card').querySelector('.employee-name').textContent;
                const time = this.closest('.shift-card').querySelector('.shift-time').textContent;
                
                if (!confirm(`${name} さんのシフトを削除しますか？\n\n時間: ${time}`)) {
                    e.preventDefault();
                }
            });
        });

        // ページ読み込み時
        window.addEventListener('load', function() {
            updateSummary();
            
            // 全てのスライダーを初期化
            document.querySelectorAll('.shift-slider').forEach(slider => {
                const date = slider.id.replace('slider-', '');
                sliderPositions[date] = 0;
            });
        });
    </script>
</body>
</html>