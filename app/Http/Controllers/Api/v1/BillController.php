<?php

namespace App\Http\Controllers\Api\v1;

use App\DTOs\Bill\BillDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\CalculateBillRequest;
use App\Http\Requests\Bill\QueryBillRequest;
use App\Models\User;
use App\Services\Bill\CalculateBillService;
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
        public readonly QueryBillService $queryBillService
    ) {
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
    public function calculateBill(CalculateBillRequest $request): JsonResponse
    {
        $bill = $this->calculateBillService->calculateBill($this->subscriber, $request->get('month'), $request->get('year'));

        if ($bill instanceof BillDTO) {
            return $this->successResponse($bill->toArray(), "Bill successfully calculated!", 200);
        } else {
            return $this->errorResponse("Bill calculation failed!");
        }

        return $this->successResponse();
    }

    #[OA\Post(
        path: "/api/v1/bill/query",
        summary: "Query bill",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['subscriber_no', 'month', 'year'],
                    properties: [
                        new OA\Property(property: 'subscriber_no', description: "Subscriber No", type: "int"),
                        new OA\Property(property: 'month', description: "Month to calculate bill 1-12", type: "int"),
                        new OA\Property(property: 'year', description: "Year to calculate bill", type: "int"),
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Bill query successfull."),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function queryBill(QueryBillRequest $request): JsonResponse
    {

        $checkSubscriber = $this->subscriberService->checkSubscriber($request->get('subscriber_no'));
        if (!$checkSubscriber)
            return $this->errorResponse("No subscriber found with the number {$request->get('subscriber_no')}!");

        $subscriber = $this->subscriberService->getSubscriber($request->get('subscriber_no'));

        $checkBill = $this->queryBillService->checkHasBill($subscriber, $request->get('month'), $request->get('year'));
        if (!$checkBill)
            return $this->errorResponse("No bill for date: " . Carbon::createFromDate($request->get('year'), $request->get('month'), null));

        $queryResult = $this->queryBillService->queryBill($subscriber, $request->get('month'), $request->get('year'));

        return $this->successResponse($queryResult->toArray());
    }

    #[OA\Post(
        path: "/api/v1/bill/query-detailed",
        security: [['bearerAuth' => []]],
        summary: "Query bill detailed 5 items per page",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ['subscriber_no', 'month', 'year'],
                    properties: [
                        new OA\Property(property: 'subscriber_no', description: "Subscriber No", type: "int"),
                        new OA\Property(property: 'month', description: "Month to calculate bill 1-12", type: "int"),
                        new OA\Property(property: 'year', description: "Year to calculate bill", type: "int"),
                        new OA\Property(property: 'page', description: "Page no (default 1)", type: "int"),
                    ]
                )
            )
        ),
        tags: ["Bill"],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "Bill query detail successfull."),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function queryBillDetailed(QueryBillRequest $request): JsonResponse
    {

        $checkSubscriber = $this->subscriberService->checkSubscriber($request->get('subscriber_no'));
        if (!$checkSubscriber)
            return $this->errorResponse("No subscriber found with the number {$request->get('subscriber_no')}!");

        $subscriber = $this->subscriberService->getSubscriber($request->get('subscriber_no'));

        $checkBill = $this->queryBillService->checkHasBill($subscriber, $request->get('month'), $request->get('year'));
        if (!$checkBill)
            return $this->errorResponse("No bill for date: " . Carbon::createFromDate($request->get('year'), $request->get('month'), null));

        $queryResult = $this->queryBillService->queryBillDetailed($subscriber, $request->get('month'), $request->get('year'), $request->get('page', 1));

        return $this->successResponse($queryResult->toArray());
    }
}
