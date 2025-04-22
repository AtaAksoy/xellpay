<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Usage\UsageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageAddRequest;
use App\Services\Usage\UsageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class UsageController extends Controller
{
    use ApiResponse;

    public function __construct(public readonly UsageService $usageService) {}

    #[OA\Put(
        path: "/api/v1/usage/",
        summary: "Add usage to subscriber",
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'usage_type', 'usage_amount'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month of usage (1-12)", type: "integer", example: 4),
                        new OA\Property(
                            property: 'usage_type',
                            description: "Type of usage (SMS, CALL, INTERNET)",
                            type: "string",
                            enum: ["SMS", "CALL", "INTERNET"],
                            example: "CALL"
                        ),
                        new OA\Property(property: 'usage_amount', description: "Usage amount (e.g., minutes, MBs, count)", type: "integer", example: 500),
                    ]
                )
            )
        ),
        tags: ["Usage"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Usage added successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usage has been added successfully!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "featureType", type: "string", example: "CALL"),
                                new OA\Property(property: "usageAmount", type: "integer", example: 500),
                                new OA\Property(property: "month", type: "integer", example: 4),
                                new OA\Property(property: "year", type: "integer", nullable: true, example: null)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation failed"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function addUsage(UsageAddRequest $request): JsonResponse
    {
        $usage = UsageDTO::createFromRequest($request);
        $subscriber = $request->user();
        $usageAddOperation = $this->usageService->addUsage($usage, $subscriber);

        return $usageAddOperation
            ? $this->successResponse($usage->toArray(), "Usage has been added successfully!", Response::HTTP_CREATED)
            : $this->errorResponse("Usage cannot be added!", Response::HTTP_BAD_REQUEST);
    }
}
