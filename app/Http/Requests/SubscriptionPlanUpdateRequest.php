<?php

declare(strict_types=1);

namespace App\Http\Requests;

class SubscriptionPlanUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        $planId = $this->route('plan') ?? $this->route('id');

        return [
            'name'             => 'sometimes|required|string|max:100',
            'slug'             => 'sometimes|required|string|max:100|unique:subscription_plans,slug,' . $planId,
            'monthly_price'    => 'sometimes|required|numeric|min:0',
            'yearly_price'     => 'sometimes|required|numeric|min:0',
            'base_user_seats'  => 'sometimes|required|integer|min:1',
            'addon_seat_price' => 'nullable|numeric|min:0',
            'modules'          => 'nullable|array',
            'database_type'    => 'nullable|string|in:shared,dedicated',
            'status'           => 'nullable|string|in:active,inactive',
        ];
    }
}
