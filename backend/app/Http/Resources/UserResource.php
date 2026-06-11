<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->uuid,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'avatar'            => $this->avatar,
            'status'            => $this->status,
            'email_verified'    => !is_null($this->email_verified_at),
            'phone_verified'    => !is_null($this->phone_verified_at),
            'last_login_at'     => $this->last_login_at?->toIso8601String(),
            'roles'             => $this->whenLoaded('roles', fn() =>
                $this->roles->pluck('slug')
            ),
            'profile' => $this->whenLoaded('customerProfile', fn() => [
                'gender'         => $this->customerProfile->gender,
                'birth_date'     => $this->customerProfile->birth_date?->format('Y-m-d'),
                'loyalty_points' => $this->customerProfile->loyalty_points,
                'loyalty_tier'   => $this->customerProfile->loyalty_tier,
                'total_spent'    => $this->customerProfile->total_spent,
                'referral_code'  => $this->customerProfile->referral_code,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
