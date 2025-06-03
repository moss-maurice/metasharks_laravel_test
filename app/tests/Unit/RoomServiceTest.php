<?php

namespace Tests\Unit;

use App\Contracts\NotifyServiceInterface;
use App\Http\Requests\StoreRoomOrderRequest;
use App\Models\RoomOrders;
use App\Models\Rooms;
use App\Models\User;
use App\Services\RoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class RoomServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoomService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RoomService(
            $this->createMock(NotifyServiceInterface::class),
            $this->createMock(LoggerInterface::class)
        );
    }

    public function test_successful_room_order()
    {
        $user = User::factory()->create();
        $room = Rooms::factory()->create();

        $request = $this->createValidRequest([
            'room_id' => $room->id,
            'date' => now()->addDay()->toDateString(),
        ], $user);

        $response = $this->service->order($request);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->status());
        $this->assertJsonStructure($response, ['id', 'date', 'room' => ['id', 'title', 'description']]);
    }

    public function test_fails_when_validation_errors()
    {
        $user = User::factory()->create();

        $request = $this->createInvalidRequest([], $user);

        $response = $this->service->order($request);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->status());
        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }

    public function test_fails_when_invalid_date_format()
    {
        $user = User::factory()->create();
        $room = Rooms::factory()->create();

        $request = $this->createValidRequest([
            'room_id' => $room->id,
            'date' => 'invalid-date-format',
        ], $user);

        $response = $this->service->order($request);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->status());
        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }

    public function test_fails_when_room_already_ordered()
    {
        $user = User::factory()->create();
        $room = Rooms::factory()->create();

        $date = now()->addDay();

        RoomOrders::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'date' => $date->toDateString(),
        ]);

        $request = $this->createValidRequest([
            'room_id' => $room->id,
            'date' => $date->toDateString(),
        ], $user);

        $response = $this->service->order($request);

        $this->assertEquals(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->status());
        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }

    private function createValidRequest(array $data, User $user): StoreRoomOrderRequest
    {
        $request = new StoreRoomOrderRequest;

        $request->merge($data);
        $request->setUserResolver(fn() => $user);

        $validator = Validator::make($data, $request->rules());

        $request->setValidator($validator);

        return $request;
    }

    private function createInvalidRequest(array $data, User $user): StoreRoomOrderRequest
    {
        $request = new StoreRoomOrderRequest;

        $request->merge($data);
        $request->setUserResolver(fn() => $user);

        $validator = Validator::make([], $request->rules());

        $validator->errors()->add('room_id', 'The room_id field is required.');
        $validator->errors()->add('date', 'The date field is required.');

        $request->setValidator($validator);

        return $request;
    }

    private function assertJsonStructure($response, array $structure): void
    {
        $responseData = json_decode($response->getContent(), true);

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                $this->assertArrayHasKey($key, $responseData);
                foreach ($value as $nestedKey) {
                    $this->assertArrayHasKey($nestedKey, $responseData[$key]);
                }
            } else {
                $this->assertArrayHasKey($value, $responseData);
            }
        }
    }
}
