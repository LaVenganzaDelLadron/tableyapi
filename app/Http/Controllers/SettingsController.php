<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $settings = Settings::query()->paginate($request->integer('per_page', 15));

        return $this->success('Settings retrieved successfully.', $settings);
    }

    public function store(StoreSettingsRequest $request): JsonResponse
    {
        $settings = Settings::create($request->validated());

        return $this->success('Settings created successfully.', $settings, 201);
    }

    public function show(Settings $settings): JsonResponse
    {
        return $this->success('Settings retrieved successfully.', $settings);
    }

    public function update(UpdateSettingsRequest $request, Settings $settings): JsonResponse
    {
        $settings->update($request->validated());

        return $this->success('Settings updated successfully.', $settings);
    }

    public function destroy(Settings $settings): JsonResponse
    {
        $settings->delete();

        return $this->success('Settings deleted successfully.');
    }
}
