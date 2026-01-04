<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * チャット一覧表示
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // 同じ会社IDのユーザーのチャットだけ取得
        $chats = Chat::with('user')
            ->whereHas('user', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('created_at', 'asc') // 古い順に変更（チャットは通常古い順）
            ->get();

        return view('chat.index', compact('chats'));
    }

    /**
     * メッセージ投稿（XSS対策済み）
     */
    public function store(Request $request)
    {
        try {
            // バリデーション（XSS対策を含む）
            $validated = $request->validate([
                'message' => [
                    'required',
                    'string',
                    'max:1000',
                    // XSS対策：危険なタグやスクリプトを禁止
                    'regex:/^(?!.*<script)(?!.*<\/script>)(?!.*javascript:)(?!.*on\w+\s*=)(?!.*<iframe)(?!.*<object)(?!.*<embed).*$/is'
                ]
            ], [
                'message.required' => 'メッセージを入力してください',
                'message.max' => 'メッセージは1000文字以内で入力してください',
                'message.regex' => '不正な文字列が含まれています。HTMLタグやスクリプトは使用できません。'
            ]);

            // サニタイズ（追加の保護層）
            $sanitizedMessage = $this->sanitizeMessage($validated['message']);

            // 空白のみのメッセージをチェック
            if (trim($sanitizedMessage) === '') {
                return redirect()->route('chat.index')->with('error', 'メッセージを入力してください');
            }

            // チャット保存
            Chat::create([
                'user_id' => Auth::id(),
                'message' => $sanitizedMessage,
            ]);

            Log::info('チャットメッセージ投稿', [
                'user_id' => Auth::id(),
                'message_length' => mb_strlen($sanitizedMessage)
            ]);

            return redirect()->route('chat.index')->with('success', 'メッセージを送信しました');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('chat.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('チャット投稿エラー', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->route('chat.index')->with('error', 'メッセージの送信に失敗しました');
        }
    }

    /**
     * メッセージのサニタイズ処理
     * 
     * @param string $message
     * @return string
     */
    private function sanitizeMessage($message)
    {
        // HTMLタグを完全に除去
        $sanitized = strip_tags($message);
        
        // 特殊文字をHTMLエンティティに変換
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 連続する空白を1つにまとめる（改行は保持）
        $sanitized = preg_replace('/[^\S\r\n]+/', ' ', $sanitized);
        
        // 前後の空白を削除
        $sanitized = trim($sanitized);
        
        return $sanitized;
    }

    /**
     * 危険なパターンをチェック（追加のセキュリティ層）
     * 
     * @param string $message
     * @return bool
     */
    private function containsDangerousPatterns($message)
    {
        $dangerousPatterns = [
            '/<script[\s\S]*?>[\s\S]*?<\/script>/i',
            '/javascript:/i',
            '/on(load|error|click|mouse\w+|key\w+)\s*=/i',
            '/<iframe[\s\S]*?>/i',
            '/<object[\s\S]*?>/i',
            '/<embed[\s\S]*?>/i',
            '/<applet[\s\S]*?>/i',
            '/eval\s*\(/i',
            '/expression\s*\(/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }
}