<?php

namespace App\Services;

use App\Contracts\NotifyServiceInterface;
use App\Contracts\RoomServiceInterface;
use App\Models\RoomOrders;
use App\Models\Rooms;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class RoomService implements RoomServiceInterface
{
    public function __construct(
        private readonly NotifyServiceInterface $notifyService,
        private readonly LoggerInterface $logger
    ) {}

    public function list(Request $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $from = $validated['from'] ?? now()->toDateString();
            $to = $validated['to'] ?? now()->addWeek()->toDateString();
            $perPage = $validated['per_page'] ?? 15;
            $page = $validated['page'] ?? 1;

            $cacheKey = collect([
                'rooms' => 'available',
                'from' => $from,
                'to' => $to,
                'page' => $page,
                'perPage' => $perPage,
            ])
                ->map(function ($value, $key) {
                    return "$key:$value";
                })
                ->implode(';');

            $rooms = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($from, $to, $page, $perPage) {
                return Rooms::leftJoin('room_orders', function ($join) use ($from, $to) {
                    $join->on('rooms.id', '=', 'room_orders.room_id')
                        ->whereBetween('room_orders.date', [$from, $to]);
                })
                    ->whereNull('room_orders.room_id')
                    ->select('rooms.*')
                    ->paginate($perPage, ['*'], 'page', $page)
                    ->through(function ($item) {
                        return $item->makeHidden(['created_at', 'updated_at']);
                    });
            });

            return response()->json($rooms);
        } catch (Exception $exception) {
            $this->logger->error('Room selection failed: ' . $exception->getMessage() . ' (code: ' . $exception->getCode() . ')');

            return response()->json(['message' => $exception->getMessage()], $exception->getCode() >= JsonResponse::HTTP_BAD_REQUEST ? ($exception->getCode() > JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED ? JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED : $exception->getCode()) : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function order(Request $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $alreadyOrdered = RoomOrders::where('room_id', $validated['room_id'])
                ->where('date', $validated['date'])
                ->exists();

            if ($alreadyOrdered) {
                throw new Exception(__('messages.room.alreadyOrdered'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $roomOrder = RoomOrders::create([
                'user_id' => $request->user()->id,
                'room_id' => $validated['room_id'],
                'date' => $validated['date'],
            ]);

            if (!$roomOrder) {
                throw new Exception(__('messages.room.failedCreateOrder'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();

            $this->notifyService->notify($request->user()->email, __('mail.notify.subject'), __('mail.notify.body'));

            Cache::flush();

            return response()->json($roomOrder->load(['room' => function ($query) {
                $query->select('id', 'title', 'description');
            }])
                ->makeHidden(['room_id', 'user_id', 'created_at', 'updated_at']), JsonResponse::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();

            $this->logger->error('Room order failed: ' . $exception->getMessage() . ' (code: ' . $exception->getCode() . ')');

            return response()->json(['message' => $exception->getMessage()], $exception->getCode() >= JsonResponse::HTTP_BAD_REQUEST ? ($exception->getCode() > JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED ? JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED : $exception->getCode()) : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
