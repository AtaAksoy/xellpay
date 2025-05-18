<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Subscriber\SubscriberDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberCreateRequest;
use App\Http\Requests\SubscriberLoginRequest;
use App\Services\Subscriber\SubscriberService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "XellPay API Documentation",
    description: "xellpay api"
)]
#[OA\Server(
    url: 'http://xellpay.test',
    description: "Local server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "JWT Bearer Token Authorization"
)]
class SubscriberController extends Controller
{
    use ApiResponse;

    public function __construct(protected SubscriberService $subscriberService) {}

    #[OA\Put(
        path: "/api/v1/subscriber/",
        summary: "Create a new subscriber account",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    type: "object",
                    required: ["name", "email", "password", "password_confirmation"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Jane Doe", description: "Subscriber full name"),
                        new OA\Property(property: "email", type: "string", example: "jane@example.com", description: "Subscriber email address"),
                        new OA\Property(property: "password", type: "string", example: "password123", description: "Password"),
                        new OA\Property(property: "password_confirmation", type: "string", example: "password123", description: "Password confirmation"),
                    ]
                )
            )
        ),
        tags: ["Subscriber"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Subscriber created successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Subscriber created successfully!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "Jane Doe"),
                                new OA\Property(property: "email", type: "string", example: "jane@example.com"),
                                new OA\Property(property: "token", type: "string", example: null)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function create(SubscriberCreateRequest $request): JsonResponse
    {
        $dto = SubscriberDTO::fromRequest($request->validated());
        $user = $this->subscriberService->create($dto);
        
        if (!$user) {
            return $this->errorResponse("User cannot be created!", 500, [$dto->toArray()]);
        }

        return $this->successResponse($user->toArray(), "Subscriber created successfully!", Response::HTTP_CREATED);
    }

    #[OA\Post(
        path: "/api/v1/subscriber",
        summary: "Authenticate an existing subscriber",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    type: "object",
                    required: ["email", "password"],
                    properties: [
                        new OA\Property(property: "email", type: "string", example: "jane@example.com", description: "Subscriber email address"),
                        new OA\Property(property: "password", type: "string", example: "password123", description: "Subscriber password")
                    ]
                )
            )
        ),
        tags: ["Subscriber"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Login successful",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "Jane Doe"),
                                new OA\Property(property: "email", type: "string", example: "jane@example.com"),
                                new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJh...")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Invalid credentials"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function login(SubscriberLoginRequest $request): JsonResponse
    {
        $response = $this->subscriberService->login($request);

        if ($response instanceof SubscriberDTO) {
            return $this->successResponse($response->toArray(), "Login successful.");
        }

        return $this->errorResponse("Unauthorised", 401);
    }
}
