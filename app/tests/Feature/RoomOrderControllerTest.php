<?php

namespace Tests\Feature;

use App\Models\RoomOrders;
use App\Models\Rooms;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RoomOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_room_ordering(): void
    {
        $user = User::factory()->create();
        $room = Rooms::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/rooms/order', [
            'room_id' => $room->id,
            'date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(JsonResponse::HTTP_CREATED)->assertJsonStructure(['id', 'date', 'room' => ['id', 'title', 'description']]);
    }

    public function test_order_fails_if_already_ordered(): void
    {
        $user = User::factory()->create();
        $room = Rooms::factory()->create();

        RoomOrders::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'date' => now()->addDay()->toDateString(),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/rooms/order', [
            'room_id' => $room->id,
            'date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
