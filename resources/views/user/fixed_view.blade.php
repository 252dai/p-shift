<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確定シフト - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/user_fixed_shift.css') }}">
</head>
<body>
    @php
        use Carbon\Carbon;
        // 月ごとにグループ化
        $shiftsByMonth = $shifts->groupBy(function($shift) {
            return Carbon::parse($shift->date)->format('Y-m');
        });
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
            <p class="section-title">シフトサマリー</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">確定シフト数</span>
                    <span class="summary-value">{{ count($shifts) }}件</span>
                </div>
                @if(count($shifts) > 0)
                    @php
                        $totalHours = 0;
                        foreach($shifts as $shift) {
                            $start = Carbon::parse($shift->start_time);
                            $end = Carbon::parse($shift->end_time);
                            $totalHours += $end->diffInHours($start) + ($end->diffInMinutes($start) % 60) / 60;
                        }
                    @endphp
                    <div class="summary-item">
                        <span class="summary-label">総勤務時間</span>
                        <span class="summary-value">{{ number_format($totalHours, 1) }}h</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="legend-section">
            <p class="section-title">表示について</p>
            <div class="legend-card">
                <div class="legend-item">
                    <span class="legend-icon">📅</span>
                    <span class="legend-text">月ごとに表示されます</span>
                </div>
                <div class="legend-item">
                    <span class="legend-icon">⏰</span>
                    <span class="legend-text">開始〜終了時刻</span>
                </div>
                <div class="legend-item">
                    <span class="legend-icon">🔍</span>
                    <span class="legend-text">カードで詳細確認</span>
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
            <h1 class="page-title">確定シフト</h1>
            <p class="page-subtitle">管理者が確定したあなたのシフト一覧</p>
        </header>

        @if ($shifts->isEmpty())
            <!-- 空の状態 -->
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>確定シフトがありません</h3>
                <p>管理者がシフトを確定するまでお待ちください</p>
                <div class="empty-actions">
                    <a href="{{ route('calendar.shift.create') }}" class="btn-primary">
                        <span>📝</span>
                        <span>希望シフトを提出</span>
                    </a>
                </div>
            </div>
        @else
            <!-- 月ごとのシフト表示 -->
            @foreach($shiftsByMonth as $yearMonth => $monthShifts)
                @php
                    $date = Carbon::parse($yearMonth . '-01');
                    $year = $date->year;
                    $month = $date->month;
                @endphp
                
                <div class="month-section">
                    <div class="month-header">
                        <h2 class="month-title">{{ $year }}年 {{ $month }}月</h2>
                        <span class="month-count">{{ count($monthShifts) }}件</span>
                    </div>

                    <div class="shifts-grid">
                        @foreach($monthShifts as $shift)
                            @php
                                $shiftDate = Carbon::parse($shift->date);
                                $dayOfWeek = $shiftDate->isoFormat('ddd');
                                $isWeekend = $shiftDate->isWeekend();
                                
                                $startTime = Carbon::parse($shift->start_time);
                                $endTime = Carbon::parse($shift->end_time);
                                $duration = $endTime->diffInHours($startTime) + ($endTime->diffInMinutes($startTime) % 60) / 60;
                            @endphp
                            
                            <div class="shift-card {{ $isWeekend ? 'weekend' : '' }}">
                                <div class="shift-date-section">
                                    <div class="shift-day">{{ $shiftDate->day }}</div>
                                    <div class="shift-weekday {{ $isWeekend ? 'weekend-text' : '' }}">{{ $dayOfWeek }}</div>
                                </div>
                                <div class="shift-time-section">
                                    <div class="time-range">
                                        <span class="time-start">{{ $startTime->format('H:i') }}</span>
                                        <span class="time-separator">〜</span>
                                        <span class="time-end">{{ $endTime->format('H:i') }}</span>
                                    </div>
                                    <div class="duration-badge">
                                        <span class="duration-icon">⏱️</span>
                                        <span>{{ number_format($duration, 1) }}時間</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </main>

    <script>
        // シフトカードのホバーアニメーション
        document.querySelectorAll('.shift-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // 統計情報のアニメーション
        window.addEventListener('load', function() {
            const summaryValues = document.querySelectorAll('.summary-value');
            summaryValues.forEach((value, index) => {
                setTimeout(() => {
                    value.style.opacity = '0';
                    value.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        value.style.transition = 'all 0.5s ease';
                        value.style.opacity = '1';
                        value.style.transform = 'scale(1)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</body>
</html>