<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Bill\BillDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\CalculateBillRequest;
use App\Http\Requests\Bill\PayBillRequest;
use App\Http\Requests\Bill\QueryBillRequest;
use App\Models\User;
use App\Services\Bill\CalculateBillService;
use App\Services\Bill\PayBillService;
use App\Services\Bill\QueryBillService;
use App\Services\Subscriber\SubscriberService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class BillController extends Controller
{

    use ApiResponse;

    public ?User $subscriber;

    public function __construct(
        public readonly CalculateBillService $calculateBillService,
        public readonly SubscriberService $subscriberService,
        public readonly QueryBillService $queryBillService,
        public readonly PayBillService $payBillService,
    ) {
        $this->subscriber = request()->user();
    }

    #[OA\Post(
        path: "/api/v1/bill/calculate",
        security: [['bearerAuth' => []]],
        summary: "Calculate and store a bill for the subscriber",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'year'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month to calculate bill for (1-12)", type: "integer", example: 4),
                        new OA\Property(property: 'year', description: "Year to calculate bill for", type: "integer", example: 2025)
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Bill calculated and stored.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Bill successfully calculated!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "billDate", type: "string", format: "date", example: "2025-04-01"),
                                new OA\Property(
                                    property: "details",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "usageId", type: "integer", example: 10),
                                            new OA\Property(property: "amount", type: "number", format: "float", example: 5.0),
                                            new OA\Property(
                                                property: "usage",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "featureType", type: "string", example: "CALL"),
                                                    new OA\Property(property: "usageAmount", type: "integer", example: 500),
                                                    new OA\Property(property: "month", type: "integer", example: 4),
                                                    new OA\Property(property: "year", type: "integer", example: 2025),
                                                ]
                                            )
                                        ]
                                    )
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Failed to calculate bill",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Bill calculation failed!"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function calculateBill(CalculateBillRequest $request): JsonResponse
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $bill = $this->calculateBillService->calculateBill($this->subscriber, $month, $year);

        if ($bill instanceof BillDTO) {
            return $this->successResponse($bill->toArray(), "Bill successfully calculated!", Response::HTTP_CREATED);
        }

        return $this->errorResponse("Bill calculation failed!", Response::HTTP_BAD_REQUEST);
    }


    #[OA\Post(
        path: "/api/v1/bill/query",
        summary: "Query total bill summary for a given subscriber, month, and year",
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'year'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month to query the bill for (1-12)", type: "integer", example: 2),
                        new OA\Property(property: 'year', description: "Year to query the bill for", type: "integer", example: 2025)
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Bill query successful",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Query successful."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total_amount", type: "number", format: "float", example: 60.0),
                                new OA\Property(property: "is_paid", type: "boolean", example: false),
                                new OA\Property(property: "bill_date", type: "string", format: "date", example: "2025-02-01")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Subscriber not found or no bill for that date",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No bill for date: February 2025"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function queryBill(QueryBillRequest $request): JsonResponse
    {
        $subscriberNo = $request->user()->id;
        $month = $request->get('month');
        $year = $request->get('year');

        $subscriber = $this->subscriberService->getSubscriber($subscriberNo);

        $checkBill = $this->queryBillService->checkHasBill($subscriber, $month, $year);
        if (!$checkBill) {
            $formattedDate = Carbon::createFromDate($year, $month)->format('F Y');
            return $this->errorResponse("No bill for date: {$formattedDate}", 400);
        }

        $queryResult = $this->queryBillService->queryBill($subscriber, $month, $year);

        return $this->successResponse($queryResult->toArray(), "Query successful.");
    }

    #[OA\Post(
        path: "/api/v1/bill/query-detailed",
        security: [['bearerAuth' => []]],
        summary: "Query detailed bill (paginated 5 items per page)",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'year'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month to calculate bill (1-12)", type: "integer", example: 2),
                        new OA\Property(property: 'year', description: "Year to calculate bill", type: "integer", example: 2025),
                        new OA\Property(property: 'page', description: "Page number (default: 1)", type: "integer", example: 1),
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Bill query successful",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Query successful."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Invalid input or subscriber/bill not found",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No bill for date: 2025-02-01"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function queryBillDetailed(QueryBillRequest $request): JsonResponse
    {
        $subscriberNo = $request->get('subscriber_no');
        $month = $request->get('month');
        $year = $request->get('year');
        $page = $request->get('page', 1);

        $checkSubscriber = $this->subscriberService->checkSubscriber($subscriberNo);
        if (!$checkSubscriber) {
            return $this->errorResponse("No subscriber found with the number {$subscriberNo}!", 400);
        }

        $subscriber = $this->subscriberService->getSubscriber($subscriberNo);

        $checkBill = $this->queryBillService->checkHasBill($subscriber, $month, $year);
        if (!$checkBill) {
            $formattedDate = Carbon::createFromDate($year, $month)->format('F Y');
            return $this->errorResponse("No bill for date: {$formattedDate}", 400);
        }

        $queryResult = $this->queryBillService->queryBillDetailed($subscriber, $month, $year, $page);

        return $this->successResponse($queryResult->toArray(), "Query successful.");
    }


    #[OA\Post(
        path: "/api/v1/bill/pay",
        security: [['bearerAuth' => []]],
        summary: "Pay a bill partially or fully for a specific month",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['month', 'year', 'amount'],
                    properties: [
                        new OA\Property(property: 'month', description: "Month to pay the bill for (1-12)", type: "integer", example: 2),
                        new OA\Property(property: 'year', description: "Year to pay the bill for", type: "integer", example: 2025),
                        new OA\Property(property: 'amount', description: "Payment amount", type: "integer", example: 50),
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Payment processed successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Payment successful."),
                        new OA\Property(property: "remaining", type: "integer", example: 20)
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Invalid payment (e.g. overpayment)",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "You are paying too much. Remaining amount is $20"),
                        new OA\Property(property: "remaining", type: "integer", example: 20)
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server error")
        ]
    )]
    public function makePayment(PayBillRequest $request): JsonResponse
    {
        $response = $this->payBillService->makePayment(
            $this->subscriber,
            $request->get('month'),
            $request->get('year'),
            $request->get('amount')
        );

        if (!$response->status) {
            return $this->errorResponse($response->message, 400, [
                'remaining' => $response->remaining
            ]);
            return response()->json([
                'success' => false,
                'message' => $response->message,
                'remaining' => $response->remaining
            ], 400);
        }

        return $this->successResponse([
            'remaining' => $response->remaining
        ], $response->message);
    }
}
