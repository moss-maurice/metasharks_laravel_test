<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface RoomServiceInterface
{
    public function list(Request $request): JsonResponse;
    public function order(Request $request): JsonResponse;
}
