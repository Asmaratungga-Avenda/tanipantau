<?php

namespace Database\Factories;

use App\Models\Petani;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Petani>
 */
class PetaniFactory extends Factory
{
    protected $model = Petani::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nik' => fake()->unique()->numerify('################'),
            'alamat' => fake()->streetAddress(),
            'provinsi' => 'Jawa Timur',
            'kabupaten' => fake()->randomElement(['Kabupaten Malang', 'Kabupaten Pasuruan', 'Kabupaten Probolinggo', 'Kabupaten Lumajang', 'Kabupaten Jember']),
            'kecamatan' => fake()->city(),
            'desa' => fake()->citySuffix(),
            'nomor_hp' => fake()->phoneNumber(),
            'status_aktif' => fake()->boolean(90),
        ];
    }
}
