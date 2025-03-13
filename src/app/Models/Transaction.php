<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * 取引クエリに新規メッセージが来た順のソートを適用する
     */
    public static function applySortByLatestReceivedMessage($query, int $userId)
    {
        return $query->select('transactions.*')
            ->leftJoin(DB::raw('(SELECT transaction_id, MAX(created_at) as last_message_at FROM messages WHERE user_id != '.$userId.' GROUP BY transaction_id) as latest_messages'), 
                'transactions.id', '=', 'latest_messages.transaction_id')
            ->orderByDesc('latest_messages.last_message_at')
            ->orderByDesc('transactions.created_at'); /* メッセージがない取引は作成日時でソート */
    }
    
    /**
     * ユーザーが保持している未完了の取引を取得する
     */
    public static function getOtherIncompleteTransactions(int $userId, int $transactionId)
    {
        $query = Transaction::where(function($query) use($userId) {
            $query->where('seller_id', $userId)
                ->orWhere('buyer_id', $userId);
        })
        ->where('id', '!=', $transactionId)
        ->where(function($query) use($userId) {
            $query->where('status', Transaction::STATUS_IN_PROGRESS)
                ->orWhere(function($q) use($userId) {
                    $q->where('status', Transaction::STATUS_COMPLETED)
                        ->whereDoesntHave('ratings', function($rating) use($userId) {
                            $rating->where('rater_id', $userId);
                        });
                });
        });
        return self::applySortByLatestReceivedMessage($query, $userId)
            ->with(['item', 'messages'])
            ->get();
    }
    
    /**
     * ユーザーが関与している取引を取得する
     */
    public static function getUserIncompleteTransactions(int $userId)
    {
        $query = Transaction::where(function($query) use ($userId) {
            $query->where('seller_id', $userId)
                ->orWhere('buyer_id', $userId);
        })
        ->where(function($query) use ($userId) {
            $query->where('status', Transaction::STATUS_IN_PROGRESS)
                ->orWhere(function($query) use ($userId) {
                    $query->where('status', Transaction::STATUS_COMPLETED)
                        ->whereDoesntHave('ratings', function($rating) use ($userId) {
                            $rating->where('rater_id', $userId);
                        });
                });
        });
        
        return self::applySortByLatestReceivedMessage($query, $userId)->get();
    }
    
    /**
     * ユーザーが関与している取引で未読メッセージがある取引の数を取得する
     */
    public static function getUnreadTransactionsCount($userId)
    {
        return self::where(function($query) use ($userId) {
            $query->where('seller_id', $userId)
                ->orWhere('buyer_id', $userId);
        })
        ->whereHas('messages', function($query) use ($userId) {
            $query->where('user_id', '!=', $userId)
                ->where('is_read', false);
        })
        ->count();
    }
}
