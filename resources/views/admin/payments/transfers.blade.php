<x-admin.layout title="Verifikasi Transfer">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Verifikasi Transfer Manual</h2>
            <p class="mt-1 text-sm text-gray-500">Cocokkan pembayaran dengan bukti atau mutasi rekening toko sebelum mengonfirmasi pesanan.</p>
        </div>

        @if (session('success'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if ($orders->isEmpty())
                <p class="p-8 text-center text-sm text-gray-500">Tidak ada transfer yang menunggu verifikasi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th scope="col" class="px-5 py-3">Pesanan</th>
                                <th scope="col" class="px-5 py-3">Penerima</th>
                                <th scope="col" class="px-5 py-3">Tanggal</th>
                                <th scope="col" class="px-5 py-3 text-right">Jumlah</th>
                                <th scope="col" class="px-5 py-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-5 py-4">
                                        <span class="block text-gray-900">{{ $order->recipient_name }}</span>
                                        <span class="block text-xs text-gray-500">{{ $order->recipient_phone }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">Rp {{ number_format((float) $order->payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.payments.transfers.verify', $order) }}" onsubmit="return confirm('Pastikan pembayaran sudah diterima sebelum mengonfirmasi pesanan ini. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-green-700 px-3 py-2 text-xs font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-700 focus:ring-offset-2">Konfirmasi pembayaran</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-4">{{ $orders->links() }}</div>
            @endif
        </section>
    </div>
</x-admin.layout>
