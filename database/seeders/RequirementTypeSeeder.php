<?php

namespace Database\Seeders;

use App\Models\RequirementType;
use Illuminate\Database\Seeder;

class RequirementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RequirementType::create([
            'os' => RequirementType::WINDOWS_OS_TYPE,
            'potential' => RequirementType::MINIMUM_POTENTIAL_TYPE,
        ]);
        RequirementType::create([
            'os' => RequirementType::WINDOWS_OS_TYPE,
            'potential' => RequirementType::RECOMMENDED_POTENTIAL_TYPE,
        ]);
        RequirementType::create([
            'os' => RequirementType::MAC_OS_TYPE,
            'potential' => RequirementType::MINIMUM_POTENTIAL_TYPE,
        ]);
        RequirementType::create([
            'os' => RequirementType::MAC_OS_TYPE,
            'potential' => RequirementType::RECOMMENDED_POTENTIAL_TYPE,
        ]);
        RequirementType::create([
            'os' => RequirementType::LINUX_OS_TYPE,
            'potential' => RequirementType::MINIMUM_POTENTIAL_TYPE,
        ]);
        RequirementType::create([
            'os' => RequirementType::LINUX_OS_TYPE,
            'potential' => RequirementType::RECOMMENDED_POTENTIAL_TYPE,
        ]);
    }
}
