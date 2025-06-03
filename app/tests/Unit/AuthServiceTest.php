<?php

namespace Tests\Unit;

use App\Http\Requests\LoginAuthRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;
    private $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->service = new AuthService($this->loggerMock);
    }

    public function test_successful_login()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $request = $this->createValidRequest([
            'email' => $user->email,
            'password' => $password,
        ]);

        $request->setUserResolver(fn() => $user);

        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(true);

        $response = $this->service->login($request);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->status());
        $this->assertJsonStructure($response, ['token']);
    }

    public function test_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password')
        ]);

        $request = $this->createValidRequest([
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->loggerMock->expects($this->once())
            ->method('error');

        $response = $this->service->login($request);

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->status());
        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }

    public function test_fails_when_validation_errors()
    {
        $request = $this->createInvalidRequest([
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response = $this->service->login($request);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->status());
        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }

    private function createValidRequest(array $data): LoginAuthRequest
    {
        $request = new LoginAuthRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->setValidator($validator);

        return $request;
    }

    private function createInvalidRequest(array $data): LoginAuthRequest
    {
        $request = new LoginAuthRequest();
        $request->merge($data);

        $validator = Validator::make([], $request->rules());
        $validator->errors()->add('email', 'The email must be a valid email address.');
        $validator->errors()->add('password', 'The password must be at least 8 characters.');
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
