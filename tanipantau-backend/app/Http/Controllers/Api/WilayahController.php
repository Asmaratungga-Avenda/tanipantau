<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WilayahApiService;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function __construct(
        protected WilayahApiService $wilayahApi
    ) {}

    public function kabupatens(): JsonResponse
    {
        $data = $this->wilayahApi->getKabupatens();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Data kabupaten tidak tersedia saat ini. Silakan coba lagi nanti.',
                'data' => [],
            ], 503);
        }

        return response()->json($data);
    }

    public function kecamatans(string $kabupatenId): JsonResponse
    {
        $data = $this->wilayahApi->getKecamatans($kabupatenId);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Data kecamatan tidak tersedia saat ini. Silakan coba lagi nanti.',
                'data' => [],
            ], 503);
        }

        return response()->json($data);
    }

    public function desas(string $kecamatanId): JsonResponse
    {
        $data = $this->wilayahApi->getDesas($kecamatanId);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Data desa tidak tersedia saat ini. Silakan coba lagi nanti.',
                'data' => [],
            ], 503);
        }

        return response()->json($data);
    }
}
