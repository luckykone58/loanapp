<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $host = '127.0.0.1';

        $domain = Domain::query()->firstOrCreate(
            ['host' => $host],
            ['name' => 'Local']
        );

        User::query()->firstOrCreate(
            ['username' => 'superadmin', 'domain_id' => $domain->id],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Super@123'),
                'role' => 'SuperAdmin',
            ]
        );
    }
}


