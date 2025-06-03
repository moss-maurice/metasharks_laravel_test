<?php

namespace App\Http\Controllers;

use App\Contracts\RoomServiceInterface;
use App\Http\Requests\RoomListRequest;

class RoomController extends Controller
{
    public function index(RoomListRequest $request, RoomServiceInterface $service)
    {
        return $service->list($request);
    }
}
