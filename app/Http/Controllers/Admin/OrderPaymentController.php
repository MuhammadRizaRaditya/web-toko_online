<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderPaymentController extends Controller
{
    public function pendingTransfers(): View
    {
        $orders = Order::query()
            ->where('channel', 'online')
            ->whereHas('payment', fn ($query) => $query
                ->where('method', 'transfer')
                ->where('status', 'pending'))
            ->with(['payment', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.transfers', compact('orders'));
    }

    public function verifyTransfer(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedOrder->channel !== 'online'
                || $lockedOrder->order_status !== 'pending_payment'
                || $payment->method !== 'transfer'
                || $payment->status !== 'pending'
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran ini tidak lagi menunggu verifikasi transfer.',
                ]);
            }

            $payment->update([
                'status' => 'paid',
                'confirmed_at' => now(),
            ]);
            $lockedOrder->transitionTo('confirmed', auth()->id(), 'Transfer manual diverifikasi oleh admin.');
        });

        return redirect()
            ->route('admin.payments.transfers.index')
            ->with('success', "Pembayaran pesanan {$order->order_number} berhasil diverifikasi.");
    }
}
