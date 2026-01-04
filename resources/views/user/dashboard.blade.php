<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/user_dashboard.css') }}">
    <title>ユーザーダッシュボード - p-shift</title>
    <script src="{{ asset('js/home.js') }}"></script>
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

        <div class="user-info">
            <div class="avatar">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
            <div class="user-details">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="company-id">会社ID: {{ Auth::user()->company_id ?? '未所属' }}</p>
            </div>
        </div>

        <nav class="nav-menu">
            <div class="nav-section">
                <p class="nav-section-title">シフト管理</p>
                <a href="{{ route('calendar.shift.create') }}" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">シフト提出</span>
                </a>
                <a href="{{ route('user.shifts.edit') }}" class="nav-item">
                    <span class="nav-icon">✏️</span>
                    <span class="nav-text">提出済み編集</span>
                </a>
                <a href="{{ route('fixed.user.view') }}" class="nav-item">
                    <span class="nav-icon">✅</span>
                    <span class="nav-text">確定シフト確認</span>
                </a>
            </div>

            <div class="nav-section">
                <p class="nav-section-title">その他</p>
                <a href="{{ route('user.salary.index') }}" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span class="nav-text">給料確認</span>
                </a>
                <a href="{{ route('chat.index') }}" class="nav-item">
                    <span class="nav-icon">💬</span>
                    <span class="nav-text">チャット</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <span class="logout-icon">🚪</span>
                    <span>ログアウト</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content">
        <header class="content-header">
            <h1 class="page-title">ユーザーダッシュボード</h1>
            <p class="page-subtitle">ようこそ、{{ Auth::user()->name }} さん</p>
        </header>

        <div class="dashboard-grid">
            <!-- クイックアクセスカード -->
            <div class="card card-primary">
                <div class="card-icon">📝</div>
                <div class="card-content">
                    <h3 class="card-title">シフト提出</h3>
                    <p class="card-description">カレンダー形式で希望シフトを簡単に一括提出できます</p>
                    <a href="{{ route('calendar.shift.create') }}" class="card-button">シフトを提出 →</a>
                </div>
            </div>

            <div class="card card-secondary">
                <div class="card-icon">✏️</div>
                <div class="card-content">
                    <h3 class="card-title">提出済み編集</h3>
                    <p class="card-description">提出した希望シフトの編集や削除が可能です</p>
                    <a href="{{ route('user.shifts.edit') }}" class="card-button">編集する →</a>
                </div>
            </div>

            <div class="card card-accent">
                <div class="card-icon">✅</div>
                <div class="card-content">
                    <h3 class="card-title">確定シフト確認</h3>
                    <p class="card-description">管理者が確定したシフトを確認できます</p>
                    <a href="{{ route('fixed.user.view') }}" class="card-button">確認する →</a>
                </div>
            </div>

            <div class="card card-success">
                <div class="card-icon">💰</div>
                <div class="card-content">
                    <h3 class="card-title">給料確認</h3>
                    <p class="card-description">勤務時間に基づいた給料情報を確認できます</p>
                    <a href="{{ route('user.salary.index') }}" class="card-button">確認する →</a>
                </div>
            </div>

            <div class="card card-chat">
                <div class="card-icon">💬</div>
                <div class="card-content">
                    <h3 class="card-title">チャット</h3>
                    <p class="card-description">管理者や他の従業員とコミュニケーション</p>
                    <a href="{{ route('chat.index') }}" class="card-button">開く →</a>
                </div>
            </div>

            <!-- お知らせカード（オプション） -->
            <div class="card card-info">
                <div class="card-icon">📢</div>
                <div class="card-content">
                    <h3 class="card-title">お知らせ</h3>
                    <p class="card-description">シフト提出期限や重要なお知らせをチェック</p>
                    <div class="notice-badge">新着なし</div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>