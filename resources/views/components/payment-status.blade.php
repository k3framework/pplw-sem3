@props(['payment'])
<span class="status {{ $payment->status === 'paid' ? 'confirmed' : 'refunded' }}">{{ $payment->statusLabel() }}</span>
