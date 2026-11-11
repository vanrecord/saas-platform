<?php

namespace App\Repositories;

use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\MedicalDetail;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Prefix;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
use App\Models\Tenant\ToothFinding;
use App\Models\Tenant\TreatmentTeeth;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(MedicalHistory::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(invoice_number)'), 'LIKE', "%{$term}%")
                      ->orWhere(DB::raw('LOWER(diagnosis)'), 'LIKE', "%{$term}%")
                      ->orWhere(DB::raw('LOWER(visit_type)'), 'LIKE', "%{$term}%")
                      ->orWhereHas('patient', function ($pQuery) use ($term, $value) {
                          $pQuery->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                                 ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                                 ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                                 ->orWhere('phone', 'LIKE', "%{$term}%");

                          if (is_numeric($value)) {
                              $pQuery->orWhere('id_number', (int) $value)
                                     ->orWhere('id', (int) $value);
                          }
                      });

                if (is_numeric($value)) {
                    $query->orWhere('estimate_id', (int) $value)
                          ->orWhere('id', (int) $value);
                }
            });
        });

        $dateRangeFilter = AllowedFilter::callback('date_range', function ($query, $value) {
            if (is_array($value)) {
                $start = $value['start'] ?? $value[0] ?? null;
                $end = $value['end'] ?? $value[1] ?? null;
            } else {
                $parts = explode(',', (string) $value);
                $start = $parts[0] ?? null;
                $end = $parts[1] ?? null;
            }

            if ($start) {
                $query->whereDate('date', '>=', Carbon::parse($start)->startOfDay());
            }
            if ($end) {
                $query->whereDate('date', '<=', Carbon::parse($end)->endOfDay());
            }
        });

        $startDateFilter = AllowedFilter::callback('start_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('date', '>=', Carbon::parse($value)->startOfDay());
            }
        });

        $endDateFilter = AllowedFilter::callback('end_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('date', '<=', Carbon::parse($value)->endOfDay());
            }
        });

        $statusFilter = AllowedFilter::callback('status', function ($query, $value) {
            $val = strtolower(trim((string) $value));
            if ($val === 'paid' || $val === (string) MedicalHistory::PAID) {
                $query->where(function ($q) {
                    $q->where('status', MedicalHistory::PAID)
                      ->orWhere(function ($sub) {
                          $sub->where('total', '>', 0)->where('balance', '<=', 0.001);
                      });
                });
            } elseif ($val === 'unpaid' || $val === (string) MedicalHistory::UNPAID) {
                $query->where(function ($q) {
                    $q->where('status', MedicalHistory::UNPAID)
                      ->orWhere(function ($sub) {
                          $sub->where('balance', '>', 0.001)->where('paid_usd', '<=', 0.001);
                      });
                });
            } elseif ($val === 'partial' || $val === (string) MedicalHistory::PARTIAL) {
                $query->where(function ($q) {
                    $q->where('status', MedicalHistory::PARTIAL)
                      ->orWhere(function ($sub) {
                          $sub->where('balance', '>', 0.001)->where('paid_usd', '>', 0.001);
                      });
                });
            } elseif ($val === 'refunded' || $val === (string) MedicalHistory::REFUNED) {
                $query->where(function ($q) {
                    $q->where('status', MedicalHistory::REFUNED)
                      ->orWhere('refunded', '>', 0.001);
                });
            } elseif ($val === 'partially_refunded' || $val === 'partially refunded' || $val === (string) MedicalHistory::PARTIALLY_REFUNED) {
                $query->where('status', MedicalHistory::PARTIALLY_REFUNED);
            }
        });

        return QueryBuilder::for(MedicalHistory::class)
            ->with([
                'patient',
                'doctor.user.profile',
                'assistant.profile',
                'payments',
            ])
            ->defaultSort('-date')
            ->allowedFields([
                'id',
                'date',
                'invoice_number',
                'estimate_id',
                'patient_id',
                'doctor_id',
                'user_id',
                'visit_type',
                'diagnosis',
                'total',
                'refunded',
                'balance',
                'paid_usd',
                'paid_kh',
                'status',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'date',
                'invoice_number',
                'estimate_id',
                'patient_id',
                'visit_type',
                'diagnosis',
                'total',
                'refunded',
                'balance',
                'created_at',
            ])
            ->allowedFilters([
                'invoice_number',
                'estimate_id',
                'visit_type',
                'diagnosis',
                AllowedFilter::exact('patient_id'),
                $startDateFilter,
                $endDateFilter,
                $dateRangeFilter,
                $statusFilter,
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'date', label: 'Daily Date', sortable: true);
        $table->column(key: 'invoice_number', label: 'Invoice #', sortable: true, searchable: true);
        $table->column(key: 'estimate_id', label: 'Quote #', sortable: true);
        $table->column(key: 'patient_id_number', label: 'ID Patient', sortable: true);
        $table->column(key: 'patient_name', label: 'Patient', sortable: true);
        $table->column(key: 'visit_type', label: 'Visit Type', sortable: true);
        $table->column(key: 'diagnosis', label: 'Diagnosis', sortable: true);
        $table->column(key: 'total', label: 'Total ($)', sortable: true);
        $table->column(key: 'refunded', label: 'Refunded ($)', sortable: true);
        $table->column(key: 'balance', label: 'Balance ($)', sortable: true);
        $table->column(key: 'status', label: 'Status', sortable: true);
    }

    public function findWithDetails($id)
    {
        return MedicalHistory::with([
            'patient',
            'doctor.user.profile',
            'assistant.profile',
            'medicalDetails.detailable',
            'medicalDetails.treatmentTeeth.toothFindings.condition',
            'payments.paymentMethod',
            'prefix',
            'receptions.doctor.user.profile',
        ])->findOrFail($id);
    }

    public function generateNextInvoiceNumber(?int $prefixId = null, ?string $dailyDate = null): array
    {
        $prefix = null;
        if ($prefixId) {
            $prefix = Prefix::find($prefixId);
        }
        if (!$prefix) {
            $prefix = Prefix::where('default_prefix', true)->first();
        }
        if (!$prefix) {
            $prefix = Prefix::first();
        }

        $prefixName = $prefix ? ($prefix->prefix_name ?: 'INV') : 'INV';
        $prefixFormat = $prefix ? ($prefix->prefix_format ?: 'Year-Series') : 'Year-Series';
        $prefixDbId = $prefix ? $prefix->id : null;

        $targetDate = $dailyDate ? Carbon::parse($dailyDate) : Carbon::today();
        $year = $targetDate->format('Y');

        if ($prefixFormat === 'Year-Series') {
            // Concurrency-safe lock in PostgreSQL (order by desc with lockForUpdate)
            $maxYearSeries = DB::table('medical_histories')
                ->where(function ($q) use ($prefixDbId, $prefixName) {
                    if ($prefixDbId) {
                        $q->where('prefix_id', $prefixDbId)->orWhere('prefix_invoice', $prefixName);
                    } else {
                        $q->where('prefix_invoice', $prefixName);
                    }
                })
                ->whereYear('date', $year)
                ->orderByDesc('invoice_num_year_series')
                ->lockForUpdate()
                ->value('invoice_num_year_series');

            $nextSequence = ($maxYearSeries ?? 0) + 1;
            $invoiceNumber = sprintf('%s-%s-%06d', $prefixName, $year, $nextSequence);

            return [
                'invoice_number' => $invoiceNumber,
                'prefix_id' => $prefixDbId,
                'prefix_invoice' => $prefixName,
                'prefix_format' => $prefixFormat,
                'invoice_id' => null,
                'invoice_num_year_series' => $nextSequence,
            ];
        } else {
            // Series
            $maxSeries = DB::table('medical_histories')
                ->where(function ($q) use ($prefixDbId, $prefixName) {
                    if ($prefixDbId) {
                        $q->where('prefix_id', $prefixDbId)->orWhere('prefix_invoice', $prefixName);
                    } else {
                        $q->where('prefix_invoice', $prefixName);
                    }
                })
                ->orderByDesc('invoice_id')
                ->lockForUpdate()
                ->value('invoice_id');

            $nextSequence = ($maxSeries ?? 0) + 1;
            $invoiceNumber = sprintf('%s-%06d', $prefixName, $nextSequence);

            return [
                'invoice_number' => $invoiceNumber,
                'prefix_id' => $prefixDbId,
                'prefix_invoice' => $prefixName,
                'prefix_format' => $prefixFormat,
                'invoice_id' => $nextSequence,
                'invoice_num_year_series' => null,
            ];
        }
    }

    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dailyDate = !empty($data['date']) ? Carbon::parse($data['date']) : Carbon::now();
            $prefixId = !empty($data['prefix_id']) ? (int) $data['prefix_id'] : null;

            // Generate safe sequence & number
            $numberInfo = $this->generateNextInvoiceNumber($prefixId, $dailyDate->format('Y-m-d'));

            $exchangeRate = !empty($data['rate']) ? (float) $data['rate'] : 4050.0;
            $overallDiscount = !empty($data['discount_invoice']) ? (float) $data['discount_invoice'] : 0.0;
            $overallDiscountType = !empty($data['discount_inv_as']) ? $data['discount_inv_as'] : '%';

            // Calculate items
            $items = $data['items'] ?? [];
            $subtotal = 0.0;
            $preparedDetails = [];

            foreach ($items as $item) {
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
                $cost = max(0, (float) ($item['cost'] ?? 0));
                $discount = max(0, (float) ($item['discount'] ?? 0));
                $discountAs = $item['discount_as'] ?? '%';

                $gross = $qty * $unitPrice;
                $rowDiscount = 0.0;
                if ($discountAs === '%') {
                    $rowDiscount = $gross * ($discount / 100);
                } else {
                    $rowDiscount = $discount;
                }
                $rowDiscount = min($gross, max(0, $rowDiscount));
                $amount = max(0, $gross - $rowDiscount);
                $costAmount = $qty * $cost;

                $subtotal += $amount;

                $itemType = strtolower($item['item_type'] ?? 'service');
                $detailableType = $itemType === 'product' ? Product::class : Service::class;
                $detailableId = (int) ($item['detailable_id'] ?? $item['service_id'] ?? 0);

                $operateDate = !empty($item['date']) ? Carbon::parse($item['date'])->format('Y-m-d') : $dailyDate->format('Y-m-d');

                $preparedDetails[] = [
                    'detailable_id' => $detailableId,
                    'detailable_type' => $detailableType,
                    'service_id' => $itemType === 'service' ? $detailableId : null,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost' => $cost,
                    'cost_amount' => $costAmount,
                    'discount' => $discount,
                    'discount_as' => $discountAs,
                    'amount' => $amount,
                    'date' => $operateDate,
                    'note' => $item['note'] ?? null,
                    'teeth' => $item['teeth'] ?? [],
                ];
            }

            // Calculate overall discount & total
            $overallDiscountAmount = 0.0;
            if ($overallDiscountType === '%') {
                $overallDiscountAmount = $subtotal * ($overallDiscount / 100);
            } else {
                $overallDiscountAmount = $overallDiscount;
            }
            $overallDiscountAmount = min($subtotal, max(0, $overallDiscountAmount));
            $total = max(0, $subtotal - $overallDiscountAmount);

            // Paid, Refunded, Balance
            $paidUsd = 0.0;
            $paidKh = 0.0;
            $refunded = 0.0;
            $balance = $total;

            $status = MedicalHistory::determineStatus($total, $paidUsd, $refunded, $balance);

            // Create Medical History
            $history = MedicalHistory::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => !empty($data['doctor_id']) ? $data['doctor_id'] : null,
                'user_id' => !empty($data['user_id']) ? $data['user_id'] : (!empty($data['assistant_id']) ? $data['assistant_id'] : auth()->id()),
                'invoice_id' => $numberInfo['invoice_id'],
                'invoice_num_year_series' => $numberInfo['invoice_num_year_series'],
                'prefix_invoice' => $numberInfo['prefix_invoice'],
                'prefix_format' => $numberInfo['prefix_format'],
                'prefix_id' => $numberInfo['prefix_id'],
                'date' => $dailyDate,
                'visit_type' => $data['visit_type'] ?? 'initial',
                'diagnosis' => $data['diagnosis'] ?? null,
                'description' => $data['description'] ?? null,
                'medical_alert' => $data['medical_alert'] ?? null,
                'rate' => $exchangeRate,
                'discount_invoice' => $overallDiscount,
                'discount_inv_as' => $overallDiscountType,
                'status' => $status,
                'type' => 'invoice',
                'estimate_id' => !empty($data['estimate_id']) ? $data['estimate_id'] : null,
            ]);

            // Save details & treatment teeth
            $odontogramFindings = $data['odontogram_findings'] ?? [];
            $processedToothSurfaces = [];

            foreach ($preparedDetails as $pDetail) {
                $teethData = $pDetail['teeth'];
                unset($pDetail['teeth']);

                $detail = $history->medicalDetails()->create($pDetail);

                if (!empty($teethData) && is_array($teethData)) {
                    $this->syncTreatmentTeethAndFindings(
                        $history,
                        $detail,
                        $teethData,
                        $odontogramFindings,
                        $processedToothSurfaces
                    );
                }
            }

            // Attach any remaining odontogram findings to first detail
            $this->attachUnassignedOdontogramFindings($history, $odontogramFindings, $processedToothSurfaces);

            return $history;
        });
    }

    public function updateInvoice($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $history = MedicalHistory::findOrFail($id);

            $dailyDate = !empty($data['date']) ? Carbon::parse($data['date']) : $history->date;
            $exchangeRate = !empty($data['rate']) ? (float) $data['rate'] : ($history->rate ?: 4050.0);
            $overallDiscount = isset($data['discount_invoice']) ? (float) $data['discount_invoice'] : ($history->discount_invoice ?: 0.0);
            $overallDiscountType = !empty($data['discount_inv_as']) ? $data['discount_inv_as'] : ($history->discount_inv_as ?: '%');

            // Calculate items
            $items = $data['items'] ?? [];
            $subtotal = 0.0;
            $preparedDetails = [];

            foreach ($items as $item) {
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
                $cost = max(0, (float) ($item['cost'] ?? 0));
                $discount = max(0, (float) ($item['discount'] ?? 0));
                $discountAs = $item['discount_as'] ?? '%';

                $gross = $qty * $unitPrice;
                $rowDiscount = 0.0;
                if ($discountAs === '%') {
                    $rowDiscount = $gross * ($discount / 100);
                } else {
                    $rowDiscount = $discount;
                }
                $rowDiscount = min($gross, max(0, $rowDiscount));
                $amount = max(0, $gross - $rowDiscount);
                $costAmount = $qty * $cost;

                $subtotal += $amount;

                $itemType = strtolower($item['item_type'] ?? 'service');
                $detailableType = $itemType === 'product' ? Product::class : Service::class;
                $detailableId = (int) ($item['detailable_id'] ?? $item['service_id'] ?? 0);

                $operateDate = !empty($item['date']) ? Carbon::parse($item['date'])->format('Y-m-d') : $dailyDate->format('Y-m-d');

                $preparedDetails[] = [
                    'id' => $item['id'] ?? null,
                    'detailable_id' => $detailableId,
                    'detailable_type' => $detailableType,
                    'service_id' => $itemType === 'service' ? $detailableId : null,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost' => $cost,
                    'cost_amount' => $costAmount,
                    'discount' => $discount,
                    'discount_as' => $discountAs,
                    'amount' => $amount,
                    'date' => $operateDate,
                    'note' => $item['note'] ?? null,
                    'teeth' => $item['teeth'] ?? [],
                ];
            }

            // Overall discount & total
            $overallDiscountAmount = 0.0;
            if ($overallDiscountType === '%') {
                $overallDiscountAmount = $subtotal * ($overallDiscount / 100);
            } else {
                $overallDiscountAmount = $overallDiscount;
            }
            $overallDiscountAmount = min($subtotal, max(0, $overallDiscountAmount));
            $total = max(0, $subtotal - $overallDiscountAmount);

            // Fetch actual payments and refunds to recalculate balance accurately
            $payments = Payment::where('medical_history_id', $id)->get();
            $paidUsd = (float) $payments->where('amount', '>', 0)->sum('amount');
            $paidKh = (float) $payments->where('amount_kh', '>', 0)->sum('amount_kh');
            $refunded = (float) $payments->where('amount', '<', 0)->sum(function ($p) {
                return abs((float) $p->amount);
            });

            $balance = max(0, $total - $paidUsd + $refunded);

            // Determine status using MedicalHistory constants
            $status = MedicalHistory::determineStatus($total, $paidUsd, $refunded, $balance);

            // Update Medical History (Invoice number is strictly preserved)
            $history->update([
                'patient_id' => $data['patient_id'] ?? $history->patient_id,
                'doctor_id' => !empty($data['doctor_id']) ? $data['doctor_id'] : null,
                'user_id' => !empty($data['user_id']) ? $data['user_id'] : (!empty($data['assistant_id']) ? $data['assistant_id'] : $history->user_id),
                'date' => $dailyDate,
                'visit_type' => $data['visit_type'] ?? $history->visit_type,
                'diagnosis' => $data['diagnosis'] ?? $history->diagnosis,
                'description' => $data['description'] ?? $history->description,
                'medical_alert' => $data['medical_alert'] ?? $history->medical_alert,
                'rate' => $exchangeRate,
                'discount_invoice' => $overallDiscount,
                'discount_inv_as' => $overallDiscountType,
                'status' => $status,
                'estimate_id' => !empty($data['estimate_id']) ? $data['estimate_id'] : $history->estimate_id,
            ]);

            // Sync details: Keep tracked IDs
            $keptDetailIds = [];
            $odontogramFindings = $data['odontogram_findings'] ?? [];
            $processedToothSurfaces = [];

            foreach ($preparedDetails as $pDetail) {
                $detailId = $pDetail['id'];
                $teethData = $pDetail['teeth'];
                unset($pDetail['id'], $pDetail['teeth']);

                $detail = null;
                if ($detailId) {
                    $detail = MedicalDetail::where('medical_history_id', $history->id)->find($detailId);
                }

                if ($detail) {
                    $detail->update($pDetail);
                } else {
                    $detail = $history->medicalDetails()->create($pDetail);
                }

                $keptDetailIds[] = $detail->id;

                // Remove existing treatment teeth and findings for this detail before re-creating
                $oldTeeth = TreatmentTeeth::where('medical_detail_id', $detail->id)->get();
                foreach ($oldTeeth as $ot) {
                    ToothFinding::where('treatment_teeth_id', $ot->id)->forceDelete();
                    $ot->delete();
                }

                if (!empty($teethData) && is_array($teethData)) {
                    $this->syncTreatmentTeethAndFindings(
                        $history,
                        $detail,
                        $teethData,
                        $odontogramFindings,
                        $processedToothSurfaces
                    );
                }
            }

            // Remove deleted medical details and their associated teeth/findings
            $removedDetails = MedicalDetail::where('medical_history_id', $history->id)
                ->whereNotIn('id', $keptDetailIds)
                ->get();

            foreach ($removedDetails as $remDetail) {
                $remTeeth = TreatmentTeeth::where('medical_detail_id', $remDetail->id)->get();
                foreach ($remTeeth as $rt) {
                    ToothFinding::where('treatment_teeth_id', $rt->id)->forceDelete();
                    $rt->delete();
                }
                $remDetail->delete();
            }

            // Attach any remaining unassigned odontogram findings to first detail
            $this->attachUnassignedOdontogramFindings($history, $odontogramFindings, $processedToothSurfaces);

            return $history;
        });
    }

    protected function syncTreatmentTeethAndFindings(
        MedicalHistory $history,
        MedicalDetail $detail,
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
                        'treatment_status' => $finding['treatment_status'] ?? $finding['status'] ?? 'observed',
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
                                    'treatment_status' => $oFinding['treatment_status'] ?? $oFinding['status'] ?? 'observed',
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
                            'treatment_status' => 'observed',
                        ];
                    }
                    $processedToothSurfaces[$key] = true;
                }
            }

            $treatmentTooth = TreatmentTeeth::create([
                'medical_history_id' => $history->id,
                'medical_detail_id' => $detail->id,
                'teeth_number' => $toothNumStr,
                'class_one' => $hasTop,
                'class_two' => $hasBottom,
                'class_three' => $hasLeft,
                'class_four' => $hasRight,
                'class_five' => $hasCenter,
                'class_six' => $hasWhole,
                'tooth_type' => $toothItem['tooth_type'] ?? null,
                'tooth_status' => $toothItem['tooth_status'] ?? null,
            ]);

            foreach ($findingsToCreate as $fData) {
                ToothFinding::create([
                    'treatment_teeth_id' => $treatmentTooth->id,
                    'dental_condition_id' => $fData['dental_condition_id'] ?? null,
                    'surface' => $fData['surface'] ?? 'whole',
                    'custom_color' => $fData['custom_color'] ?? null,
                    'note' => $fData['note'] ?? null,
                    'treatment_status' => $fData['treatment_status'] ?? 'observed',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    protected function attachUnassignedOdontogramFindings(
        MedicalHistory $history,
        array $odontogramFindings = [],
        array $processedToothSurfaces = []
    ): void {
        if (empty($odontogramFindings)) {
            return;
        }

        $fallbackDetail = $history->medicalDetails()->first();
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

            $treatmentTooth = TreatmentTeeth::create([
                'medical_history_id' => $history->id,
                'medical_detail_id' => $fallbackDetail->id,
                'teeth_number' => $toothNumStr,
                'class_one' => $hasTop,
                'class_two' => $hasBottom,
                'class_three' => $hasLeft,
                'class_four' => $hasRight,
                'class_five' => $hasCenter,
                'class_six' => $hasWhole,
            ]);

            foreach ($findingsList as $fData) {
                ToothFinding::create([
                    'treatment_teeth_id' => $treatmentTooth->id,
                    'dental_condition_id' => $fData['dental_condition_id'] ?? $fData['condition_id'] ?? null,
                    'surface' => $fData['surface'] ?? 'whole',
                    'custom_color' => $fData['custom_color'] ?? $fData['color'] ?? null,
                    'note' => $fData['note'] ?? null,
                    'treatment_status' => $fData['treatment_status'] ?? $fData['status'] ?? 'observed',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    public function deleteInvoice($id)
    {
        return DB::transaction(function () use ($id) {
            $history = MedicalHistory::findOrFail($id);
            $history->medicalDetails()->delete();
            $history->treatmentTeeths()->delete();
            return $history->delete();
        });
    }
}
