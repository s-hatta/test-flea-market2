<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'item_id',
        'seller_id',
        'buyer_id',
        'status',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'completed_at' => 'datetime',
    ];

    const STATUS_IN_PROGRESS = 0;
    const STATUS_COMPLETED = 1;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * 未読メッセージを取得
     */
    public function unreadMessagesCount( int $userId ): int
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    /**
     * 取引が完了状態か
     */
    public function isCompleted(): bool
    {
        return (int)$this->status === self::STATUS_COMPLETED;
    }

    /**
     * 現在の取引状態に合わせた文字列を取得
     */
    public function getStatusLabel(): string
    {
        switch ((int)$this->status) {
            case self::STATUS_IN_PROGRESS:
                return '取引中';
            case self::STATUS_COMPLETED:
                return '完了';
            default:
                return '不明';
        }
    }

    /**
     * 取引を完了状態にする
     */
    public function complete(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        return $this->save();
    }

    /**
     * 取引の相手を取得する
     */
    public function getOtherUser(int $userId): User
    {
        return ($this->seller_id === $userId)
            ? $this->buyer
            : $this->seller;
    }
}
