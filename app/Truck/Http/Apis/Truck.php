<?php

namespace App\Truck\Http\Apis;

use App\Http\Controllers\Controller;
use App\Store\Values\UUID;
use App\Truck\Models\Truck as Model;
use App\Truck\Values\VIN;
use Illuminate\Contracts\Pagination\Paginator;

class Truck extends Controller
{
    public function index(): Paginator
    {
        return Model::paginate();
    }

    public function show($id): Model
    {
        return Model::findOrFail($id);
    }

    public function showByVIN(string $vin): Model
    {
        return Model::vin(new VIN($vin))
            ->firstOrFail();
    }

    public function showByUUID($uuid): Model
    {
        return Model::uuid(new UUID($uuid))
            ->firstOrFail();
    }
}
