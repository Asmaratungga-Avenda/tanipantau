<?php

namespace Database\Factories;

use App\Models\Lahan;
use App\Models\Petani;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lahan>
 */
class LahanFactory extends Factory
{
    protected $model = Lahan::class;

    public function definition(): array
    {
        return [
            'petani_id' => Petani::factory(),
            'nama_lahan' => 'Lahan ' . fake()->word(),
            'komoditas' => fake()->randomElement(['Padi', 'Jagung', 'Kacang Tanah', 'Singkong']),
            'luas_lahan' => fake()->randomFloat(2, 0.5, 5),
            'garis_lintang' => fake()->latitude(-8, -6),
            'garis_bujur' => fake()->longitude(105, 114),
            'tanggal_tanam' => fake()->dateTimeBetween('-3 months', 'now'),
            'fase_lahan' => fake()->randomElement(['Persiapan', 'Penanaman', 'Pertumbuhan', 'Panen', 'Panen Selesai']),
            'provinsi' => 'Jawa Timur',
            'kabupaten' => fake()->randomElement(['Kabupaten Malang', 'Kabupaten Pasuruan', 'Kabupaten Probolinggo', 'Kabupaten Lumajang', 'Kabupaten Jember']),
            'kecamatan' => fake()->city(),
            'desa' => fake()->citySuffix(),
            'status_aktif' => fake()->boolean(95),
        ];
    }
}
