<?php

namespace App\Carrier\Http\Apis;

use App\Carrier\Models\Carrier as Model;
use App\Carrier\Values\USDOT;
use App\Http\Controllers\Controller;
use App\Store\Values\UUID;
use Illuminate\Contracts\Pagination\Paginator;

class Carrier extends Controller
{
    public function index(): Paginator
    {
        return Model::paginate();
    }

    public function show($id): Model
    {
        return Model::findOrFail($id);
    }

    public function showByUSDOT($usdot): Model
    {
        return Model::usdot(new USDOT($usdot))
            ->firstOrFail();
    }

    public function showByUUID($uuid): Model
    {
        return Model::uuid(new UUID($uuid))
            ->firstOrFail();
    }
}
