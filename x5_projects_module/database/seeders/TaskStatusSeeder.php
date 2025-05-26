<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskStatus::create([
            'name' => 'New',
        ]);
        TaskStatus::create([
            'name' => 'Active',
        ]);
        TaskStatus::create([
            'name' => 'Dev Done',
        ]);
        TaskStatus::create([
            'name' => 'QA Done',
        ]);
        TaskStatus::create([
            'name' => 'Closed',
        ]);
    }
}
