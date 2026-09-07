@props(['reservation'])
<span class="status {{ in_array($reservation->status, ['pending', 'confirmed']) ? $reservation->status : 'neutral' }}">{{ $reservation->statusLabel() }}</span>

