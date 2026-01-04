<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー招待 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/admin_search.css') }}">
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

        <div class="info-section">
            <p class="section-title">招待について</p>
            <div class="info-card">
                <div class="info-item">
                    <span class="info-icon">🔍</span>
                    <p class="info-text">名前またはメールアドレスでユーザーを検索できます</p>
                </div>
                <div class="info-item">
                    <span class="info-icon">✉️</span>
                    <p class="info-text">招待すると、あなたの会社IDが設定されます</p>
                </div>
                <div class="info-item">
                    <span class="info-icon">👥</span>
                    <p class="info-text">招待したユーザーはチームメンバーになります</p>
                </div>
            </div>
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
            <h1 class="page-title">ユーザー招待</h1>
            <p class="page-subtitle">新しいメンバーを検索して招待しましょう</p>
        </header>

        <!-- メッセージ表示 -->
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <span class="alert-icon">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 検索フォーム -->
        <div class="search-section">
            <form method="POST" action="{{ route('admin.users.search') }}" class="search-form">
                @csrf
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="keyword" 
                        class="search-input" 
                        placeholder="名前またはメールアドレスで検索..." 
                        value="{{ old('keyword', $keyword ?? '') }}" 
                        required
                        autofocus>
                    <button type="submit" class="search-btn">
                        <span>検索</span>
                    </button>
                </div>
                <p class="search-hint">※ 部分一致で検索できます</p>
            </form>
        </div>

        <!-- 検索結果 -->
        @if(isset($users))
            <div class="results-section">
                <div class="results-header">
                    <h2 class="results-title">検索結果</h2>
                    <span class="results-count">{{ count($users) }} 件</span>
                </div>

                @if(count($users) > 0)
                    <div class="users-grid">
                        @foreach($users as $user)
                            <div class="user-card">
                                <div class="user-card-header">
                                    <div class="user-avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                                    <div class="user-details">
                                        <h3 class="user-name">{{ $user->name }}</h3>
                                        <p class="user-email">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="user-card-footer">
                                    <form method="POST" action="{{ route('admin.users.invite', $user) }}" class="invite-form">
                                        @csrf
                                        <button type="submit" class="invite-btn">
                                            <span class="invite-icon">✉️</span>
                                            <span>招待する</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-results">
                        <div class="empty-icon">🔍</div>
                        <h3>該当するユーザーが見つかりませんでした</h3>
                        <p>別のキーワードで検索してみてください</p>
                    </div>
                @endif
            </div>
        @else
            <!-- 初期状態 -->
            <div class="initial-state">
                <div class="initial-icon">👋</div>
                <h3>ユーザーを検索して招待しましょう</h3>
                <p>上の検索ボックスに名前またはメールアドレスを入力してください</p>
            </div>
        @endif
    </main>

    <script>
        // 招待ボタンのクリック時に確認
        document.querySelectorAll('.invite-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const userName = this.closest('.user-card').querySelector('.user-name').textContent;
                const userEmail = this.closest('.user-card').querySelector('.user-email').textContent;
                
                if (!confirm(`${userName} (${userEmail}) を招待しますか？\n\n招待すると、このユーザーにあなたの会社IDが設定されます。`)) {
                    e.preventDefault();
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
    </script>
</body>
</html>