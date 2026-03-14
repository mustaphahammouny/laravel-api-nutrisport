<x-mail::message>
# Order #{{ $order->id }} Confirmed

Hello {{ $user->name ?? $order->customer?->name ?? 'Customer' }},

Your order has been created successfully.

Order ID: #{{ $order->id }}

Status: {{ $order->status?->label() ?? 'N/A' }}

Payment method: {{ $order->payment_method?->label() ?? 'N/A' }}

Shipping address: {{ $order->shipping_full_address }}, {{ $order->shipping_city }}, {{ $order->shipping_country }}

<x-mail::table>
| Product | Unit Price | Quantity | Line Total |
| :-----: | :--------: | :-----: | :--------: |
@foreach ($order->items as $item)
| {{ $item->product_name_snapshot }} | {{ number_format((float) $item->unit_price, 2) }} | {{ $item->quantity }} | {{ number_format((float) $item->line_total, 2) }} |
@endforeach
</x-mail::table>

**Total:** {{ number_format((float) $order->total, 2) }}

**Paid amount:** {{ number_format((float) $order->paid_amount, 2) }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
