<?php

namespace App\Http\Controllers;

use App\Contracts\RoomServiceInterface;
use App\Http\Requests\StoreRoomOrderRequest;

class RoomOrderController extends Controller
{
    public function store(StoreRoomOrderRequest $request, RoomServiceInterface $service)
    {
        return $service->order($request);
    }
}
