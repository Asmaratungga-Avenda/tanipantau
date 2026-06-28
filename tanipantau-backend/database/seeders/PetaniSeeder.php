<?php

namespace Database\Seeders;

use App\Models\Petani;
use Illuminate\Database\Seeder;

class PetaniSeeder extends Seeder
{
    public function run(): void
    {
        Petani::factory()->count(10)->create();
    }
}
