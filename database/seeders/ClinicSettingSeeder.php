<?php

namespace Database\Seeders;

use App\Models\ClinicSetting;
use Illuminate\Database\Seeder;

class ClinicSettingSeeder extends Seeder
{
    public function run(): void
    {
        ClinicSetting::getSettings();
    }
}
