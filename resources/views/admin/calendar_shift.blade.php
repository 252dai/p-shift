<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>希望シフト確認 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/cale_shift.css') }}">
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();
        $year = $date->year;
        $month = $date->month;
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
        $daysInMonth = $date->daysInMonth;
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
            <p class="section-title">提出状況</p>
            <div class="summary-card">
                <div class="summary-item">
                    <span class="summary-label">提出済み</span>
                    <span class="summary-value" id="submittedCount">0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">未提出</span>
                    <span class="summary-value text-gray" id="notSubmittedCount">0</span>
                </div>
                <div class="summary-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressBar"></div>
                    </div>
                    <span class="progress-text" id="progressText">0%</span>
                </div>
            </div>
        </div>

        <div class="selection-info">
            <p class="section-title">選択中</p>
            <div class="selection-card">
                <div class="selection-count">
                    <span class="selection-number" id="selectionCount">0</span>
                    <span class="selection-label">件のシフト</span>
                </div>
                <button class="clear-selection-btn" onclick="clearAllSelections()" id="clearBtn" disabled>
                    <span>✕</span>
                    <span>選択解除</span>
                </button>
            </div>
        </div>

        <div class="search-section">
            <p class="section-title">検索</p>
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="従業員名で検索..." class="search-input">
            </div>
        </div>

        <div class="filter-section">
            <p class="section-title">フィルター</p>
            <button type="button" class="filter-btn active" onclick="filterAll()">
                <span>📋</span>
                <span>全て表示</span>
            </button>
            <button type="button" class="filter-btn" onclick="filterWithShifts()">
                <span>✓</span>
                <span>希望あり</span>
            </button>
            <button type="button" class="filter-btn" onclick="filterNoShifts()">
                <span>○</span>
                <span>希望なし</span>
            </button>
        </div>

        <div class="quick-actions">
            <p class="section-title">クイック操作</p>
            <button class="quick-btn" onclick="selectAllShifts()">
                <span>☑</span>
                <span>全て選択</span>
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
            <div class="header-left">
                <h1 class="page-title">希望シフト一覧</h1>
                <p class="page-subtitle">従業員から提出された希望シフトを確認して確定できます</p>
            </div>
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

        <!-- 通知メッセージ -->
        <div id="notification" class="notification"></div>

        <!-- カレンダー表示 -->
        <div id="calendarView" class="calendar-wrapper">
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
                    @php
                        $day = 1;
                        $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
                    @endphp

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
                                        $hasShifts = isset($shifts[$ymd]) && count($shifts[$ymd]) > 0;
                                        $shiftCount = $hasShifts ? count($shifts[$ymd]) : 0;
                                        $displayLimit = 3; // 最初に表示する件数
                                    @endphp
                                    <td class="{{ $cellClass }} {{ $hasShifts ? 'has-shifts' : 'no-shifts' }}" 
                                        data-has-shifts="{{ $hasShifts ? '1' : '0' }}"
                                        data-date="{{ $ymd }}">
                                        <div class="day-cell">
                                            <div class="day-header-row">
                                                <div class="day-number">{{ $day }}</div>
                                                <div class="day-actions">
                                                    @if($hasShifts)
                                                        <button class="select-day-btn" onclick="toggleDaySelection(this, '{{ $ymd }}')" title="この日の全てを選択">
                                                            <span>☑</span>
                                                        </button>
                                                        <span class="shift-badge">{{ $shiftCount }}件</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($hasShifts)
                                                <div class="shift-container">
                                                    @foreach($shifts[$ymd] as $index => $shift)
                                                        <div class="shift-entry {{ $index >= $displayLimit ? 'hidden-shift' : '' }}" 
                                                             data-employee-name="{{ $shift->user->name }}"
                                                             data-shift-id="shift-{{ $shift->id }}">
                                                            <div class="shift-mini-card">
                                                                <label class="shift-checkbox-label">
                                                                    <input type="checkbox" 
                                                                           class="shift-checkbox" 
                                                                           data-user-id="{{ $shift->user->id }}"
                                                                           data-date="{{ $ymd }}"
                                                                           data-start="{{ $shift->start_time }}"
                                                                           data-end="{{ $shift->end_time }}"
                                                                           data-name="{{ $shift->user->name }}"
                                                                           onchange="updateSelectionCount()">
                                                                    <div class="shift-info-wrapper" onclick="openShiftModal(this.previousElementSibling)">
                                                                        <span class="mini-name">{{ $shift->user->name }}</span>
                                                                        <span class="mini-time">{{ $shift->start_time }}〜{{ $shift->end_time }}</span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    @if($shiftCount > $displayLimit)
                                                        <button class="show-more-btn" onclick="toggleAllShifts(this, '{{ $ymd }}')">
                                                            <span class="more-icon">+</span>
                                                            <span class="more-text">あと{{ $shiftCount - $displayLimit }}件</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="no-shift-text">希望なし</span>
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

    <!-- 一括操作バー -->
    <div id="bulkActionBar" class="bulk-action-bar">
        <div class="bulk-info">
            <span class="bulk-count" id="bulkCount">0</span>
            <span class="bulk-label">件選択中</span>
        </div>
        <div class="bulk-actions">
            <button class="bulk-btn bulk-btn-cancel" onclick="clearAllSelections()">
                キャンセル
            </button>
            <button class="bulk-btn bulk-btn-confirm" onclick="confirmBulkShifts()">
                <span>✓</span>
                <span>一括確定</span>
            </button>
        </div>
    </div>

    <!-- シフト詳細モーダル -->
    <div id="shiftModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">シフト詳細</h2>
                <button class="modal-close" onclick="closeShiftModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-info-group">
                    <label class="modal-label">従業員名</label>
                    <div class="modal-value" id="modalEmployeeName"></div>
                </div>
                <div class="modal-info-group">
                    <label class="modal-label">日付</label>
                    <div class="modal-value" id="modalDate"></div>
                </div>
                <div class="modal-info-group">
                    <label class="modal-label">勤務時間</label>
                    <div class="modal-value modal-time" id="modalTime"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-cancel" onclick="closeShiftModal()">閉じる</button>
            </div>
        </div>
    </div>

    <script>
        // グローバル変数
        let currentFilter = 'all';
        let currentSearchTerm = '';
        let selectedShifts = new Set();

        // 選択数の更新
        function updateSelectionCount() {
            const checkboxes = document.querySelectorAll('.shift-checkbox:checked');
            const count = checkboxes.length;
            
            document.getElementById('selectionCount').textContent = count;
            document.getElementById('bulkCount').textContent = count;
            document.getElementById('clearBtn').disabled = count === 0;
            
            const bulkBar = document.getElementById('bulkActionBar');
            if (count > 0) {
                bulkBar.classList.add('show');
            } else {
                bulkBar.classList.remove('show');
            }
        }

        // 全選択
        function selectAllShifts() {
            const visibleCheckboxes = document.querySelectorAll('.shift-checkbox');
            const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
            
            visibleCheckboxes.forEach(checkbox => {
                if (!checkbox.closest('.shift-entry').classList.contains('hidden-shift')) {
                    checkbox.checked = !allChecked;
                }
            });
            
            updateSelectionCount();
            showNotification(allChecked ? '選択を解除しました' : '全てのシフトを選択しました', 'info');
        }

        // 日付ごとの全選択
        function toggleDaySelection(button, date) {
            const cell = button.closest('td');
            const checkboxes = cell.querySelectorAll('.shift-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            
            button.classList.toggle('active');
            updateSelectionCount();
        }

        // 選択解除
        function clearAllSelections() {
            document.querySelectorAll('.shift-checkbox:checked').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.select-day-btn.active').forEach(btn => {
                btn.classList.remove('active');
            });
            updateSelectionCount();
        }

        // 一括確定
        function confirmBulkShifts() {
            const checkboxes = document.querySelectorAll('.shift-checkbox:checked');
            
            if (checkboxes.length === 0) {
                showNotification('シフトを選択してください', 'error');
                return;
            }
            
            const shiftsData = Array.from(checkboxes).map(cb => ({
                user_id: cb.dataset.userId,
                date: cb.dataset.date,
                start_time: cb.dataset.start,
                end_time: cb.dataset.end,
                name: cb.dataset.name
            }));
            
            const message = `${shiftsData.length}件のシフトを一括確定しますか?\n\n確定するシフト:\n${shiftsData.map(s => `・${s.name} (${s.date} ${s.start_time}〜${s.end_time})`).slice(0, 5).join('\n')}${shiftsData.length > 5 ? '\n...他' + (shiftsData.length - 5) + '件' : ''}`;
            
            if (confirm(message)) {
                // 一括確定のフォームを作成して送信
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.calendar.bulk-fix') }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const shiftsInput = document.createElement('input');
                shiftsInput.type = 'hidden';
                shiftsInput.name = 'shifts';
                shiftsInput.value = JSON.stringify(shiftsData);
                form.appendChild(shiftsInput);
                
                document.body.appendChild(form);
                form.submit();
                
                showNotification(`${shiftsData.length}件のシフトを確定しました`, 'success');
            }
        }

        // モーダル開く
        function openShiftModal(checkbox) {
            const modal = document.getElementById('shiftModal');
            const name = checkbox.dataset.name;
            const date = checkbox.dataset.date;
            const startTime = checkbox.dataset.start;
            const endTime = checkbox.dataset.end;

            document.getElementById('modalEmployeeName').textContent = name;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalTime').textContent = `${startTime} 〜 ${endTime}`;

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // モーダル閉じる
        function closeShiftModal() {
            const modal = document.getElementById('shiftModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // モーダル外クリックで閉じる
        document.getElementById('shiftModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeShiftModal();
            }
        });

        // ESCキーでモーダル閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeShiftModal();
            }
            
            // Ctrl+A で全選択
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
                e.preventDefault();
                selectAllShifts();
            }
        });

        // もっと見るボタン
        function toggleAllShifts(button, date) {
            const cell = button.closest('td');
            const isExpanded = button.classList.contains('expanded');

            if (isExpanded) {
                const visibleShifts = cell.querySelectorAll('.visible-shift');
                const count = visibleShifts.length;
                visibleShifts.forEach(shift => {
                    shift.classList.remove('visible-shift');
                    shift.classList.add('hidden-shift');
                });
                button.classList.remove('expanded');
                button.innerHTML = `<span class="more-icon">+</span><span class="more-text">あと${count}件</span>`;
            } else {
                const hiddenShifts = cell.querySelectorAll('.hidden-shift');
                hiddenShifts.forEach(shift => {
                    shift.classList.remove('hidden-shift');
                    shift.classList.add('visible-shift');
                });
                button.classList.add('expanded');
                button.innerHTML = `<span class="more-icon">−</span><span class="more-text">閉じる</span>`;
            }
        }

        // フィルター
        function filterAll() {
            currentFilter = 'all';
            applyFilters();
            setActiveFilter(0);
        }

        function filterWithShifts() {
            currentFilter = 'with-shifts';
            applyFilters();
            setActiveFilter(1);
        }

        function filterNoShifts() {
            currentFilter = 'no-shifts';
            applyFilters();
            setActiveFilter(2);
        }

        function applyFilters() {
            document.querySelectorAll('.calendar td[data-has-shifts]').forEach(td => {
                let show = true;

                if (currentFilter === 'with-shifts' && td.dataset.hasShifts === '0') {
                    show = false;
                } else if (currentFilter === 'no-shifts' && td.dataset.hasShifts === '1') {
                    show = false;
                }

                if (show && currentSearchTerm && td.dataset.hasShifts === '1') {
                    const shiftEntries = td.querySelectorAll('.shift-entry');
                    let hasMatch = false;
                    shiftEntries.forEach(entry => {
                        const name = entry.dataset.employeeName.toLowerCase();
                        if (name.includes(currentSearchTerm.toLowerCase())) {
                            hasMatch = true;
                            entry.style.display = '';
                        } else {
                            entry.style.display = 'none';
                        }
                    });
                    show = hasMatch;
                } else if (show && td.dataset.hasShifts === '1') {
                    td.querySelectorAll('.shift-entry').forEach(entry => {
                        entry.style.display = '';
                    });
                }

                td.style.display = show ? '' : 'none';
            });
        }

        function setActiveFilter(index) {
            document.querySelectorAll('.filter-btn').forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // 検索機能
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            currentSearchTerm = e.target.value;
            applyFilters();
        });

        // 提出状況の集計
        function updateSummary() {
            const totalDays = document.querySelectorAll('.calendar td[data-has-shifts]').length;
            const withShifts = document.querySelectorAll('.calendar td[data-has-shifts="1"]').length;
            const noShifts = totalDays - withShifts;
            const percentage = totalDays > 0 ? Math.round((withShifts / totalDays) * 100) : 0;
            
            document.getElementById('submittedCount').textContent = withShifts;
            document.getElementById('notSubmittedCount').textContent = noShifts;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('progressText').textContent = percentage + '%';
        }

        // 通知表示
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // ページ読み込み時
        window.addEventListener('load', function() {
            updateSummary();
            updateSelectionCount();
        });
    </script>
</body>
</html>