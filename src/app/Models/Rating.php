<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'transaction_id',
        'rater_id',
        'rated_user_id',
        'score',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    /**
     * ユーザーの平均評価を取得する
     */
    public static function getUserAverageRating(int $userId): float
    {
        $ratings = self::where('rated_user_id', $userId)->get();
        if ($ratings->isEmpty()) {
            return 0;
        }
        return round($ratings->avg('score'), 1);
    }

    /**
     * その取引でユーザーが評価済みかを取得する
     */
    public static function hasUserRated(int $transactionId, int $raterId): bool
    {
        return self::where('transaction_id', $transactionId)
            ->where('rater_id', $raterId)
            ->exists();
    }
}
