<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Log\LoggerInterface;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!Auth::attempt($validated)) {
                throw new Exception(__('messages.auth.unauthorized'), JsonResponse::HTTP_UNAUTHORIZED);
            }

            $user = $request->user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['token' => $token], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            $this->logger->error('User login failed: ' . $exception->getMessage() . ' (code: ' . $exception->getCode() . ')');

            return response()->json(['message' => $exception->getMessage()], $exception->getCode() >= JsonResponse::HTTP_BAD_REQUEST ? ($exception->getCode() > JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED ? JsonResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED : $exception->getCode()) : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
