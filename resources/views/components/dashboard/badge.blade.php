@props(['status'])
@php
    $map = [
        'pending_payment' => ['Menunggu Pembayaran', 'bg-amber-50 text-amber-700 border-amber-200'],
        'confirmed' => ['Dikonfirmasi', 'bg-blue-50 text-blue-700 border-blue-200'],
        'processing' => ['Diproses', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'shipped' => ['Dikirim', 'bg-purple-50 text-purple-700 border-purple-200'],
        'completed' => ['Selesai', 'bg-green-50 text-green-700 border-green-200'],
        'cancelled' => ['Dibatalkan', 'bg-gray-100 text-gray-600 border-gray-200'],
        'refunded' => ['Direfund', 'bg-orange-50 text-orange-700 border-orange-200'],
        'failed_delivery' => ['Gagal Kirim', 'bg-red-50 text-red-700 border-red-200'],
        'returned' => ['Dikembalikan', 'bg-rose-50 text-rose-700 border-rose-200'],
    ];
    [$label, $class] = $map[$status] ?? [ ucfirst(str_replace('_', ' ', $status)), 'bg-gray-100 text-gray-600 border-gray-200' ];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $class"]) }}>{{ $label }}</span>
