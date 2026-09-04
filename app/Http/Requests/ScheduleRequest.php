<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $today = now(config('restaurant.timezone'))->startOfDay();

        return [
            'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addDays(config('restaurant.max_days_ahead'))->toDateString()],
            'guest_count' => ['required', 'integer', 'between:1,6'],
        ];
    }
}
