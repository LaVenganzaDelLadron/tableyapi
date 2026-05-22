<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationsRequest;
use App\Http\Requests\UpdateNotificationsRequest;
use App\Models\Notifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    private const RELATIONS = ['user'];

    public function index(Request $request): JsonResponse
    {
        $notifications = Notifications::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Notifications retrieved successfully.', $notifications);
    }

    public function store(StoreNotificationsRequest $request): JsonResponse
    {
        $notification = Notifications::create($request->validated())->load(self::RELATIONS);

        return $this->success('Notification created successfully.', $notification, 201);
    }

    public function show(Notifications $notification): JsonResponse
    {
        return $this->success('Notification retrieved successfully.', $notification->load(self::RELATIONS));
    }

    public function update(UpdateNotificationsRequest $request, Notifications $notification): JsonResponse
    {
        $notification->update($request->validated());

        return $this->success('Notification updated successfully.', $notification->load(self::RELATIONS));
    }

    public function destroy(Notifications $notification): JsonResponse
    {
        $notification->delete();

        return $this->success('Notification deleted successfully.');
    }
}
