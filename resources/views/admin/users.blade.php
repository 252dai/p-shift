<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin_users.css') }}">
    <title>ユーザー管理 - p-shift</title>
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

        <div class="summary-info">
            <p class="section-title">ユーザー統計</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">総ユーザー数</span>
                    <span class="summary-value">{{ count($users) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">会社ID</span>
                    <span class="summary-value small">{{ Auth::user()->company_id ?? '未設定' }}</span>
                </div>
            </div>
        </div>

        <div class="actions-section">
            <p class="section-title">クイック操作</p>
            <a href="{{ route('admin.users.search') }}" class="action-btn">
                <span>✉️</span>
                <span>新規ユーザー招待</span>
            </a>
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
            <div class="header-top">
                <div>
                    <h1 class="page-title">ユーザー管理</h1>
                    <p class="page-subtitle">会社内のユーザー一覧と管理</p>
                </div>
                <div class="header-actions">
                    <input type="text" id="searchInput" class="search-input" placeholder="🔍 名前やメールで検索...">
                </div>
            </div>
        </header>

        <!-- 成功メッセージ -->
        @if (session('message'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- ユーザーテーブル -->
        <div class="table-wrapper">
            <table class="users-table" id="usersTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)">
                            <span>名前</span>
                            <span class="sort-icon">⇅</span>
                        </th>
                        <th class="sortable" onclick="sortTable(1)">
                            <span>メール</span>
                            <span class="sort-icon">⇅</span>
                        </th>
                        <th>会社ID</th>
                        <th class="action-column">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="user-row">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                                    <span class="user-name">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="user-email">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="company-badge">{{ $user->company_id }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn-edit" onclick="editUser({{ $user->id }}, '{{ $user->name }}')">
                                        <span>✏️</span>
                                        <span>編集</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="delete-form" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <span>🗑️</span>
                                            <span>削除</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- ユーザーが0人の場合 -->
            @if(count($users) === 0)
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>ユーザーがいません</h3>
                    <p>新しいユーザーを招待してチームを作りましょう</p>
                    <a href="{{ route('admin.users.search') }}" class="btn-primary">
                        <span>✉️</span>
                        <span>ユーザーを招待</span>
                    </a>
                </div>
            @endif
        </div>
    </main>

    <script>
        // 検索機能
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.querySelector('.user-email').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // ソート機能
        function sortTable(columnIndex) {
            const table = document.getElementById('usersTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // 現在のソート状態を取得
            const header = table.querySelectorAll('th')[columnIndex];
            const isAscending = header.classList.contains('sort-asc');
            
            // 全てのソートクラスをリセット
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            // ソート
            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                
                if (isAscending) {
                    return bText.localeCompare(aText, 'ja');
                } else {
                    return aText.localeCompare(bText, 'ja');
                }
            });
            
            // ソート状態を更新
            header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
            
            // 行を再配置
            rows.forEach(row => tbody.appendChild(row));
        }

        // 編集機能（仮実装）
        function editUser(userId, userName) {
            alert(`ユーザー編集機能は実装予定です\n\nユーザーID: ${userId}\n名前: ${userName}`);
        }

        // 削除確認
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const userName = this.closest('tr').querySelector('.user-name').textContent;
                
                if (!confirm(`本当に「${userName}」を削除しますか？\n\nこの操作は取り消せません。`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>