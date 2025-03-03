<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'content',
        'image_url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 画像ファイルを持っているか
     */
    public function hasImage(): bool
    {
        return !is_null($this->image_url);
    }

    /**
     * 既読状態にする
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->is_read = true;
            return $this->save();
        }
        return true;
    }

    /**
     * 未読メッセージを取得する
     */
    public static function getUnreadMessages(int $transactionId, int $userId)
    {
        return self::where('transaction_id', $transactionId)
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->get();
    }

    /**
     * すべての未読メッセージの数を取得する
     */
    public static function getTotalUnreadCount(int $userId)
    {
        return self::whereHas('transaction', function ($query) use ($userId) {
            $query->where('seller_id', $userId)
                ->orWhere('buyer_id', $userId);
        })
        ->where('user_id', '!=', $userId)
        ->where('is_read', false)
        ->count();
    }
}
