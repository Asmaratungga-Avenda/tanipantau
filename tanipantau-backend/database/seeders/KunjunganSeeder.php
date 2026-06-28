<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Lahan;
use Illuminate\Database\Seeder;

class KunjunganSeeder extends Seeder
{
    public function run(): void
    {
        // Add random visits to some Lahan
        Lahan::inRandomOrder()->limit(15)->get()->each(function ($lahan) {
            Kunjungan::factory()->count(rand(1, 4))->create([
                'lahan_id' => $lahan->id
            ]);
        });
    }
}
