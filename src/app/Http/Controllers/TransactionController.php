<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Message;
use App\Models\Rating;
use App\Http\Requests\MessageRequest;

class TransactionController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);
        $hasRated = Rating::hasUserRated($id, $user->id);
        if( $transaction->seller_id !== $user->id && $transaction->buyer_id !== $user->id ) {
            abort(404);
        }
        $otherTransactions = Transaction::getOtherIncompleteTransactions($user->id,$transaction->id);
        $otherUser = User::where('id', ($transaction->seller_id === $user->id)? $transaction->buyer_id : $transaction->seller_id)->first();
        $messages = $transaction->messages()->orderBy('created_at', 'asc')->get();

        /* 未読メッセージを既読にする */
        $unreadMessages = Message::getUnreadMessages($id, $user->id);
        foreach ($unreadMessages as $message) {
            $message->markAsRead();
        }
        return view('transaction.transaction_detail', compact(
            'transaction',
            'otherTransactions',
            'otherUser',
            'messages',
            'hasRated'
        ));
    }

    public function store(MessageRequest $request, $id)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);

        if ($transaction->seller_id !== $user->id && $transaction->buyer_id !== $user->id) {
            abort(404);
        }

        $message = new Message();
        $message->transaction_id = $id;
        $message->user_id = $user->id;
        $message->content = $request->content;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = Storage::disk('public')->putFile('images/messages', $file);
            $message->image_url = basename($path);
        }
        $message->save();
        $transaction->touch();
        return redirect()->to('/transaction/' . $id);
    }

    public function complete($id)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);

        if ($transaction->buyer_id !== $user->id) {
            return abort(404);
        }

        if ($transaction->isCompleted()) {
            return abort(404);
        }

        $transaction->complete();
        return redirect()->to('/transaction/' . $id);
    }

    public function submitRating(Request $request, $id)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);

        if ($transaction->seller_id !== $user->id && $transaction->buyer_id !== $user->id) {
            abort(404);
        }

        /* 取引完了していなければ戻る */
        if (!$transaction->isCompleted()) {
            return redirect()->to('/transaction/' . $id);
        }

        /* 評価完了していれば商品一覧へ */
        if (Rating::hasUserRated($id, $user->id)) {
            return redirect('/mypage?tab=transaction');
        }

        $ratedUserId = ($user->id === $transaction->seller_id)
            ? $transaction->buyer_id
            : $transaction->seller_id;

        /* 評価情報保存 */
        $rating = new Rating();
        $rating->transaction_id = $id;
        $rating->rater_id = $user->id;
        $rating->rated_user_id = $ratedUserId;
        $rating->score = $request->score;
        $rating->save();
        return redirect('/mypage?tab=transaction');
    }
}
