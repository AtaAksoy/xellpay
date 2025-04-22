<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Bill\BillDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\CalculateBillRequest;
use App\Models\User;
use App\Services\Bill\CalculateBillService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class BillController extends Controller
{
    
    use ApiResponse;

    public User $subscriber;

    public function __construct(public readonly CalculateBillService $calculateBillService)
    {
        $this->subscriber = request()->user();
    }

    #[OA\Post(
        path: "/api/v1/bill/calculate",
        security: [['bearerAuth' => []]],
        summary: "Calculate and store bill",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'year'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month to calculate bill 1-12", type: "int"),
                        new OA\Property(property: 'year', description: "Year to calculate bill", type: "int"),
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(response: Response::HTTP_CREATED, description: "Bill calculated and stored."),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function calculateBill(CalculateBillRequest $request) : JsonResponse {
        $bill = $this->calculateBillService->calculateBill($this->subscriber, $request->get('month'), $request->get('year'));

        if ($bill instanceof BillDTO) {
            return $this->successResponse($bill->toArray(), "Bill successfully calculated!", 200);
        } else {
            return $this->errorResponse("Bill calculation failed!");
        }

        return $this->successResponse();
    }

}
