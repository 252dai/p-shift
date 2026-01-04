<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <title>管理者ダッシュボード - p-shift</title>
</head>
<body>
    <!-- サイドバー -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📅</span>
                <span class="logo-text">p-shift</span>
            </div>
            <div class="user-badge">管理者</div>
        </div>

        <div class="user-info">
            <div class="avatar">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
            <div class="user-details">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="company-id">会社ID: {{ Auth::user()->company_id ?? '未設定' }}</p>
            </div>
        </div>

        <nav class="nav-menu">
            <div class="nav-section">
                <p class="nav-section-title">シフト管理</p>
                <a href="{{ route('admin.calendar.shift') }}" class="nav-item">
                    <span class="nav-icon">📋</span>
                    <span class="nav-text">希望シフト一覧</span>
                </a>
                <a href="{{ route('admin.fixed.index') }}" class="nav-item">
                    <span class="nav-icon">✏️</span>
                    <span class="nav-text">確定シフト編集</span>
                </a>
            </div>

            <div class="nav-section">
                <p class="nav-section-title">従業員管理</p>
                <a href="{{ route('admin.users') }}" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">ユーザー一覧</span>
                </a>
                <a href="{{ route('admin.users.search') }}" class="nav-item">
                    <span class="nav-icon">✉️</span>
                    <span class="nav-text">従業員招待</span>
                </a>
            </div>

            <div class="nav-section">
                <p class="nav-section-title">その他</p>
                <a href="{{ route('admin.salary.index') }}" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span class="nav-text">給料一覧</span>
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
            <h1 class="page-title">管理者ダッシュボード</h1>
            <p class="page-subtitle">ようこそ、{{ Auth::user()->name }} さん</p>
        </header>

        <div class="dashboard-grid">
            <!-- クイックアクセスカード -->
            <div class="card card-primary">
                <div class="card-icon">📋</div>
                <div class="card-content">
                    <h3 class="card-title">希望シフト確認</h3>
                    <p class="card-description">従業員から提出された希望シフトをカレンダー形式で確認できます</p>
                    <a href="{{ route('admin.calendar.shift') }}" class="card-button">シフトを見る →</a>
                </div>
            </div>

            <div class="card card-secondary">
                <div class="card-icon">✏️</div>
                <div class="card-content">
                    <h3 class="card-title">確定シフト編集</h3>
                    <p class="card-description">確定したシフトの編集や削除を行えます</p>
                    <a href="{{ route('admin.fixed.index') }}" class="card-button">編集する →</a>
                </div>
            </div>

            <div class="card card-accent">
                <div class="card-icon">👥</div>
                <div class="card-content">
                    <h3 class="card-title">ユーザー管理</h3>
                    <p class="card-description">従業員の一覧表示、編集、削除が可能です</p>
                    <a href="{{ route('admin.users') }}" class="card-button">管理する →</a>
                </div>
            </div>

            <div class="card card-info">
                <div class="card-icon">✉️</div>
                <div class="card-content">
                    <h3 class="card-title">従業員招待</h3>
                    <p class="card-description">新しい従業員をシステムに招待します</p>
                    <a href="{{ route('admin.users.search') }}" class="card-button">招待する →</a>
                </div>
            </div>

            <div class="card card-success">
                <div class="card-icon">💰</div>
                <div class="card-content">
                    <h3 class="card-title">給料管理</h3>
                    <p class="card-description">従業員の給料情報を確認できます</p>
                    <a href="{{ route('admin.salary.index') }}" class="card-button">確認する →</a>
                </div>
            </div>

            <div class="card card-chat">
                <div class="card-icon">💬</div>
                <div class="card-content">
                    <h3 class="card-title">チャット</h3>
                    <p class="card-description">従業員とリアルタイムでコミュニケーション</p>
                    <a href="{{ route('chat.index') }}" class="card-button">開く →</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>