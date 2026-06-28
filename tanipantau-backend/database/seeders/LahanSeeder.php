<?php

namespace Database\Seeders;

use App\Models\Lahan;
use App\Models\Petani;
use Illuminate\Database\Seeder;

class LahanSeeder extends Seeder
{
    public function run(): void
    {
        // Each Petani has 1-3 Lahan
        Petani::all()->each(function ($petani) {
            Lahan::factory()->count(rand(1, 3))->create([
                'petani_id' => $petani->id
            ]);
        });
    }
}
