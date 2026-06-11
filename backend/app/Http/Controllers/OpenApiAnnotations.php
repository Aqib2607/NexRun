<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API Documentation for NexRun Premium Sportswear E-Commerce Backend.",
    title: "NexRun API Documentation",
    contact: new OA\Contact(email: "support@nexrun.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class OpenApiAnnotations
{
    #[OA\Get(
        path: "/api/ping",
        summary: "Health Check",
        tags: ["System"],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function ping() {}
}
