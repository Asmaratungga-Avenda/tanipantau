<?php

namespace Database\Factories;

use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kunjungan>
 */
class KunjunganFactory extends Factory
{
    protected $model = Kunjungan::class;

    public function definition(): array
    {
        return [
            'lahan_id' => Lahan::factory(),
            'petugas_id' => User::where('role', 'petugas')->inRandomOrder()->first() ?? User::factory(),
            'tanggal_kunjungan' => fake()->dateTimeBetween('-1 month', 'now'),
            'kondisi_lahan' => fake()->randomElement(['Sangat Baik', 'Baik', 'Sedang', 'Kurang Baik', 'Sangat Kurang Baik']),
            'catatan' => fake()->sentence(),
            'foto' => null,
            'status_tindak_lanjut' => fake()->randomElement(['Aman', 'Perlu Pemantauan', 'Perlu Tindakan']),
        ];
    }
}
