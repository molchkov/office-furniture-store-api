<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Office furniture store API"
)]
#[OA\SecurityScheme(
    securityScheme: "passport",
    type: "oauth2",
    description: "Oauth2 security",
    name: "Authorization",
    in: "header",
    bearerFormat: "bearer",
    scheme: "http",
    flows: [
        new OA\Flow(
            tokenUrl: "/oauth/token",
            flow: "password",
            scopes: []
        )
    ]
)]
abstract class Controller
{
    //
}
