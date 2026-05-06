<?php

namespace VitalSaaS\VitalAccess\Database\Seeders;

use Illuminate\Database\Seeder;

class VitalAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            VitalAccessModulesSeeder::class,
        ]);
    }
}