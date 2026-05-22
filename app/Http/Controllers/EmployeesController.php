<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeesRequest;
use App\Http\Requests\UpdateEmployeesRequest;
use App\Models\Employees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    private const RELATIONS = ['employeeAttendances', 'employeePayRecords'];

    public function index(Request $request): JsonResponse
    {
        $employees = Employees::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Employees retrieved successfully.', $employees);
    }

    public function store(StoreEmployeesRequest $request): JsonResponse
    {
        $employee = Employees::create($request->validated())->load(self::RELATIONS);

        return $this->success('Employee created successfully.', $employee, 201);
    }

    public function show(Employees $employee): JsonResponse
    {
        return $this->success('Employee retrieved successfully.', $employee->load(self::RELATIONS));
    }

    public function update(UpdateEmployeesRequest $request, Employees $employee): JsonResponse
    {
        $employee->update($request->validated());

        return $this->success('Employee updated successfully.', $employee->load(self::RELATIONS));
    }

    public function destroy(Employees $employee): JsonResponse
    {
        $employee->delete();

        return $this->success('Employee deleted successfully.');
    }
}
