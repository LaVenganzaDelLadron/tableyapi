<?php

namespace Database\Seeders;

use App\Models\Notifications;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tableya.test')->first();
        $customer = User::where('email', 'maria.customer@tableya.test')->first();

        $notifications = [
            ['user_id' => $admin?->id, 'title' => 'Low Stock Alert', 'message' => 'Tableya Wholesale Box is below the preferred reseller stock level.', 'type' => 'inventory', 'is_read' => false, 'read_at' => null],
            ['user_id' => $admin?->id, 'title' => 'Production Completed', 'message' => 'A new cacao production batch has been added to inventory.', 'type' => 'production', 'is_read' => true, 'read_at' => now()->subDay()],
            ['user_id' => $customer?->id, 'title' => 'Order Update', 'message' => 'Your tableya order has been completed. Salamat sa pag-order!', 'type' => 'order', 'is_read' => false, 'read_at' => null],
        ];

        foreach ($notifications as $notification) {
            if (! $notification['user_id']) {
                continue;
            }

            Notifications::updateOrCreate(
                ['user_id' => $notification['user_id'], 'title' => $notification['title'], 'type' => $notification['type']],
                $notification
            );
        }
    }
}
