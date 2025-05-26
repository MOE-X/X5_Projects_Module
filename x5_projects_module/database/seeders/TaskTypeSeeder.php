<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskType;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskType::create([
            'name' => 'Video',
        ]);
        TaskType::create([
            'name' => 'File',
        ]);
        TaskType::create([
            'name' => 'Normal Task',
        ]);
    }
}
