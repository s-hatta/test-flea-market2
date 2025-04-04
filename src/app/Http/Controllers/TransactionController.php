<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Message;
use App\Models\Rating;
use App\Http\Requests\MessageRequest;
use App\Mail\TransactionCompletedMail;

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

    public function update(MessageRequest $request, $id, $messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);

        if( $message->user_id !== $user->id ) {
            abort(403);
        }
        if( $message->transaction_id != $id ) {
            abort(404);
        }

        /* メッセージ内容更新 */
        $message->content = $request->content;
        $message->save();

        return redirect()->to('/transaction/' . $id);
    }

    public function delete(Request $request, $id, $messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== $user->id) {
            abort(403);
        }
        if ($message->transaction_id != $id) {
            abort(404);
        }

        /* 削除実行 */
        if ($message->image_url) {
            Storage::disk('public')->delete('images/messages/' . $message->image_url);
        }
        $message->delete();

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

        /* 出品者にメールを送信 */
        $seller = User::findOrFail($transaction->seller_id);
        Mail::to($seller->email)->send(new TransactionCompletedMail($transaction, $user));

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
            return redirect('/');
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
        return redirect('/');
    }
}
