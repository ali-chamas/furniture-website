<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        OrderStatus::create(['status_name' => 'Pending']);
        OrderStatus::create(['status_name' => 'Delivered']);
        OrderStatus::create(['status_name' => 'Cancelled']);
    }
}
