<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>シフト提出 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/user_cale_shift.css') }}">
</head>
<body>
    @php
        use Carbon\Carbon;
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();
        $daysInMonth = $date->daysInMonth;
        $year = $date->year;
        $month = $date->month;
        $firstDay = Carbon::create($year, $month, 1);
        $startWeekday = $firstDay->dayOfWeek;
    @endphp

    <!-- サイドバー -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📅</span>
                <span class="logo-text">p-shift</span>
            </div>
        </div>

        <!-- プリセット管理 -->
        <div class="quick-actions">
            <p class="section-title">よく使う時間を保存</p>
            <div class="preset-creator">
                <input type="time" id="customStart" class="preset-input" value="09:00">
                <span class="preset-separator">〜</span>
                <input type="time" id="customEnd" class="preset-input" value="17:00">
                <button type="button" class="save-preset-btn" onclick="saveCustomPreset()">
                    <span>💾</span>
                </button>
            </div>
            <div id="savedPresets" class="saved-presets"></div>
        </div>

        <!-- 日付範囲選択 -->
        <div class="range-selection">
            <p class="section-title">日付範囲で選択</p>
            <div class="range-inputs">
                <input type="number" id="rangeStart" class="range-input" placeholder="開始日" min="1" max="{{ $daysInMonth }}">
                <span class="range-separator">〜</span>
                <input type="number" id="rangeEnd" class="range-input" placeholder="終了日" min="1" max="{{ $daysInMonth }}">
            </div>
            <button type="button" class="range-btn" onclick="selectRange()">
                <span>📅</span>
                <span>範囲選択</span>
            </button>
        </div>

        <!-- クイックアクション -->
        <div class="quick-fill">
            <p class="section-title">クイック選択</p>
            <button type="button" class="action-btn" onclick="fillWeekdays()">
                <span class="action-icon">📆</span>
                <span>平日のみ</span>
            </button>
            <button type="button" class="action-btn" onclick="fillWeekends()">
                <span class="action-icon">🎉</span>
                <span>土日のみ</span>
            </button>
            <button type="button" class="action-btn" onclick="selectAll()">
                <span class="action-icon">✓</span>
                <span>全日選択</span>
            </button>
            <button type="button" class="action-btn danger" onclick="clearAll()">
                <span class="action-icon">🗑️</span>
                <span>全てクリア</span>
            </button>
        </div>

        <!-- コピー機能 -->
        <div class="copy-section">
            <p class="section-title">時間をコピー</p>
            <button type="button" class="action-btn copy" onclick="copyFirstDay()">
                <span class="action-icon">📋</span>
                <span>1日目を全てにコピー</span>
            </button>
        </div>

        <div class="sidebar-footer">
            <div class="shift-count">
                提出予定: <span id="shiftCount">0</span>日
            </div>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content">
        <header class="content-header">
            <div class="header-top">
                <div>
                    <h1 class="page-title">希望シフト提出</h1>
                    <p class="page-subtitle">勤務可能な日と時間を入力してください</p>
                </div>
                <div class="help-btn" onclick="showHelp()">
                    <span>❓</span>
                    <span>使い方</span>
                </div>
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

        <!-- フォーム -->
        <form method="POST" action="{{ route('calendar.shift.store') }}" id="shiftForm">
            @csrf
            
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
                        @php
                            $day = 1;
                            $cellCount = 0;
                        @endphp

                        @while ($day <= $daysInMonth)
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $isWeekend = ($i === 0 || $i === 6);
                                        $cellClass = $isWeekend ? ($i === 0 ? 'sunday' : 'saturday') : '';
                                    @endphp
                                    
                                    @if ($cellCount < $startWeekday || $day > $daysInMonth)
                                        <td class="{{ $cellClass }}"></td>
                                    @else
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                            $isWeekday = !$isWeekend;
                                        @endphp
                                        <td class="{{ $cellClass }}" data-weekday="{{ $isWeekday ? '1' : '0' }}" data-day="{{ $day }}">
                                            <div class="day-cell" onclick="toggleSelection(this)">
                                                <div class="day-number">{{ $day }}</div>
                                                <div class="selection-indicator">✓</div>
                                                <div class="time-inputs">
                                                    <input type="time" 
                                                           name="shifts[{{ $dateStr }}][start_time]" 
                                                           class="time-input start-time"
                                                           placeholder="開始"
                                                           onclick="event.stopPropagation()"
                                                           onchange="updateShiftCount()">
                                                    <span class="time-separator">〜</span>
                                                    <input type="time" 
                                                           name="shifts[{{ $dateStr }}][end_time]" 
                                                           class="time-input end-time"
                                                           placeholder="終了"
                                                           onclick="event.stopPropagation()"
                                                           onchange="updateShiftCount()">
                                                </div>
                                            </div>
                                        </td>
                                        @php $day++; @endphp
                                    @endif
                                    @php $cellCount++; @endphp
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>
            </div>

            <div class="submit-section">
                <a href="{{ route('user.dashboard') }}" class="back-btn">
                    ← ダッシュボードへ戻る
                </a>
                <button type="submit" class="submit-btn">
                    <span class="submit-icon">✓</span>
                    <span>まとめて提出</span>
                </button>
            </div>
        </form>
    </main>

    <script>
        let selectedCells = new Set();

        // シフト数のカウント更新
        function updateShiftCount() {
            let count = 0;
            document.querySelectorAll('.start-time').forEach(input => {
                const endTime = input.closest('.time-inputs').querySelector('.end-time');
                if (input.value && endTime.value) {
                    count++;
                }
            });
            document.getElementById('shiftCount').textContent = count;
        }

        // セル選択のトグル
        function toggleSelection(cell) {
            const td = cell.closest('td');
            if (td.classList.contains('selected')) {
                td.classList.remove('selected');
                selectedCells.delete(td);
            } else {
                td.classList.add('selected');
                selectedCells.add(td);
            }
        }

        // 選択をクリア
        function clearSelection() {
            selectedCells.forEach(cell => {
                cell.classList.remove('selected');
            });
            selectedCells.clear();
        }

        // プリセット管理
        function getSavedPresets() {
            const saved = localStorage.getItem('shiftPresets');
            return saved ? JSON.parse(saved) : [];
        }

        function renderPresets() {
            const container = document.getElementById('savedPresets');
            const presets = getSavedPresets();
            
            if (presets.length === 0) {
                container.innerHTML = '';
                return;
            }
            
            container.innerHTML = presets.map(preset => `
                <div class="preset-item">
                    <button type="button" class="preset-btn" onclick="applyPreset('${preset.start}', '${preset.end}')">
                        ${preset.start}〜${preset.end}
                    </button>
                    <button type="button" class="delete-preset-btn" onclick="deletePreset(${preset.id})">×</button>
                </div>
            `).join('');
        }

        function saveCustomPreset() {
            const start = document.getElementById('customStart').value;
            const end = document.getElementById('customEnd').value;
            
            if (!start || !end) {
                alert('時間を入力してください');
                return;
            }
            
            if (start >= end) {
                alert('終了時間は開始時間より後にしてください');
                return;
            }

            const presets = getSavedPresets();
            const newPreset = { start, end, id: Date.now() };
            presets.push(newPreset);
            localStorage.setItem('shiftPresets', JSON.stringify(presets));
            
            renderPresets();
            
            if (selectedCells.size > 0) {
                applyToSelected(start, end);
            } else {
                alert('プリセットを保存しました！\n日付を選択してから使えます。');
            }
        }

        function applyPreset(start, end) {
            if (selectedCells.size === 0) {
                alert('日付を選択してください\n\n使い方:\n1. カレンダーの日付をクリック（複数可）\n2. プリセットボタンをクリック');
                return;
            }
            applyToSelected(start, end);
        }

        function applyToSelected(start, end) {
            selectedCells.forEach(cell => {
                const startInput = cell.querySelector('.start-time');
                const endInput = cell.querySelector('.end-time');
                if (startInput && endInput) {
                    startInput.value = start;
                    endInput.value = end;
                }
            });
            clearSelection();
            updateShiftCount();
        }

        function deletePreset(id) {
            if (confirm('このプリセットを削除しますか？')) {
                const presets = getSavedPresets().filter(p => p.id !== id);
                localStorage.setItem('shiftPresets', JSON.stringify(presets));
                renderPresets();
            }
        }

        // 日付範囲選択
        function selectRange() {
            const start = parseInt(document.getElementById('rangeStart').value);
            const end = parseInt(document.getElementById('rangeEnd').value);
            
            if (!start || !end) {
                alert('開始日と終了日を入力してください');
                return;
            }
            
            if (start > end) {
                alert('終了日は開始日以降を指定してください');
                return;
            }
            
            clearSelection();
            document.querySelectorAll('td[data-day]').forEach(td => {
                const day = parseInt(td.dataset.day);
                if (day >= start && day <= end) {
                    td.classList.add('selected');
                    selectedCells.add(td);
                }
            });
            
            alert(`${start}日〜${end}日を選択しました\nプリセットから時間を選んでください`);
        }

        // 平日のみ
        function fillWeekdays() {
            clearSelection();
            document.querySelectorAll('td[data-weekday="1"]').forEach(td => {
                if (td.querySelector('.day-cell')) {
                    td.classList.add('selected');
                    selectedCells.add(td);
                    const startInput = td.querySelector('.start-time');
                    const endInput = td.querySelector('.end-time');
                    if (!startInput.value && !endInput.value) {
                        startInput.value = '09:00';
                        endInput.value = '17:00';
                    }
                }
            });
            updateShiftCount();
        }

        // 土日のみ
        function fillWeekends() {
            clearSelection();
            document.querySelectorAll('td[data-weekday="0"]').forEach(td => {
                if (td.querySelector('.day-cell')) {
                    td.classList.add('selected');
                    selectedCells.add(td);
                    const startInput = td.querySelector('.start-time');
                    const endInput = td.querySelector('.end-time');
                    if (!startInput.value && !endInput.value) {
                        startInput.value = '10:00';
                        endInput.value = '18:00';
                    }
                }
            });
            updateShiftCount();
        }

        // 全日選択
        function selectAll() {
            clearSelection();
            document.querySelectorAll('td[data-day]').forEach(td => {
                td.classList.add('selected');
                selectedCells.add(td);
            });
            alert('全ての日を選択しました\nプリセットから時間を選んでください');
        }

        // 全てクリア
        function clearAll() {
            if (confirm('入力した内容を全てクリアしますか？')) {
                document.querySelectorAll('.time-input').forEach(input => {
                    input.value = '';
                });
                clearSelection();
                updateShiftCount();
            }
        }

        // 1日目をコピー
        function copyFirstDay() {
            const firstCell = document.querySelector('td[data-day="1"]');
            if (!firstCell) return;
            
            const firstStart = firstCell.querySelector('.start-time').value;
            const firstEnd = firstCell.querySelector('.end-time').value;
            
            if (!firstStart || !firstEnd) {
                alert('1日目の時間を入力してください');
                return;
            }
            
            if (confirm(`1日目の時間（${firstStart}〜${firstEnd}）を\n全ての日にコピーしますか？`)) {
                document.querySelectorAll('td[data-day]').forEach(td => {
                    const startInput = td.querySelector('.start-time');
                    const endInput = td.querySelector('.end-time');
                    if (startInput && endInput) {
                        startInput.value = firstStart;
                        endInput.value = firstEnd;
                    }
                });
                updateShiftCount();
            }
        }

        // 使い方を表示
        function showHelp() {
            alert(`📝 シフト提出の使い方

【基本操作】
1️⃣ 日付をクリックして選択（緑色に変わります）
2️⃣ プリセットボタンで時間を一括設定

【便利機能】
💾 よく使う時間を保存
  → 時間を入力して保存ボタン
  
📅 日付範囲で選択
  → 開始日〜終了日を入力

📋 1日目をコピー
  → 1日目の時間を全日にコピー

【クイック選択】
📆 平日のみ → 月〜金を選択
🎉 土日のみ → 土日を選択
✓ 全日選択 → 全ての日を選択

【ショートカット】
選択した日を再度クリック → 選択解除`);
        }

        // フォーム送信前のバリデーション
        document.getElementById('shiftForm').addEventListener('submit', function(e) {
            let validShifts = 0;
            let hasError = false;

            document.querySelectorAll('.time-inputs').forEach(inputs => {
                const startInput = inputs.querySelector('.start-time');
                const endInput = inputs.querySelector('.end-time');
                
                if ((startInput.value && !endInput.value) || (!startInput.value && endInput.value)) {
                    hasError = true;
                }
                
                if (startInput.value && endInput.value) {
                    validShifts++;
                    if (startInput.value >= endInput.value) {
                        hasError = true;
                    }
                }
            });

            if (validShifts === 0) {
                e.preventDefault();
                alert('勤務可能な日を1日以上入力してください。');
                return;
            }

            if (hasError) {
                e.preventDefault();
                alert('入力内容を確認してください。\n・開始時間と終了時間の両方を入力\n・終了時間は開始時間より後');
                return;
            }

            if (!confirm(`${validShifts}日分のシフトを提出します。\nよろしいですか？`)) {
                e.preventDefault();
            }
        });

        // ページ読み込み時
        window.addEventListener('load', function() {
            updateShiftCount();
            renderPresets();
        });
    </script>
</body>
</html>