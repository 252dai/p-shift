<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>シフト編集 - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/fixed_edit.css') }}">
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <header class="page-header">
            <div class="header-content">
                <a href="{{ route('admin.fixed.index') }}" class="back-link">
                    <span class="back-icon">←</span>
                    <span>確定シフト一覧へ戻る</span>
                </a>
                <div class="logo">
                    <span class="logo-icon">📅</span>
                    <span class="logo-text">p-shift</span>
                </div>
            </div>
        </header>

        <!-- メインコンテンツ -->
        <main class="main-content">
            <div class="edit-card">
                <div class="card-header">
                    <div class="header-icon">✏️</div>
                    <div class="header-text">
                        <h1 class="card-title">シフト編集</h1>
                        <p class="card-subtitle">確定済みシフトの時間を変更できます</p>
                    </div>
                </div>

                <!-- 従業員情報 -->
                <div class="employee-info">
                    <div class="info-badge">
                        <span class="badge-icon">👤</span>
                        <span class="employee-name">{{ $shift->user->name }}</span>
                    </div>
                </div>

                <!-- 編集フォーム -->
                <form method="POST" action="{{ route('admin.fixed.update', $shift->id) }}" class="edit-form" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="section-title">
                            <span class="section-icon">📅</span>
                            <span>勤務日</span>
                        </div>
                        <div class="date-display">
                            <input type="hidden" name="date" value="{{ $shift->date }}">
                            <span class="date-value">{{ \Carbon\Carbon::parse($shift->date)->format('Y年m月d日') }}</span>
                            <span class="day-badge">{{ ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($shift->date)->dayOfWeek] }}</span>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <span class="section-icon">🕐</span>
                            <span>勤務時間</span>
                        </div>
                        
                        <div class="time-inputs">
                            <div class="input-group">
                                <label class="input-label">開始時間</label>
                                <div class="time-input-wrapper">
                                    <input 
                                        type="time" 
                                        name="start_time" 
                                        id="startTime"
                                        value="{{ old('start_time', $shift->start_time) }}" 
                                        class="time-input"
                                        required>
                                    <span class="input-icon">▶</span>
                                </div>
                                @error('start_time')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="time-separator">〜</div>

                            <div class="input-group">
                                <label class="input-label">終了時間</label>
                                <div class="time-input-wrapper">
                                    <input 
                                        type="time" 
                                        name="end_time" 
                                        id="endTime"
                                        value="{{ old('end_time', $shift->end_time) }}" 
                                        class="time-input"
                                        required>
                                    <span class="input-icon">⏹</span>
                                </div>
                                @error('end_time')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- 勤務時間表示 -->
                        <div class="work-duration">
                            <span class="duration-label">勤務時間:</span>
                            <span class="duration-value" id="workDuration">--時間--分</span>
                        </div>
                    </div>

                    <!-- クイック時間設定 -->
                    <div class="quick-time-section">
                        <div class="section-title">
                            <span class="section-icon">⚡</span>
                            <span>クイック設定</span>
                        </div>
                        <div class="quick-buttons">
                            <button type="button" class="quick-btn" onclick="setTime('09:00', '18:00')">
                                <span>9:00 - 18:00</span>
                                <span class="quick-hours">(9時間)</span>
                            </button>
                            <button type="button" class="quick-btn" onclick="setTime('10:00', '19:00')">
                                <span>10:00 - 19:00</span>
                                <span class="quick-hours">(9時間)</span>
                            </button>
                            <button type="button" class="quick-btn" onclick="setTime('13:00', '22:00')">
                                <span>13:00 - 22:00</span>
                                <span class="quick-hours">(9時間)</span>
                            </button>
                            <button type="button" class="quick-btn" onclick="setTime('17:00', '22:00')">
                                <span>17:00 - 22:00</span>
                                <span class="quick-hours">(5時間)</span>
                            </button>
                        </div>
                    </div>

                    <!-- 変更履歴 -->
                    @if(old('start_time') || old('end_time'))
                    <div class="change-preview">
                        <div class="preview-title">変更内容</div>
                        <div class="preview-content">
                            <div class="preview-item">
                                <span class="preview-label">変更前:</span>
                                <span class="preview-old">{{ $shift->start_time }} 〜 {{ $shift->end_time }}</span>
                            </div>
                            <div class="preview-arrow">↓</div>
                            <div class="preview-item">
                                <span class="preview-label">変更後:</span>
                                <span class="preview-new">{{ old('start_time', $shift->start_time) }} 〜 {{ old('end_time', $shift->end_time) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- アクションボタン -->
                    <div class="form-actions">
                        <a href="{{ route('admin.fixed.index') }}" class="btn btn-cancel">
                            <span>キャンセル</span>
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <span class="btn-icon">✓</span>
                            <span>変更を保存</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- 通知 -->
        <div id="notification" class="notification"></div>
    </div>

    <script>
        // 勤務時間計算
        function calculateWorkDuration() {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            
            if (startTime && endTime) {
                const [startHour, startMin] = startTime.split(':').map(Number);
                const [endHour, endMin] = endTime.split(':').map(Number);
                
                let totalMinutes = (endHour * 60 + endMin) - (startHour * 60 + startMin);
                
                // 日をまたぐ場合
                if (totalMinutes < 0) {
                    totalMinutes += 24 * 60;
                }
                
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                
                document.getElementById('workDuration').textContent = `${hours}時間${minutes}分`;
                
                // 長時間勤務の警告
                if (hours > 10) {
                    document.getElementById('workDuration').style.color = '#ef4444';
                    showNotification('10時間を超える勤務です。休憩時間を確認してください。', 'warning');
                } else {
                    document.getElementById('workDuration').style.color = '#43e97b';
                }
            }
        }

        // クイック時間設定
        function setTime(start, end) {
            document.getElementById('startTime').value = start;
            document.getElementById('endTime').value = end;
            calculateWorkDuration();
            showNotification(`時間を ${start} - ${end} に設定しました`, 'info');
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

        // フォーム検証
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            
            if (!startTime || !endTime) {
                e.preventDefault();
                showNotification('開始時間と終了時間を入力してください', 'error');
                return;
            }
            
            // 確認ダイアログ
            const employeeName = '{{ $shift->user->name }}';
            const date = '{{ \Carbon\Carbon::parse($shift->date)->format("Y年m月d日") }}';
            
            if (!confirm(`${employeeName} さんの ${date} のシフトを\n${startTime} 〜 ${endTime} に変更しますか?`)) {
                e.preventDefault();
            }
        });

        // 時間入力時に勤務時間を計算
        document.getElementById('startTime').addEventListener('change', calculateWorkDuration);
        document.getElementById('endTime').addEventListener('change', calculateWorkDuration);

        // ページ読み込み時に勤務時間を計算
        window.addEventListener('load', function() {
            calculateWorkDuration();
            
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif
        });
    </script>
</body>
</html>