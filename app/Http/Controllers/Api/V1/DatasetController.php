<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dataset;

class DatasetController extends Controller
{
    /** GET /api/v1/datasets/{code}/schema — reference data / schema download (IF-006). */
    public function schema(string $code)
    {
        $dataset = Dataset::where('code', $code)->where('is_active', true)->first();
        abort_unless($dataset, 404, 'Unknown or inactive dataset code.');

        return response()->json([
            'code' => $dataset->code,
            'name' => $dataset->name,
            'version' => $dataset->updated_at?->toIso8601String(),
            'frequency' => $dataset->frequency,
            'fields' => $dataset->fields,
        ]);
    }
}
