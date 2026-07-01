<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BankService;
use App\Services\BankValidationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CekRekeningController extends Controller
{
    protected BankService $bankService;
    protected BankValidationManager $validationManager;

    public function __construct(BankService $bankService, BankValidationManager $validationManager)
    {
        $this->bankService = $bankService;
        $this->validationManager = $validationManager;
    }

    #[OA\Post(
        path: '/cek-rekening',
        summary: 'Cek nama pemilik rekening bank/e-wallet',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['bank_code', 'account_number'],
                properties: [
                    new OA\Property(property: 'bank_code', type: 'string', description: 'Kode bank/ewallet', example: 'bca'),
                    new OA\Property(property: 'account_number', type: 'string', description: 'Nomor rekening', example: '0888123456'),
                ]
            )
        ),
        tags: ['Cek Rekening'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Rekening ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Rekening ditemukan'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'status', type: 'string', example: 'valid'),
                                new OA\Property(property: 'bank_code', type: 'string', example: 'bca'),
                                new OA\Property(property: 'bank_name', type: 'string', example: 'Bank BCA'),
                                new OA\Property(property: 'type', type: 'string', example: 'bank'),
                                new OA\Property(property: 'account_number', type: 'string', example: '0888123456'),
                                new OA\Property(property: 'account_name', type: 'string', example: 'Budi Santoso'),
                                new OA\Property(property: 'reference_id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                                new OA\Property(property: 'validator', type: 'string', example: 'dummy'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Rekening tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Rekening tidak ditemukan'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'status', type: 'string', example: 'invalid'),
                                new OA\Property(property: 'bank_code', type: 'string', example: 'bca'),
                                new OA\Property(property: 'bank_name', type: 'string', example: 'Bank BCA'),
                                new OA\Property(property: 'type', type: 'string', example: 'bank'),
                                new OA\Property(property: 'account_number', type: 'string', example: 'xxxx'),
                                new OA\Property(property: 'message', type: 'string', example: 'Nomor rekening tidak ditemukan'),
                                new OA\Property(property: 'reference_id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                                new OA\Property(property: 'validator', type: 'string', example: 'dummy'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal'
            ),
        ]
    )]
    public function cek(Request $request): JsonResponse
    {
        $request->validate([
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $result = $this->validationManager->cekRekening(
            $request->bank_code,
            $request->account_number
        );

        if (empty($result['bank_name'])) {
            $bank = $this->bankService->findBank($request->bank_code);
            if ($bank) {
                $result['bank_name'] = $bank['name'];
                $result['type'] = $bank['type'];
            }
        }

        $result['reference_id'] = (string) \Illuminate\Support\Str::uuid();

        $httpStatus = $result['status'] === 'valid' ? 200 : 404;

        return response()->json([
            'success' => $result['status'] === 'valid',
            'message' => $result['message'] ?? ($result['status'] === 'valid' ? 'Rekening ditemukan' : 'Rekening tidak ditemukan'),
            'data' => $result,
        ], $httpStatus);
    }

    #[OA\Get(
        path: '/banks',
        summary: 'Daftar bank dan e-wallet yang didukung',
        tags: ['Cek Rekening'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar bank berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'bank_code', type: 'string', example: 'bca'),
                                    new OA\Property(property: 'bank_name', type: 'string', example: 'Bank BCA'),
                                    new OA\Property(property: 'type', type: 'string', example: 'bank'),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function daftarBank(): JsonResponse
    {
        $banks = $this->bankService->getAllBanks();

        $data = collect($banks)->map(fn ($bank, $code) => [
            'bank_code' => $code,
            'bank_name' => $bank['name'],
            'type' => $bank['type'],
        ])->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
