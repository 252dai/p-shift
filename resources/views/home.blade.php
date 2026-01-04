<!-- resources/views/home.blade.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>p-shift - シフト管理をもっとスマートに</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
</head>
<body>
    <!-- 背景グラデーションアニメーション -->
    <div class="gradient-bg"></div>
    <div class="gradient-overlay"></div>

    <!-- ヘッダー -->
    <header class="header">
        <div class="logo">
            <span class="logo-icon">📅</span>
            <span class="logo-text">p-shift</span>
        </div>
        <nav class="nav">
            <a href="{{ route('login') }}" class="nav-link">ログイン</a>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="container">
        <div class="hero">
            <div class="badge">シフト管理の新しいスタンダード</div>
            <h1 class="title">
                <span class="title-line">シフト管理を</span>
                <span class="title-line gradient-text">もっとスマートに</span>
            </h1>
            <p class="subtitle">
                直感的なUIで、チームのシフト作成から共有まで。<br>
                p-shiftで、働き方をもっと自由に、もっと効率的に。
            </p>
            
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="button button-primary">
                    <span>新規登録</span>
                    <svg class="button-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="button button-secondary">
                    ログイン
                </a>
            </div>

            <!-- 特徴リスト -->
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-text">
                        <h3>簡単作成</h3>
                        <p>数クリックでシフト表完成</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-text">
                        <h3>分析機能</h3>
                        <p>勤務データを可視化</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- フッター -->
    <footer class="footer">
        <p>&copy; 2024 p-shift. All rights reserved.</p>
    </footer>

    <!-- 浮遊する装飾要素 -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
</body>
</html>