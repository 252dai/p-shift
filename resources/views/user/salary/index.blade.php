<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>給料確認 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/salary.css') }}">
</head>
<body>
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
            <p class="section-title">給料サマリー</p>
            <div class="summary-card highlight">
                <div class="summary-label">今月の給料</div>
                <div class="summary-amount">¥{{ number_format($salary) }}</div>
            </div>
        </div>

        <div class="legend-section">
            <p class="section-title">計算内訳</p>
            <div class="legend-card">
                <div class="legend-item">
                    <span class="legend-dot regular"></span>
                    <span class="legend-text">通常勤務：時給×時間</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot overtime"></span>
                    <span class="legend-text">残業：時給×1.25</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot night"></span>
                    <span class="legend-text">深夜：時給×1.25</span>
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
            <h1 class="page-title">給料確認</h1>
            <p class="page-subtitle">{{ $year }}年{{ $month }}月の給料明細</p>
        </header>

        <!-- 給料カード -->
        <div class="salary-card">
            <div class="salary-header">
                <div class="period-info">
                    <span class="period-icon">📅</span>
                    <span class="period-text">{{ $year }}年{{ $month }}月</span>
                </div>
                <div class="total-salary">
                    <span class="salary-label">支給額</span>
                    <span class="salary-amount">¥{{ number_format($salary) }}</span>
                </div>
            </div>

            <div class="salary-details">
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-icon">⏰</div>
                        <div class="detail-content">
                            <span class="detail-label">通常勤務時間</span>
                            <span class="detail-value">{{ $regular_hours }}時間</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">💰</div>
                        <div class="detail-content">
                            <span class="detail-label">時給</span>
                            <span class="detail-value">¥{{ number_format($user->hourly_wage) }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-icon overtime-icon">⏱️</div>
                        <div class="detail-content">
                            <span class="detail-label">残業時間</span>
                            <span class="detail-value overtime-value">{{ $overtime_hours }}時間</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon night-icon">🌙</div>
                        <div class="detail-content">
                            <span class="detail-label">深夜時間</span>
                            <span class="detail-value night-value">{{ $night_hours }}時間</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="salary-breakdown">
                <h3 class="breakdown-title">詳細内訳</h3>
                <div class="breakdown-list">
                    <div class="breakdown-item">
                        <span class="breakdown-label">
                            <span class="breakdown-dot regular"></span>
                            通常勤務 ({{ $regular_hours }}h × ¥{{ number_format($user->hourly_wage) }})
                        </span>
                        <span class="breakdown-value">¥{{ number_format($regular_hours * $user->hourly_wage) }}</span>
                    </div>

                    @if($overtime_hours > 0)
                        <div class="breakdown-item">
                            <span class="breakdown-label">
                                <span class="breakdown-dot overtime"></span>
                                残業手当 ({{ $overtime_hours }}h × ¥{{ number_format($user->hourly_wage * 1.25) }})
                            </span>
                            <span class="breakdown-value">¥{{ number_format($overtime_hours * $user->hourly_wage * 1.25) }}</span>
                        </div>
                    @endif

                    @if($night_hours > 0)
                        <div class="breakdown-item">
                            <span class="breakdown-label">
                                <span class="breakdown-dot night"></span>
                                深夜手当 ({{ $night_hours }}h × ¥{{ number_format($user->hourly_wage * 1.25) }})
                            </span>
                            <span class="breakdown-value">¥{{ number_format($night_hours * $user->hourly_wage * 1.25) }}</span>
                        </div>
                    @endif

                    <div class="breakdown-item total">
                        <span class="breakdown-label">合計支給額</span>
                        <span class="breakdown-value">¥{{ number_format($salary) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 注意事項 -->
        <div class="notice-card">
            <div class="notice-icon">ℹ️</div>
            <div class="notice-content">
                <h4 class="notice-title">お知らせ</h4>
                <ul class="notice-list">
                    <li>給料は確定シフトに基づいて計算されています</li>
                    <li>残業・深夜手当は時給の1.25倍で計算されています</li>
                    <li>詳細について質問がある場合は管理者にお問い合わせください</li>
                </ul>
            </div>
        </div>
    </main>

    <script>
        // 給料額のカウントアップアニメーション
        window.addEventListener('load', function() {
            const salaryAmount = document.querySelector('.salary-amount');
            const targetValue = {{ $salary }};
            const duration = 1500;
            const startTime = performance.now();

            function animate(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // イージング関数
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const currentValue = Math.floor(targetValue * easeOut);
                
                salaryAmount.textContent = '¥' + currentValue.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }
            
            requestAnimationFrame(animate);
        });

        // 詳細項目のフェードインアニメーション
        window.addEventListener('load', function() {
            const detailItems = document.querySelectorAll('.detail-item');
            detailItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.5s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100 + 200);
            });
        });
    </script>
</body>
</html>