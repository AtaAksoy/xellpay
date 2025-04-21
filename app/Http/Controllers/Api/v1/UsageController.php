<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Usage\UsageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageAddRequest;
use App\Services\Usage\UsageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsageController extends Controller
{

    use ApiResponse;

    public function __construct(
        public readonly UsageService $usageService
    ) {}

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
                    required: ['subscriber_no', 'month', 'usage_type', 'usage_amount'],
                    properties: [
                        new OA\Property(property: 'subscriber_no', description: "Subscriber No", type: "string"),
                        new OA\Property(property: 'month', description: "Month (1-12)", type: "integer"),
                        new OA\Property(property: 'usage_type', description: "Usage Type", type: "string", enum: ["SMS", "CALL", "INTERNET"]),
                        new OA\Property(property: 'usage_amount', description: "Usage Amount", type: "integer"),
                    ]
                )
            )
        ),
        tags: ["Usage"],
        responses: [
            new OA\Response(response: Response::HTTP_CREATED, description: "Usage Added"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function addUsage(UsageAddRequest $request): JsonResponse
    {
        $usage = UsageDTO::createFromRequest($request);

        $usageAddOperation = $this->usageService->addUsage($usage);

        return $usageAddOperation ? $this->successResponse($usage->toArray(), "Usage has been added successfully!", 201) : $this->errorResponse("Usage cannot be added!");
    }
}
