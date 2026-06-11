<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\CustomerAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreAddressRequest;
use App\Http\Requests\User\UpdateAddressRequest;

class AddressController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();
        return $this->success($addresses);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['customer_id'] = $request->user()->id;

        if (!empty($data['is_default'])) {
            $request->user()->addresses()
                ->where('address_type', $data['address_type'])
                ->update(['is_default' => false]);
        }

        $address = CustomerAddress::create($data);
        return $this->created($address);
    }

    public function show(CustomerAddress $address, Request $request): JsonResponse
    {
        $this->authorizeAddress($address, $request);
        return $this->success($address);
    }

    public function update(UpdateAddressRequest $request, CustomerAddress $address): JsonResponse
    {
        $this->authorizeAddress($address, $request);

        $data = $request->validated();

        $address->update($data);
        return $this->success($address->fresh());
    }

    public function destroy(CustomerAddress $address, Request $request): JsonResponse
    {
        $this->authorizeAddress($address, $request);
        $address->delete();
        return $this->noContent('Address deleted.');
    }

    public function setDefault(CustomerAddress $address, Request $request): JsonResponse
    {
        $this->authorizeAddress($address, $request);

        $request->user()->addresses()
            ->where('address_type', $address->address_type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);
        return $this->success($address->fresh());
    }

    private function authorizeAddress(CustomerAddress $address, Request $request): void
    {
        if ($address->customer_id !== $request->user()->id) {
            abort(403, 'Access denied.');
        }
    }
}
