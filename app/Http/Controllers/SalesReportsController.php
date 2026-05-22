<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReportsRequest;
use App\Http\Requests\UpdateSalesReportsRequest;
use App\Models\SalesReports;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = SalesReports::query()->paginate($request->integer('per_page', 15));

        return $this->success('Sales reports retrieved successfully.', $reports);
    }

    public function store(StoreSalesReportsRequest $request): JsonResponse
    {
        $report = DB::transaction(fn () => SalesReports::create($request->validated()));

        return $this->success('Sales report created successfully.', $report, 201);
    }

    public function show(SalesReports $salesReport): JsonResponse
    {
        return $this->success('Sales report retrieved successfully.', $salesReport);
    }

    public function update(UpdateSalesReportsRequest $request, SalesReports $salesReport): JsonResponse
    {
        $salesReport = DB::transaction(function () use ($request, $salesReport) {
            $salesReport->update($request->validated());

            return $salesReport;
        });

        return $this->success('Sales report updated successfully.', $salesReport);
    }

    public function destroy(SalesReports $salesReport): JsonResponse
    {
        $salesReport->delete();

        return $this->success('Sales report deleted successfully.');
    }
}
