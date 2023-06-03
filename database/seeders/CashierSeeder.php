<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => 'cashier@gmail.com',
        ], [
            'name' => 'Cashier',
            'password' => bcrypt('cashier123!'),
        ]);
        $user->assignRole(RoleEnum::CASHIER->value);
    }
}
