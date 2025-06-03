<?php

namespace Tests\Feature;

use App\Models\Rooms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_available_rooms(): void
    {
        Rooms::factory()->count(5)->create();

        $response = $this->getJson('/api/rooms/list');

        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function test_list_rooms_with_from_parameter(): void
    {
        Rooms::factory()->count(3)->create();

        $response = $this->getJson('/api/rooms/list?from=2025-06-01');
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function test_list_rooms_with_from_and_to_parameters(): void
    {
        Rooms::factory()->count(3)->create();

        $response = $this->getJson('/api/rooms/list?from=2025-06-01&to=2025-06-14');
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function test_list_rooms_with_from_to_and_page_parameters(): void
    {
        Rooms::factory()->count(15)->create();

        $response = $this->getJson('/api/rooms/list?from=2025-06-01&to=2025-06-14&page=2');
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function test_list_rooms_with_all_parameters(): void
    {
        Rooms::factory()->count(30)->create();

        $response = $this->getJson('/api/rooms/list?from=2025-06-01&to=2025-06-14&page=2&per_page=10');
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson('/api/rooms/list?from=invalid-date');
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->getJson('/api/rooms/list?from=2025-06-01&to=2024-06-14');
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->getJson('/api/rooms/list?page=0');
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->getJson('/api/rooms/list?per_page=101');
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->getJson('/api/rooms/list?per_page=0');
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
