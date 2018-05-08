<?php

namespace App\Truck\Http\Apis;

use App\Carrier\Values\USDOT;
use App\Http\Controllers\Controller;
use App\Truck\Models\Fleet as Model;
use App\Truck\Values\VIN;
use Illuminate\Contracts\Pagination\Paginator;

class Fleet extends Controller
{
    public function index(): Paginator
    {
        return Model::paginate();
    }

    public function show($id): Model
    {
        return Model::findOrFail($id);
    }

    public function showByVIN($vin): Model
    {
        return Model::vin(new VIN($vin))
            ->firstOrFail();
    }

    public function showByUSDOT($usdot): Model
    {
        return Model::usdot(new USDOT((int) $usdot))
            ->firstOrFail();
    }

    public function showByUUID($uuid): Model
    {
        return Model::where('truck_uuid', $uuid)
            ->orWhere('carrier_uuid', $uuid)
            ->firstOrFail();
    }
}
