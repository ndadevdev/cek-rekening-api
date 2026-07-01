<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Cek Rekening Bank/E-Wallet API',
    description: 'REST API untuk mengecek nama pemilik rekening bank dan e-wallet Indonesia',
    version: '1.0.0',
    contact: new OA\Contact(
        email: 'developer@example.com'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'API Token',
    description: 'Masukkan token API yang didapat dari endpoint login'
)]
class OpenApi
{
}
