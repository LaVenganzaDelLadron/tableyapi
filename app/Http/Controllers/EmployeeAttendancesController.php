<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeAttendancesRequest;
use App\Http\Requests\UpdateEmployeeAttendancesRequest;
use App\Models\EmployeeAttendances;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeAttendancesController extends Controller
{
    private const RELATIONS = ['employee', 'employeePayRecords'];

    public function index(Request $request): JsonResponse
    {
        $attendances = EmployeeAttendances::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Employee attendances retrieved successfully.', $attendances);
    }

    public function store(StoreEmployeeAttendancesRequest $request): JsonResponse
    {
        $attendance = DB::transaction(fn () => EmployeeAttendances::create($request->validated())->load(self::RELATIONS));

        return $this->success('Employee attendance created successfully.', $attendance, 201);
    }

    public function show(EmployeeAttendances $employeeAttendance): JsonResponse
    {
        return $this->success('Employee attendance retrieved successfully.', $employeeAttendance->load(self::RELATIONS));
    }

    public function update(UpdateEmployeeAttendancesRequest $request, EmployeeAttendances $employeeAttendance): JsonResponse
    {
        $employeeAttendance = DB::transaction(function () use ($request, $employeeAttendance) {
            $employeeAttendance->update($request->validated());

            return $employeeAttendance->load(self::RELATIONS);
        });

        return $this->success('Employee attendance updated successfully.', $employeeAttendance);
    }

    public function destroy(EmployeeAttendances $employeeAttendance): JsonResponse
    {
        $employeeAttendance->delete();

        return $this->success('Employee attendance deleted successfully.');
    }
}
