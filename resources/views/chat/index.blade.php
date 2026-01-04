<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャット - p-shift</title>
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- サイドバー -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📅</span>
                <span class="logo-text">p-shift</span>
            </div>
            <div class="user-badge">{{ Auth::user()->role === 'admin' ? '管理者' : '従業員' }}</div>
        </div>

        <div class="info-section">
            <p class="section-title">チャットについて</p>
            <div class="info-card">
                <div class="info-item">
                    <span class="info-icon">💬</span>
                    <span class="info-text">会社全体で情報共有</span>
                </div>
                <div class="info-item">
                    <span class="info-icon">⏰</span>
                    <span class="info-text">リアルタイムで確認</span>
                </div>
                <div class="info-item">
                    <span class="info-icon">🔄</span>
                    <span class="info-text">更新ボタンで最新表示</span>
                </div>
            </div>
        </div>

        <div class="sidebar-footer">
            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="back-btn">
                <span>← ダッシュボードへ</span>
            </a>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content">
        <div class="chat-container">
            <header class="chat-header">
                <div class="chat-header-info">
                    <h1 class="chat-title">💬 全体チャット</h1>
                    <p class="chat-subtitle">会社全体で情報を共有できます</p>
                </div>
                <button class="refresh-btn" onclick="location.reload()">
                    <span>🔄</span>
                    <span>更新</span>
                </button>
            </header>

            <div class="messages-wrapper">
                <div class="messages-container" id="messagesContainer">
                    @forelse ($chats as $chat)
                        <div class="message {{ $chat->user_id === Auth::id() ? 'message-own' : 'message-other' }}">
                            <div class="message-avatar">{{ mb_substr($chat->user->name, 0, 1) }}</div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-sender">{{ e($chat->user->name) }}</span>
                                    <span class="message-time">{{ $chat->created_at->format('Y/m/d H:i') }}</span>
                                </div>
                                <div class="message-text">{!! nl2br(e($chat->message)) !!}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-chat">
                            <div class="empty-icon">💬</div>
                            <p>まだメッセージがありません</p>
                            <p class="empty-hint">最初のメッセージを送信してみましょう</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="message-input-area">
                <form method="POST" action="{{ route('chat.store') }}" class="message-form" id="messageForm">
                    @csrf
                    <textarea 
                        name="message" 
                        class="message-input" 
                        placeholder="メッセージを入力... (Shift + Enter で送信)"
                        rows="1"
                        maxlength="1000"
                        required></textarea>
                    <button type="submit" class="send-btn">
                        <span class="send-icon">📤</span>
                        <span>送信</span>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // メッセージを最下部にスクロール
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        // ページ読み込み時にスクロール
        window.addEventListener('load', scrollToBottom);

        // テキストエリアの自動リサイズ
        const messageInput = document.querySelector('.message-input');
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Shift + Enter で送信
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.shiftKey) {
                    e.preventDefault();
                    this.closest('form').requestSubmit();
                }
            });
        }

        // フォーム送信時にスクロール
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                const input = this.querySelector('.message-input');
                const message = input.value.trim();
                
                // 空メッセージのチェック
                if (!message) {
                    e.preventDefault();
                    alert('メッセージを入力してください');
                    return;
                }
                
                // XSS対策：危険な文字列のチェック（追加の保護層）
                const dangerousPatterns = [
                    /<script/i,
                    /javascript:/i,
                    /on\w+\s*=/i,
                    /<iframe/i,
                    /<object/i,
                    /<embed/i
                ];
                
                for (let pattern of dangerousPatterns) {
                    if (pattern.test(message)) {
                        e.preventDefault();
                        alert('不正な文字列が含まれています');
                        return;
                    }
                }
                
                setTimeout(scrollToBottom, 100);
            });
        }
    </script>
</body>
</html>