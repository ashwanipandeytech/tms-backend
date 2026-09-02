<?php

declare(strict_types=1);

namespace App\Http\Requests;

class SubscriptionPlanStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:100',
            'slug'             => 'required|string|max:100|unique:subscription_plans,slug',
            'monthly_price'    => 'required|numeric|min:0',
            'yearly_price'     => 'required|numeric|min:0',
            'base_user_seats'  => 'required|integer|min:1',
            'addon_seat_price' => 'nullable|numeric|min:0',
            'modules'          => 'nullable|array',
            'database_type'    => 'nullable|string|in:shared,dedicated',
            'status'           => 'nullable|string|in:active,inactive',
        ];
    }
}
