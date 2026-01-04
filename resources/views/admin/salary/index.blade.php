<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>給料管理 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/admin_salary.css') }}">
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
            <p class="section-title">給料サマリー</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">対象期間</span>
                    <span class="summary-value small">{{ $year }}年{{ $month }}月</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">従業員数</span>
                    <span class="summary-value">{{ count($salaryData) }}人</span>
                </div>
                <div class="summary-item highlight">
                    <span class="summary-label">合計給料</span>
                    <span class="summary-value large">¥{{ number_format($totalSalary) }}</span>
                </div>
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
                    <h1 class="page-title">給料管理</h1>
                    <p class="page-subtitle">{{ $year }}年{{ $month }}月の給料一覧</p>
                </div>
                <div class="header-actions">
                    <button class="export-btn" onclick="exportToCSV()">
                        <span>📊</span>
                        <span>CSVエクスポート</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- 成功メッセージ -->
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 給料テーブル -->
        <div class="table-wrapper">
            <table class="salary-table" id="salaryTable">
                <thead>
                    <tr>
                        <th class="name-column">名前</th>
                        <th class="wage-column">時給</th>
                        <th class="hours-column">通常時間</th>
                        <th class="hours-column">残業時間</th>
                        <th class="hours-column">深夜時間</th>
                        <th class="salary-column">給料</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salaryData as $data)
                        <tr class="salary-row">
                            <td>
                                <div class="employee-info">
                                    <div class="employee-avatar">{{ mb_substr($data['user']->name, 0, 1) }}</div>
                                    <span class="employee-name">{{ $data['user']->name }}</span>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.salary.updateHourlyWage', $data['user']->id) }}" class="wage-form">
                                    @csrf
                                    <div class="wage-input-group">
                                        <input type="number" name="hourly_wage" value="{{ $data['user']->hourly_wage }}" min="0" class="wage-input">
                                        <span class="wage-unit">円</span>
                                        <button type="submit" class="update-btn">
                                            <span>更新</span>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td class="hours-cell">
                                <span class="hours-badge regular">{{ $data['regular_hours'] }}h</span>
                            </td>
                            <td class="hours-cell">
                                <span class="hours-badge overtime">{{ $data['overtime_hours'] }}h</span>
                            </td>
                            <td class="hours-cell">
                                <span class="hours-badge night">{{ $data['night_hours'] }}h</span>
                            </td>
                            <td class="salary-cell">
                                <span class="salary-amount">¥{{ number_format($data['salary']) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="total-label">合計給料</td>
                        <td class="total-amount">¥{{ number_format($totalSalary) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </main>

    <script>
        // 時給更新フォームの送信前確認
        document.querySelectorAll('.wage-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const name = this.closest('tr').querySelector('.employee-name').textContent;
                const newWage = this.querySelector('.wage-input').value;
                
                if (!confirm(`${name} さんの時給を ${newWage}円 に更新しますか？`)) {
                    e.preventDefault();
                }
            });
        });

        // CSVエクスポート機能
        function exportToCSV() {
            const table = document.getElementById('salaryTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            
            let csv = '名前,時給,通常時間,残業時間,深夜時間,給料\n';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const name = cells[0].querySelector('.employee-name').textContent;
                const wage = cells[1].querySelector('.wage-input').value;
                const regular = cells[2].textContent.replace('h', '');
                const overtime = cells[3].textContent.replace('h', '');
                const night = cells[4].textContent.replace('h', '');
                const salary = cells[5].textContent.replace('¥', '').replace(/,/g, '');
                
                csv += `${name},${wage},${regular},${overtime},${night},${salary}\n`;
            });
            
            // 合計行を追加
            csv += `合計,,,,,${document.querySelector('.total-amount').textContent.replace('¥', '').replace(/,/g, '')}\n`;
            
            // ダウンロード
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `給料一覧_{{ $year }}年{{ $month }}月.csv`;
            link.click();
        }

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

        // 時給入力欄のフォーカス時にハイライト
        document.querySelectorAll('.wage-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('tr').classList.add('editing');
            });
            
            input.addEventListener('blur', function() {
                this.closest('tr').classList.remove('editing');
            });
        });
    </script>
</body>
</html>