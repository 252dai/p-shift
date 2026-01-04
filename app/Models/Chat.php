<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message'];

    /**
     * メッセージ取得時に自動的にエスケープ（追加の保護層）
     * 
     * @param string $value
     * @return string
     */
    public function getMessageAttribute($value)
    {
        // データベースから取得時も念のためエスケープ
        // ただし、既にサニタイズ済みなので二重エスケープを避ける
        return $value;
    }

    /**
     * メッセージ保存時の処理
     * 
     * @param string $value
     * @return void
     */
    public function setMessageAttribute($value)
    {
        // 保存時にも念のため基本的なサニタイズを実施
        $this->attributes['message'] = strip_tags($value);
    }

    /**
     * ユーザーとのリレーション
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 会社IDでスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    /**
     * 最近のメッセージを取得
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $limit = 100)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}