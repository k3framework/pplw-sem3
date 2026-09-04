<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ReservationRequest extends ScheduleRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'time_slot_id' => ['required', 'integer', Rule::exists('time_slots', 'id')->where('is_active', true)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
