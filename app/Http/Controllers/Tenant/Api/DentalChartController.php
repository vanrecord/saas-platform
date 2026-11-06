<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DentalChartRequest;
use App\Http\Requests\Tenant\SyncChartFindingsRequest;
use App\Models\Tenant\DentalChart;
use App\Services\Tenant\DentalChartSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    public function __construct(
        protected DentalChartSyncService $syncService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = DentalChart::query()
            ->with(['patient:id,firstname,lastname,id_number'])
            ->withCount('teeth');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        $charts = $query->latest('chart_date')->paginate($request->integer('per_page', 15));

        return response()->json($charts);
    }

    public function store(DentalChartRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $chart = DentalChart::create($data);

        return response()->json([
            'message' => 'Dental chart created successfully',
            'data' => $chart->load('patient'),
        ], 201);
    }

    public function show(DentalChart $dentalChart): JsonResponse
    {
        $dentalChart->load([
            'patient',
            'teeth.findings.condition',
            'creator:id,name',
            'updater:id,name',
        ]);

        return response()->json(['data' => $dentalChart]);
    }

    public function update(DentalChartRequest $request, DentalChart $dentalChart): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['updated_by'] = $request->user()->id;
        }

        $dentalChart->update($data);

        return response()->json([
            'message' => 'Dental chart updated successfully',
            'data' => $dentalChart->fresh('patient'),
        ]);
    }

    public function destroy(DentalChart $dentalChart): JsonResponse
    {
        $dentalChart->delete();

        return response()->json(['message' => 'Dental chart deleted successfully']);
    }

    /**
     * Synchronize findings from the interactive dental chart
     */
    public function syncFindings(SyncChartFindingsRequest $request, DentalChart $dentalChart): JsonResponse
    {
        $updatedChart = $this->syncService->sync(
            $dentalChart,
            $request->validated(),
            $request->user()?->id
        );

        return response()->json([
            'message' => 'Chart findings synchronized successfully',
            'data' => $updatedChart,
        ]);
    }
}

