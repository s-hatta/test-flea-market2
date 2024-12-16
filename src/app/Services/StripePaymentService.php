<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use App\Models\Item;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    public function createCheckoutSession(Item $item, string $paymentMethod, string $successUrl, string $cancelUrl)
    {
        $lineItems = [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->name,
                    'images' => [$item->img_url ? url('storage/images/items/' . $item->img_url) : null],
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => 1,
        ]];

        $paymentMethodTypes = [];
        if ($paymentMethod === 'card') {
            $paymentMethodTypes[] = 'card';
        } elseif ($paymentMethod === 'cvs') {
            $paymentMethodTypes[] = 'konbini';
        }

        return Session::create([
            'payment_method_types' => $paymentMethodTypes,
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'item_id' => $item->id,
            ],
        ]);
    }

    public function handleWebhook($payload, $sigHeader)
    {
        $endpoint_secret = config('stripe.webhook_secret');
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return false;
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return false;
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $order = Order::where('stripe_session_id', $session->id)->first();
            
            if ($order) {
                $order->payment_status = 'paid';
                $order->save();
                
                /* 商品の数量を0にする */
                $item = Item::find($order->item_id);
                if ($item) {
                    $item->stock = 0;
                    $item->save();
                }
            }
        }
        return true;
    }
}