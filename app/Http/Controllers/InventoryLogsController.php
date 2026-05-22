<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryLogsRequest;
use App\Http\Requests\UpdateInventoryLogsRequest;
use App\Models\InventoryLogs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryLogsController extends Controller
{
    private const RELATIONS = ['product', 'order', 'productionBatch'];

    public function index(Request $request): JsonResponse
    {
        $logs = InventoryLogs::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Inventory logs retrieved successfully.', $logs);
    }

    public function store(StoreInventoryLogsRequest $request): JsonResponse
    {
        $log = DB::transaction(fn () => InventoryLogs::create($request->validated())->load(self::RELATIONS));

        return $this->success('Inventory log created successfully.', $log, 201);
    }

    public function show(InventoryLogs $inventoryLog): JsonResponse
    {
        return $this->success('Inventory log retrieved successfully.', $inventoryLog->load(self::RELATIONS));
    }

    public function update(UpdateInventoryLogsRequest $request, InventoryLogs $inventoryLog): JsonResponse
    {
        $inventoryLog = DB::transaction(function () use ($request, $inventoryLog) {
            $inventoryLog->update($request->validated());

            return $inventoryLog->load(self::RELATIONS);
        });

        return $this->success('Inventory log updated successfully.', $inventoryLog);
    }

    public function destroy(InventoryLogs $inventoryLog): JsonResponse
    {
        $inventoryLog->delete();

        return $this->success('Inventory log deleted successfully.');
    }
}
