<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $ahora = now();
        $lote = [];

        for ($i = 1; $i <= 20000; $i++) {
            $lote[] = [
                'nombre'      => 'Producto ' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'descripcion' => 'Descripción del Producto ' . $i,
                'precio'      => rand(500, 15000) / 100,
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ];

            if ($i % 1000 === 0) {
                DB::table('productos')->insert($lote);
                $lote = [];
            }
        }
    }
}
