@php
    $totalFee = (float) ($get('total_delivery_fee') ?? 0);
    $allocations = $get('order_allocations') ?? [];
    $allocatedFee = 0;

    if (is_array($allocations)) {
        foreach ($allocations as $alloc) {
            $allocatedFee += (float) ($alloc['assigned_fee'] ?? 0);
        }
    }

    $remaining = round($totalFee - $allocatedFee, 2);
    $customerCount = is_array($allocations) ? count($allocations) : 0;
@endphp

<div class="p-4 rounded-2xl border transition-all shadow-xs space-y-3 {{ $remaining === 0.0 ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800' : ($remaining < 0 ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800') }}">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2">
            <span class="text-xl">{{ $remaining === 0.0 ? '🎉' : ($remaining < 0 ? '⚠️' : '📊') }}</span>
            <h4 class="font-extrabold text-sm {{ $remaining === 0.0 ? 'text-emerald-950 dark:text-emerald-200' : ($remaining < 0 ? 'text-rose-950 dark:text-rose-200' : 'text-amber-950 dark:text-amber-200') }}">
                Delivery Pool Financial Allocation Summary
            </h4>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $remaining === 0.0 ? 'bg-emerald-700 text-white' : ($remaining < 0 ? 'bg-rose-700 text-white' : 'bg-amber-700 text-white') }}">
            {{ $remaining === 0.0 ? '✓ Balanced (&#8369;0.00)' : ($remaining < 0 ? 'Overallocated' : '&#8369;' . number_format($remaining, 2) . ' Remaining') }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
        <div class="p-3 rounded-xl bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800">
            <span class="text-gray-500 dark:text-gray-400 text-[11px] block">Total Lalamove Route Fee:</span>
            <span class="font-black text-base text-gray-900 dark:text-gray-100">&#8369;{{ number_format($totalFee, 2) }}</span>
        </div>

        <div class="p-3 rounded-xl bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800">
            <span class="text-gray-500 dark:text-gray-400 text-[11px] block">Total Allocated to Customers ({{ $customerCount }}):</span>
            <span class="font-black text-base text-emerald-600 dark:text-emerald-400">&#8369;{{ number_format($allocatedFee, 2) }}</span>
        </div>

        <div class="p-3 rounded-xl bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800">
            <span class="text-gray-500 dark:text-gray-400 text-[11px] block">Remaining Unsettled Shipping:</span>
            <span class="font-black text-base {{ $remaining === 0.0 ? 'text-emerald-600 dark:text-emerald-400' : ($remaining < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                &#8369;{{ number_format($remaining, 2) }}
            </span>
        </div>
    </div>

    <p class="text-[11px] leading-relaxed {{ $remaining === 0.0 ? 'text-emerald-900 dark:text-emerald-300' : ($remaining < 0 ? 'text-rose-900 dark:text-rose-300' : 'text-amber-900 dark:text-amber-300') }}">
        @if($remaining === 0.0)
            ✓ Excellent! The total route fee of &#8369;{{ number_format($totalFee, 2) }} is 100% covered across {{ $customerCount }} pooled customer orders.
        @elseif($remaining > 0)
            ⏳ Every time you assign a shipping fee to a customer below, the remaining balance auto-deducts here. <strong>&#8369;{{ number_format($remaining, 2) }}</strong> still needs to be assigned to reach the total route fee.
        @else
            ⚠️ The total customer fees exceed the route fee by &#8369;{{ number_format(abs($remaining), 2) }}. Adjust customer fees below to balance the pool.
        @endif
    </p>
</div>
