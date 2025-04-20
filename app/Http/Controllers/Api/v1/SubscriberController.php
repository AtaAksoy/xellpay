<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberCreateRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[
    OA\Info(version: "1.0.0", description: "xellpay api", title: "XellPay API Documentation"),
    OA\Server(url: 'http://xellpay.test', description: "local server"),
]
class SubscriberController extends Controller
{

    use ApiResponse;

    #[OA\Put(
        path: "/api/v1/subscriber/",
        summary: "Create a subscriber",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    type: "object",
                    required: ["name", "email", "password", "password_confirmation"],
                    properties: [
                        new OA\Property(property: 'name', description: "Subscriber Name", type: "string"),
                        new OA\Property(property: 'email', description: "Subscriber Email", type: "string"),
                        new OA\Property(property: 'password', description: "Password", type: "string"),
                        new OA\Property(property: 'password_confirmation', description: "Password confirmation", type: "string"),
                    ]
                )
            )
        ),
        tags: ["Subscriber"],
        responses: [
            new OA\Response(response: Response::HTTP_CREATED, description: "Register Successfully"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function create(SubscriberCreateRequest $request)
    {
        return $this->successResponse();
    }
}
