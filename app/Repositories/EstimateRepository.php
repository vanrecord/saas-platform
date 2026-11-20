<?php

namespace App\Repositories;

use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\Estimate;
use App\Models\Tenant\EstimateDetail;
use App\Models\Tenant\EstimateTooth;
use App\Models\Tenant\EstimateToothFinding;
use App\Models\Tenant\MedicalDetail;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Product;
use App\Models\Tenant\Reception;
use App\Models\Tenant\Service;
use App\Models\Tenant\ToothFinding;
use App\Models\Tenant\TreatmentTeeth;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EstimateRepository extends BaseRepository implements EstimateRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Estimate::class)
             ->setRepositoryId('rinvex.repository.estimate');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $rawTerm = preg_replace('/^qut0*/i', '', $term);

            $query->where(function ($query) use ($term, $rawTerm, $value) {
                $query->where(DB::raw('CAST(code AS TEXT)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(COALESCE(diagnosis, \'\'))'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(COALESCE(description, \'\'))'), 'LIKE', "%{$term}%")
                    ->orWhereHas('patient', function ($pQuery) use ($term, $value) {
                        $pQuery->where(DB::raw('LOWER(COALESCE(firstname, \'\'))'), 'LIKE', "%{$term}%")
                               ->orWhere(DB::raw('LOWER(COALESCE(lastname, \'\'))'), 'LIKE', "%{$term}%")
                               ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                               ->orWhere('phone', 'LIKE', "%{$term}%");

                        if (is_numeric($value)) {
                            $pQuery->orWhere('id_number', (int) $value)
                                   ->orWhere('id', (int) $value);
                        }
                    });

                if (is_numeric($rawTerm)) {
                    $query->orWhere('code', (int) $rawTerm)
                          ->orWhere('id', (int) $rawTerm);
                }
            });
        });

        $startDateFilter = AllowedFilter::callback('start_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('estimate_at', '>=', Carbon::parse($value)->startOfDay());
            }
        });

        $endDateFilter = AllowedFilter::callback('end_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('estimate_at', '<=', Carbon::parse($value)->endOfDay());
            }
        });

        $patientNameFilter = AllowedFilter::callback('patient_name', function ($query, $value) {
            $term = strtolower(trim((string) $value));
            if ($term !== '') {
                $query->whereHas('patient', function ($pQuery) use ($term) {
                    $pQuery->where(DB::raw('LOWER(COALESCE(firstname, \'\'))'), 'LIKE', "%{$term}%")
                           ->orWhere(DB::raw('LOWER(COALESCE(lastname, \'\'))'), 'LIKE', "%{$term}%")
                           ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                           ->orWhere('phone', 'LIKE', "%{$term}%");
                });
            }
        });

        $patientIdFilter = AllowedFilter::callback('patient_id', function ($query, $value) {
            if (!empty($value)) {
                $query->where('patient_id', $value);
            }
        });

        $quoteNoFilter = AllowedFilter::callback('quote_no', function ($query, $value) {
            $term = strtolower(trim((string) $value));
            $clean = preg_replace('/^qut0*/i', '', $term);
            if ($clean !== '') {
                $query->where(function ($q) use ($clean) {
                    if (is_numeric($clean)) {
                        $q->where('code', (int) $clean)->orWhere('id', (int) $clean);
                    } else {
                        $q->where(DB::raw('CAST(code AS TEXT)'), 'LIKE', "%{$clean}%");
                    }
                });
            }
        });

        $statusFilter = AllowedFilter::callback('status', function ($query, $value) {
            if ($value !== null && $value !== '') {
                $val = strtolower(trim((string) $value));
                $map = [
                    'draft' => Estimate::DRAFT,
                    'approved' => Estimate::APPROVED,
                    'rejected' => Estimate::REJECTE,
                    'completed' => Estimate::COMPLETED,
                    'verified' => Estimate::VERIFIED,
                    'partially_approved' => Estimate::PARTIALLY_APPROVED,
                    'partially approved' => Estimate::PARTIALLY_APPROVED,
                    'partially_rejected' => Estimate::PARTIALLY_REJECTED,
                    'partially rejected' => Estimate::PARTIALLY_REJECTED,
                ];
                if (isset($map[$val])) {
                    $query->where('status', $map[$val]);
                } elseif (is_numeric($val)) {
                    $query->where('status', (int) $val);
                }
            }
        });

        $paginated = QueryBuilder::for(Estimate::class)
            ->with([
                'patient',
                'doctor.user.profile',
                'assistant.profile',
                'estimateDetails.teeth',
            ])
            ->defaultSort('-estimate_at', '-id')
            ->allowedSorts([
                AllowedSort::field('estimate_at'),
                AllowedSort::field('code'),
                AllowedSort::field('id'),
                AllowedSort::field('status'),
            ])
            ->allowedFilters([
                $globalSearch,
                $startDateFilter,
                $endDateFilter,
                $patientNameFilter,
                $patientIdFilter,
                $quoteNoFilter,
                $statusFilter,
            ])
            ->paginate()
            ->withQueryString();

        return $paginated;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'estimate_at', label: 'Daily Date', sortable: true);
        $table->column(key: 'quote_no', label: 'Quote #', sortable: true, searchable: true);
        $table->column(key: 'patient_id_number', label: 'ID Patient', sortable: true);
        $table->column(key: 'patient_name', label: 'Patient', sortable: true);
        $table->column(key: 'total', label: 'Total ($)');
        $table->column(key: 'discount_amount', label: 'Discount ($)');
        $table->column(key: 'status', label: 'Status', sortable: true);
    }

    public function findWithDetails($id)
    {
        return Estimate::with([
            'patient.addressRecord',
            'doctor.user.profile',
            'assistant.profile',
            'estimateDetails.detailable',
            'estimateDetails.teeth.toothFindings.condition',
            'estimateDetails.approvedByUser',
            'estimateDetails.rejectedByUser',
            'invoice.medicalDetails',
        ])->findOrFail($id);
    }

    public function getNextCode(): int
    {
        $max = (int) (Estimate::max('code') ?? 0);
        return $max + 1;
    }

    public function createEstimate(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dailyDate = !empty($data['estimate_at']) ? Carbon::parse($data['estimate_at']) : Carbon::now();
            $code = !empty($data['code']) ? (int) $data['code'] : $this->getNextCode();
            $rate = !empty($data['rate']) ? (float) $data['rate'] : 4050.0;
            $discount = !empty($data['discount_invoice']) ? (float) $data['discount_invoice'] : 0.0;
            $discountAs = !empty($data['discount_inv_as']) ? $data['discount_inv_as'] : '%';

            $estimate = Estimate::create([
                'doctor_id' => !empty($data['doctor_id']) ? $data['doctor_id'] : null,
                'user_id' => !empty($data['user_id']) ? $data['user_id'] : auth()->id(),
                'employee_id' => !empty($data['assistant_id']) ? $data['assistant_id'] : (!empty($data['employee_id']) ? $data['employee_id'] : 0),
                'patient_id' => $data['patient_id'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'medical_alert' => $data['medical_alert'] ?? null,
                'description' => $data['description'] ?? null,
                'reason' => null,
                'rate' => $rate,
                'discount_invoice' => $discount,
                'discount_inv_as' => $discountAs,
                'estimate_at' => $dailyDate,
                'status' => Estimate::DRAFT,
                'code' => $code,
                'term' => !empty($data['term']),
                'chief_complaint' => $data['chief_complaint'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->syncEstimateDetails($estimate, $data['items'] ?? [], $data['odontogram_findings'] ?? []);
            $estimate->recalculateStatus();

            return $estimate;
        });
    }

    public function updateEstimate($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $estimate = Estimate::where('id', $id)->lockForUpdate()->firstOrFail();
            $dailyDate = !empty($data['estimate_at']) ? Carbon::parse($data['estimate_at']) : ($estimate->estimate_at ?: Carbon::now());
            $rate = !empty($data['rate']) ? (float) $data['rate'] : ($estimate->rate ?: 4050.0);
            $discount = isset($data['discount_invoice']) ? (float) $data['discount_invoice'] : $estimate->discount_invoice;
            $discountAs = !empty($data['discount_inv_as']) ? $data['discount_inv_as'] : ($estimate->discount_inv_as ?: '%');

            $estimate->update([
                'doctor_id' => !empty($data['doctor_id']) ? $data['doctor_id'] : $estimate->doctor_id,
                'employee_id' => isset($data['assistant_id']) ? $data['assistant_id'] : (isset($data['employee_id']) ? $data['employee_id'] : $estimate->employee_id),
                'patient_id' => $data['patient_id'] ?? $estimate->patient_id,
                'diagnosis' => $data['diagnosis'] ?? $estimate->diagnosis,
                'medical_alert' => $data['medical_alert'] ?? $estimate->medical_alert,
                'description' => $data['description'] ?? $estimate->description,
                'rate' => $rate,
                'discount_invoice' => $discount,
                'discount_inv_as' => $discountAs,
                'estimate_at' => $dailyDate,
                'code' => !empty($data['code']) ? (int) $data['code'] : $estimate->code,
                'term' => isset($data['term']) ? (bool) $data['term'] : $estimate->term,
                'chief_complaint' => $data['chief_complaint'] ?? $estimate->chief_complaint,
            ]);

            $this->syncEstimateDetails($estimate, $data['items'] ?? [], $data['odontogram_findings'] ?? []);
            $estimate->recalculateStatus();

            return $estimate;
        });
    }

    protected function syncEstimateDetails(Estimate $estimate, array $items, array $odontogramFindings = []): void
    {
        $keptIds = [];
        $processedToothSurfaces = [];

        foreach ($items as $item) {
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $cost = max(0, (float) ($item['cost'] ?? 0));
            $discount = max(0, (float) ($item['discount'] ?? 0));
            $discountAs = $item['discount_as'] ?? '%';

            $gross = $qty * $unitPrice;
            $discAmount = $discountAs === '%' ? ($gross * ($discount / 100)) : $discount;
            $discAmount = min($gross, max(0, $discAmount));
            $amount = max(0, $gross - $discAmount);

            $itemType = strtolower($item['item_type'] ?? 'service');
            $detailableType = $itemType === 'product' ? Product::class : Service::class;
            $detailableId = (int) ($item['detailable_id'] ?? $item['service_id'] ?? 0);
            $operateDate = !empty($item['date']) ? Carbon::parse($item['date'])->format('Y-m-d') : ($estimate->estimate_at ? Carbon::parse($estimate->estimate_at)->format('Y-m-d') : Carbon::today()->format('Y-m-d'));

            $detailData = [
                'detailable_id' => $detailableId,
                'detailable_type' => $detailableType,
                'service_id' => $itemType === 'service' ? $detailableId : null,
                'date' => $operateDate,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'cost' => $cost,
                'cost_amount' => $cost * $qty,
                'discount' => $discount,
                'discount_as' => $discountAs,
                'amount' => $amount,
                'note' => $item['note'] ?? null,
            ];

            $detail = null;
            if (!empty($item['id'])) {
                $existing = $estimate->estimateDetails()->find($item['id']);
                if ($existing) {
                    $existing->update($detailData);
                    $detail = $existing;
                }
            }

            if (!$detail) {
                $detailData['status'] = EstimateDetail::DRAFT;
                $detail = $estimate->estimateDetails()->create($detailData);
            }

            $keptIds[] = $detail->id;

            // Sync teeth and findings
            $oldTeeth = EstimateTooth::where('estimate_detail_id', $detail->id)->get();
            foreach ($oldTeeth as $ot) {
                EstimateToothFinding::where('estimate_teeth_id', $ot->id)->forceDelete();
                $ot->delete();
            }

            $teeth = $item['teeth'] ?? [];
            if (is_string($teeth)) {
                $teeth = explode('|', $teeth);
            }
            if (is_array($teeth)) {
                $this->syncEstimateTeethAndFindings(
                    $detail,
                    $teeth,
                    $odontogramFindings,
                    $processedToothSurfaces
                );
            }
        }

        // Attach any remaining unassigned odontogram findings to first detail
        $this->attachUnassignedEstimateOdontogramFindings($estimate, $odontogramFindings, $processedToothSurfaces);

        // Remove deleted items safely
        $removedItems = $estimate->estimateDetails()->whereNotIn('id', $keptIds)->get();
        foreach ($removedItems as $removed) {
            if ($removed->medical_detail_id) {
                $medDetail = MedicalDetail::find($removed->medical_detail_id);
                if ($medDetail) {
                    $invoice = MedicalHistory::find($medDetail->medical_history_id);
                    $remTeeth = TreatmentTeeth::where('medical_detail_id', $medDetail->id)->get();
                    foreach ($remTeeth as $rt) {
                        ToothFinding::where('treatment_teeth_id', $rt->id)->forceDelete();
                        $rt->delete();
                    }
                    $itemType = strtolower($removed->item_type ?? 'service');
                    $detailableId = (int) ($removed->detailable_id ?: $removed->service_id);
                    $this->updateStock($detailableId, $itemType, (int) $removed->quantity, 'increment');
                    $medDetail->delete();
                    if ($invoice) {
                        $invoice->recalculateFinancials(true);
                    }
                }
            }
            $remTeeth = EstimateTooth::where('estimate_detail_id', $removed->id)->get();
            foreach ($remTeeth as $rt) {
                EstimateToothFinding::where('estimate_teeth_id', $rt->id)->forceDelete();
                $rt->delete();
            }
            $removed->delete();
        }
    }

    protected function syncEstimateTeethAndFindings(
        EstimateDetail $detail,
        array $teethData,
        array $odontogramFindings = [],
        array &$processedToothSurfaces = []
    ): void {
        foreach ($teethData as $toothItem) {
            $toothNum = is_array($toothItem) ? ($toothItem['teeth_number'] ?? $toothItem['tooth'] ?? null) : $toothItem;
            if (!$toothNum) {
                continue;
            }

            $toothNumStr = (string) $toothNum;

            $hasTop = !empty($toothItem['class_one']) || !empty($toothItem['surfaces']['top']);
            $hasBottom = !empty($toothItem['class_two']) || !empty($toothItem['surfaces']['bottom']);
            $hasLeft = !empty($toothItem['class_three']) || !empty($toothItem['surfaces']['left']);
            $hasRight = !empty($toothItem['class_four']) || !empty($toothItem['surfaces']['right']);
            $hasCenter = !empty($toothItem['class_five']) || !empty($toothItem['surfaces']['center']);
            $hasWhole = !empty($toothItem['class_six']) || !empty($toothItem['surfaces']['whole']);

            $findingsToCreate = [];

            // 1. Direct findings in toothItem
            if (!empty($toothItem['findings']) && is_array($toothItem['findings'])) {
                foreach ($toothItem['findings'] as $finding) {
                    $surf = $finding['surface'] ?? 'whole';
                    $key = "{$toothNumStr}_{$surf}";
                    $findingsToCreate[$key] = [
                        'surface' => $surf,
                        'dental_condition_id' => $finding['dental_condition_id'] ?? $finding['condition_id'] ?? null,
                        'custom_color' => $finding['custom_color'] ?? $finding['color'] ?? null,
                        'note' => $finding['note'] ?? null,
                        'treatment_status' => $finding['treatment_status'] ?? $finding['status'] ?? 'planned',
                    ];
                    $processedToothSurfaces[$key] = true;

                    if ($surf === 'top') $hasTop = true;
                    if ($surf === 'bottom') $hasBottom = true;
                    if ($surf === 'left') $hasLeft = true;
                    if ($surf === 'right') $hasRight = true;
                    if ($surf === 'center') $hasCenter = true;
                    if ($surf === 'whole') $hasWhole = true;
                }
            }

            // 2. Matching from odontogram_findings payload
            if (!empty($odontogramFindings)) {
                foreach ($odontogramFindings as $oFinding) {
                    if (isset($oFinding['tooth']) && (string) $oFinding['tooth'] === $toothNumStr) {
                        $fServiceId = $oFinding['service_id'] ?? null;
                        if ($fServiceId === null || $fServiceId == $detail->detailable_id || $fServiceId == $detail->service_id) {
                            $surf = $oFinding['surface'] ?? 'whole';
                            $key = "{$toothNumStr}_{$surf}";
                            if (!isset($findingsToCreate[$key])) {
                                $findingsToCreate[$key] = [
                                    'surface' => $surf,
                                    'dental_condition_id' => $oFinding['dental_condition_id'] ?? $oFinding['condition_id'] ?? null,
                                    'custom_color' => $oFinding['custom_color'] ?? $oFinding['color'] ?? null,
                                    'note' => $oFinding['note'] ?? null,
                                    'treatment_status' => $oFinding['treatment_status'] ?? $oFinding['status'] ?? 'planned',
                                ];
                            }
                            $processedToothSurfaces[$key] = true;

                            if ($surf === 'top') $hasTop = true;
                            if ($surf === 'bottom') $hasBottom = true;
                            if ($surf === 'left') $hasLeft = true;
                            if ($surf === 'right') $hasRight = true;
                            if ($surf === 'center') $hasCenter = true;
                            if ($surf === 'whole') $hasWhole = true;
                        }
                    }
                }
            }

            // 3. Fallback: if surfaces were marked boolean but no specific finding was attached
            $surfacesMap = [
                'top' => $hasTop,
                'bottom' => $hasBottom,
                'left' => $hasLeft,
                'right' => $hasRight,
                'center' => $hasCenter,
                'whole' => $hasWhole,
            ];

            foreach ($surfacesMap as $surfName => $isMarked) {
                if ($isMarked) {
                    $key = "{$toothNumStr}_{$surfName}";
                    if (!isset($findingsToCreate[$key])) {
                        $findingsToCreate[$key] = [
                            'surface' => $surfName,
                            'dental_condition_id' => null,
                            'custom_color' => null,
                            'note' => null,
                            'treatment_status' => 'planned',
                        ];
                    }
                    $processedToothSurfaces[$key] = true;
                }
            }

            if (!$hasTop && !$hasBottom && !$hasLeft && !$hasRight && !$hasCenter && !$hasWhole) {
                $hasWhole = true;
            }

            $numInt = (int) $toothNumStr;
            $toothType = is_array($toothItem) && !empty($toothItem['tooth_type'])
                ? $toothItem['tooth_type']
                : (($numInt >= 51 && $numInt <= 85) ? 'primary' : 'permanent');

            $status = is_array($toothItem)
                ? ($toothItem['tooth_status'] ?? ($toothItem['findings'][0]['status'] ?? 'planned'))
                : 'planned';

            $estimateTooth = EstimateTooth::create([
                'estimate_detail_id' => $detail->id,
                'teeth_number' => $toothNumStr,
                'class_one' => $hasTop,
                'class_two' => $hasBottom,
                'class_three' => $hasLeft,
                'class_four' => $hasRight,
                'class_five' => $hasCenter,
                'class_six' => $hasWhole,
                'tooth_type' => $toothType,
                'tooth_status' => $status,
            ]);

            foreach ($findingsToCreate as $fData) {
                EstimateToothFinding::create([
                    'estimate_teeth_id' => $estimateTooth->id,
                    'dental_condition_id' => $this->resolveValidConditionId($fData['dental_condition_id'] ?? null),
                    'surface' => $fData['surface'] ?? 'whole',
                    'custom_color' => $fData['custom_color'] ?? null,
                    'note' => $fData['note'] ?? null,
                    'treatment_status' => $fData['treatment_status'] ?? 'planned',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    protected function resolveValidConditionId($conditionId): ?int
    {
        if (empty($conditionId) || !is_numeric($conditionId)) {
            return null;
        }

        static $validConditionIds = null;
        if ($validConditionIds === null) {
            if (DentalCondition::count() === 0 && class_exists(\Database\Seeders\DentalConditionSeeder::class)) {
                try {
                    (new \Database\Seeders\DentalConditionSeeder())->run();
                } catch (\Throwable $e) {
                    // Suppress seeder error if table already has records or unique constraint
                }
            }
            $validConditionIds = DentalCondition::pluck('id')->map(fn($id) => (int) $id)->all();
        }

        $cId = (int) $conditionId;
        return in_array($cId, $validConditionIds, true) ? $cId : null;
    }

    protected function attachUnassignedEstimateOdontogramFindings(
        Estimate $estimate,
        array $odontogramFindings = [],
        array $processedToothSurfaces = []
    ): void {
        if (empty($odontogramFindings)) {
            return;
        }

        $fallbackDetail = $estimate->estimateDetails()->first();
        if (!$fallbackDetail) {
            return;
        }

        $unassignedByTooth = [];
        foreach ($odontogramFindings as $oFinding) {
            $toothNumStr = (string) ($oFinding['tooth'] ?? '');
            $surf = $oFinding['surface'] ?? 'whole';
            $key = "{$toothNumStr}_{$surf}";
            if ($toothNumStr !== '' && empty($processedToothSurfaces[$key])) {
                $unassignedByTooth[$toothNumStr][] = $oFinding;
            }
        }

        foreach ($unassignedByTooth as $toothNumStr => $findingsList) {
            $hasTop = collect($findingsList)->contains('surface', 'top');
            $hasBottom = collect($findingsList)->contains('surface', 'bottom');
            $hasLeft = collect($findingsList)->contains('surface', 'left');
            $hasRight = collect($findingsList)->contains('surface', 'right');
            $hasCenter = collect($findingsList)->contains('surface', 'center');
            $hasWhole = collect($findingsList)->contains('surface', 'whole');

            $numInt = (int) $toothNumStr;

            $estimateTooth = EstimateTooth::create([
                'estimate_detail_id' => $fallbackDetail->id,
                'teeth_number' => $toothNumStr,
                'class_one' => $hasTop,
                'class_two' => $hasBottom,
                'class_three' => $hasLeft,
                'class_four' => $hasRight,
                'class_five' => $hasCenter,
                'class_six' => $hasWhole,
                'tooth_type' => ($numInt >= 51 && $numInt <= 85) ? 'primary' : 'permanent',
                'tooth_status' => $findingsList[0]['status'] ?? 'planned',
            ]);

            foreach ($findingsList as $fData) {
                EstimateToothFinding::create([
                    'estimate_teeth_id' => $estimateTooth->id,
                    'dental_condition_id' => $this->resolveValidConditionId($fData['dental_condition_id'] ?? $fData['condition_id'] ?? null),
                    'surface' => $fData['surface'] ?? 'whole',
                    'custom_color' => $fData['custom_color'] ?? $fData['color'] ?? null,
                    'note' => $fData['note'] ?? null,
                    'treatment_status' => $fData['treatment_status'] ?? $fData['status'] ?? 'planned',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    public function deleteEstimate($id)
    {
        return DB::transaction(function () use ($id) {
            $estimate = Estimate::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($estimate->invoice) {
                $hasPayments = $estimate->invoice->payments()->exists();
                if ($hasPayments) {
                    throw new \Exception('Cannot delete quote because linked invoice has recorded payments.');
                }
            }

            foreach ($estimate->estimateDetails as $detail) {
                if ($detail->medical_detail_id) {
                    $medDetail = MedicalDetail::find($detail->medical_detail_id);
                    if ($medDetail) {
                        $remTeeth = TreatmentTeeth::where('medical_detail_id', $medDetail->id)->get();
                        foreach ($remTeeth as $rt) {
                            ToothFinding::where('treatment_teeth_id', $rt->id)->forceDelete();
                            $rt->delete();
                        }
                        $medDetail->delete();
                    }
                }
                $remTeeth = EstimateTooth::where('estimate_detail_id', $detail->id)->get();
                foreach ($remTeeth as $rt) {
                    EstimateToothFinding::where('estimate_teeth_id', $rt->id)->forceDelete();
                    $rt->delete();
                }
                $detail->delete();
            }

            return $estimate->delete();
        });
    }

    public function approveItem(int $quoteId, int $itemId)
    {
        return $this->approveSelectedItemsAndCreateInvoice($quoteId, [$itemId]);
    }

    public function rejectItem(int $quoteId, int $itemId, ?string $reason = null)
    {
        return $this->rejectSelectedItems($quoteId, [$itemId], $reason);
    }

    public function approveSelectedItemsAndCreateInvoice(int $quoteId, array $itemIds)
    {
        return DB::transaction(function () use ($quoteId, $itemIds) {
            $estimate = Estimate::where('id', $quoteId)->lockForUpdate()->firstOrFail();

            if (empty($itemIds)) {
                throw new \Exception('Please select at least one quote item before approving.');
            }

            // Lock and fetch selected items
            $items = $estimate->estimateDetails()->whereIn('id', $itemIds)->lockForUpdate()->get();

            if ($items->count() !== count(array_unique($itemIds))) {
                throw new \Exception('One or more selected items do not belong to this quote.');
            }

            // Validate that ALL selected items have DRAFT status
            foreach ($items as $item) {
                if ((int) $item->status !== EstimateDetail::DRAFT) {
                    throw new \Exception("Item '{$item->item_name}' has already been processed or is not in draft status.");
                }
            }

            // Check whether an Invoice already exists for this Quote workflow
            $invoice = MedicalHistory::where('estimate_id', $estimate->id)->lockForUpdate()->first();

            if (!$invoice) {
                $invoiceRepo = app(InvoiceRepositoryInterface::class);
                $dailyDate = $estimate->estimate_at ? Carbon::parse($estimate->estimate_at) : Carbon::today();
                $numberInfo = $invoiceRepo->generateNextInvoiceNumber(null, $dailyDate->format('Y-m-d'));

                $invoice = MedicalHistory::create([
                    'patient_id' => $estimate->patient_id,
                    'doctor_id' => $estimate->doctor_id,
                    'user_id' => $estimate->employee_id ?: ($estimate->user_id ?: auth()->id()),
                    'invoice_id' => $numberInfo['invoice_id'],
                    'invoice_num_year_series' => $numberInfo['invoice_num_year_series'],
                    'prefix_invoice' => $numberInfo['prefix_invoice'],
                    'prefix_format' => $numberInfo['prefix_format'],
                    'prefix_id' => $numberInfo['prefix_id'],
                    'date' => $dailyDate,
                    'visit_type' => 'initial',
                    'diagnosis' => $estimate->diagnosis,
                    'description' => $estimate->description,
                    'medical_alert' => $estimate->medical_alert,
                    'rate' => $estimate->rate ?: 4050.0,
                    'discount_invoice' => $estimate->discount_invoice ?: 0.0,
                    'discount_inv_as' => $estimate->discount_inv_as ?: '%',
                    'status' => MedicalHistory::UNPAID,
                    'type' => 'invoice',
                    'estimate_id' => $estimate->id,
                ]);

                // Create ONE Reception linked to this Invoice
                Reception::create([
                    'patient_id' => $invoice->patient_id,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $invoice->doctor_id,
                    'reception_date' => Carbon::now(),
                    'visit_type' => 'initial',
                    'chief_complaint' => $estimate->chief_complaint ?: $estimate->diagnosis,
                    'status' => Reception::REGISTERED,
                    'notes' => $estimate->description,
                    'created_by' => auth()->id() ?: $invoice->user_id,
                ]);
            } else {
                $hasReception = Reception::where('invoice_id', $invoice->id)->exists();
                if (!$hasReception) {
                    Reception::create([
                        'patient_id' => $invoice->patient_id,
                        'invoice_id' => $invoice->id,
                        'doctor_id' => $invoice->doctor_id,
                        'reception_date' => Carbon::now(),
                        'visit_type' => $invoice->visit_type ?: 'initial',
                        'chief_complaint' => $estimate->chief_complaint ?: $estimate->diagnosis,
                        'status' => Reception::REGISTERED,
                        'notes' => $estimate->description,
                        'created_by' => auth()->id() ?: $invoice->user_id,
                    ]);
                }
            }

            // Transfer each selected item into MedicalDetail
            foreach ($items as $item) {
                $itemType = strtolower($item->item_type ?? 'service');
                $detailableType = $itemType === 'product' ? Product::class : Service::class;
                $detailableId = (int) ($item->detailable_id ?: $item->service_id);

                $medDetail = null;
                if ($item->medical_detail_id) {
                    $medDetail = MedicalDetail::find($item->medical_detail_id);
                }

                if (!$medDetail) {
                    $medDetail = MedicalDetail::create([
                        'medical_history_id' => $invoice->id,
                        'detailable_id' => $detailableId,
                        'detailable_type' => $detailableType,
                        'service_id' => $itemType === 'service' ? $detailableId : null,
                        'quantity' => $item->quantity ?: 1,
                        'unit_price' => $item->unit_price ?: 0,
                        'cost' => $item->cost ?: 0,
                        'cost_amount' => $item->cost_amount ?: (($item->cost ?: 0) * ($item->quantity ?: 1)),
                        'discount' => $item->discount ?: 0,
                        'discount_as' => $item->discount_as ?: '%',
                        'amount' => $item->amount ?: 0,
                        'date' => $item->date ?: Carbon::today(),
                        'note' => $item->note,
                    ]);

                    // Copy teeth and tooth findings to treatment_teeths and tooth_findings
                    $itemTeeth = $item->teeth()->with('toothFindings')->get();
                    foreach ($itemTeeth as $tooth) {
                        $treatmentTooth = TreatmentTeeth::create([
                            'medical_history_id' => $invoice->id,
                            'medical_detail_id' => $medDetail->id,
                            'teeth_number' => $tooth->teeth_number,
                            'class_one' => $tooth->class_one,
                            'class_two' => $tooth->class_two,
                            'class_three' => $tooth->class_three,
                            'class_four' => $tooth->class_four,
                            'class_five' => $tooth->class_five,
                            'class_six' => $tooth->class_six,
                            'tooth_type' => $tooth->tooth_type,
                            'tooth_status' => $tooth->tooth_status,
                        ]);

                        foreach ($tooth->toothFindings as $tFinding) {
                            ToothFinding::create([
                                'treatment_teeth_id' => $treatmentTooth->id,
                                'dental_condition_id' => $this->resolveValidConditionId($tFinding->dental_condition_id),
                                'surface' => $tFinding->surface,
                                'custom_color' => $tFinding->custom_color,
                                'note' => $tFinding->note,
                                'treatment_status' => $tFinding->treatment_status ?: 'observed',
                                'created_by' => auth()->id() ?? $tFinding->created_by,
                            ]);
                        }
                    }

                    $this->updateStock($detailableId, $itemType, (int) $item->quantity, 'decrement');
                } else {
                    $medDetail->update([
                        'quantity' => $item->quantity ?: 1,
                        'unit_price' => $item->unit_price ?: 0,
                        'cost' => $item->cost ?: 0,
                        'cost_amount' => $item->cost_amount ?: (($item->cost ?: 0) * ($item->quantity ?: 1)),
                        'discount' => $item->discount ?: 0,
                        'discount_as' => $item->discount_as ?: '%',
                        'amount' => $item->amount ?: 0,
                        'date' => $item->date ?: Carbon::today(),
                        'note' => $item->note,
                    ]);

                    $existingTTeeth = TreatmentTeeth::where('medical_detail_id', $medDetail->id)->get();
                    foreach ($existingTTeeth as $extt) {
                        ToothFinding::where('treatment_teeth_id', $extt->id)->forceDelete();
                        $extt->delete();
                    }

                    $itemTeeth = $item->teeth()->with('toothFindings')->get();
                    foreach ($itemTeeth as $tooth) {
                        $treatmentTooth = TreatmentTeeth::create([
                            'medical_history_id' => $invoice->id,
                            'medical_detail_id' => $medDetail->id,
                            'teeth_number' => $tooth->teeth_number,
                            'class_one' => $tooth->class_one,
                            'class_two' => $tooth->class_two,
                            'class_three' => $tooth->class_three,
                            'class_four' => $tooth->class_four,
                            'class_five' => $tooth->class_five,
                            'class_six' => $tooth->class_six,
                            'tooth_type' => $tooth->tooth_type,
                            'tooth_status' => $tooth->tooth_status,
                        ]);

                        foreach ($tooth->toothFindings as $tFinding) {
                            ToothFinding::create([
                                'treatment_teeth_id' => $treatmentTooth->id,
                                'dental_condition_id' => $this->resolveValidConditionId($tFinding->dental_condition_id),
                                'surface' => $tFinding->surface,
                                'custom_color' => $tFinding->custom_color,
                                'note' => $tFinding->note,
                                'treatment_status' => $tFinding->treatment_status ?: 'observed',
                                'created_by' => auth()->id() ?? $tFinding->created_by,
                            ]);
                        }
                    }
                }

                $item->update([
                    'status' => EstimateDetail::APPROVED,
                    'approved_by' => auth()->id(),
                    'approved_at' => Carbon::now(),
                    'rejected_by' => null,
                    'rejected_at' => null,
                    'rejected_note' => null,
                    'medical_detail_id' => $medDetail->id,
                ]);
            }

            $invoice->recalculateFinancials(true);
            $estimate->recalculateStatus();

            return [
                'success' => true,
                'message' => count($items) . ' quote item(s) approved and invoice created/updated.',
                'invoice_id' => $invoice->id,
                'redirect_url' => route('invoices.edit', $invoice->id),
                'estimate' => $estimate->fresh(),
            ];
        });
    }

    public function rejectSelectedItems(int $quoteId, array $itemIds, ?string $reason = null)
    {
        return DB::transaction(function () use ($quoteId, $itemIds, $reason) {
            $estimate = Estimate::where('id', $quoteId)->lockForUpdate()->firstOrFail();

            if (empty($itemIds)) {
                throw new \Exception('Please select at least one quote item before rejecting.');
            }

            // Lock and fetch selected items
            $items = $estimate->estimateDetails()->whereIn('id', $itemIds)->lockForUpdate()->get();

            if ($items->count() !== count(array_unique($itemIds))) {
                throw new \Exception('One or more selected items do not belong to this quote.');
            }

            // Validate that ALL selected items have DRAFT status
            foreach ($items as $item) {
                if ((int) $item->status !== EstimateDetail::DRAFT) {
                    throw new \Exception("Item '{$item->item_name}' has already been processed or is not in draft status.");
                }
            }

            foreach ($items as $item) {
                if ($item->medical_detail_id) {
                    $medDetail = MedicalDetail::find($item->medical_detail_id);
                    if ($medDetail) {
                        $invoice = MedicalHistory::find($medDetail->medical_history_id);
                        TreatmentTeeth::where('medical_detail_id', $medDetail->id)->delete();
                        $itemType = strtolower($item->item_type ?? 'service');
                        $detailableId = (int) ($item->detailable_id ?: $item->service_id);
                        $this->updateStock($detailableId, $itemType, (int) $item->quantity, 'increment');
                        $medDetail->delete();

                        if ($invoice) {
                            $invoice->recalculateFinancials(true);
                        }
                    }
                }

                $item->update([
                    'status' => EstimateDetail::REJECTE,
                    'rejected_by' => auth()->id(),
                    'rejected_at' => Carbon::now(),
                    'rejected_note' => $reason,
                    'approved_by' => null,
                    'approved_at' => null,
                    'medical_detail_id' => null,
                ]);
            }

            $estimate->recalculateStatus();

            return [
                'success' => true,
                'message' => count($items) . ' quote item(s) rejected successfully.',
                'estimate' => $estimate->fresh(),
            ];
        });
    }

    public function calculateQuoteStatus(int $quoteId): int
    {
        $estimate = Estimate::findOrFail($quoteId);
        return $estimate->recalculateStatus();
    }

    public function addItem(int $quoteId, array $itemData)
    {
        return DB::transaction(function () use ($quoteId, $itemData) {
            $estimate = Estimate::where('id', $quoteId)->lockForUpdate()->firstOrFail();

            $qty = max(1, (int) ($itemData['quantity'] ?? 1));
            $unitPrice = max(0, (float) ($itemData['unit_price'] ?? 0));
            $cost = max(0, (float) ($itemData['cost'] ?? 0));
            $discount = max(0, (float) ($itemData['discount'] ?? 0));
            $discountAs = $itemData['discount_as'] ?? '%';

            $gross = $qty * $unitPrice;
            $discAmount = $discountAs === '%' ? ($gross * ($discount / 100)) : $discount;
            $discAmount = min($gross, max(0, $discAmount));
            $amount = max(0, $gross - $discAmount);

            $itemType = strtolower($itemData['item_type'] ?? 'service');
            $detailableType = $itemType === 'product' ? Product::class : Service::class;
            $detailableId = (int) ($itemData['detailable_id'] ?? $itemData['service_id'] ?? 0);

            $detail = $estimate->estimateDetails()->create([
                'detailable_id' => $detailableId,
                'detailable_type' => $detailableType,
                'service_id' => $itemType === 'service' ? $detailableId : null,
                'date' => !empty($itemData['date']) ? Carbon::parse($itemData['date'])->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'cost' => $cost,
                'cost_amount' => $cost * $qty,
                'discount' => $discount,
                'discount_as' => $discountAs,
                'amount' => $amount,
                'note' => $itemData['note'] ?? null,
                'status' => EstimateDetail::DRAFT,
            ]);

            if (!empty($itemData['teeth'])) {
                $teeth = $itemData['teeth'];
                if (is_string($teeth)) {
                    $teeth = explode('|', $teeth);
                }
                if (is_array($teeth)) {
                    $processed = [];
                    $this->syncEstimateTeethAndFindings(
                        $detail,
                        $teeth,
                        $itemData['odontogram_findings'] ?? [],
                        $processed
                    );
                }
            }

            $estimate->recalculateStatus();

            return [
                'success' => true,
                'message' => 'Item added successfully.',
                'item' => $detail->fresh(['teeth.toothFindings.condition']),
                'estimate' => $estimate->fresh(),
            ];
        });
    }

    protected function updateStock(int $itemId, string $type, int $qty, string $direction): void
    {
        if ($type === 'product' && $itemId > 0) {
            $product = Product::find($itemId);
            if ($product && $product->qty !== null) {
                $newQty = $direction === 'decrement' ? max(0, $product->qty - $qty) : $product->qty + $qty;
                $product->update(['qty' => $newQty]);
            }
        } elseif ($type === 'service' && $itemId > 0) {
            $service = Service::find($itemId);
            if ($service && isset($service->type) && $service->type !== 'service' && $service->qty !== null) {
                $newQty = $direction === 'decrement' ? max(0, $service->qty - $qty) : $service->qty + $qty;
                $service->update(['qty' => $newQty]);
            }
        }
    }
}
