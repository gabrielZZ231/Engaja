<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BiIndicador;

class BiIndicadoresSeeder extends Seeder
{
    public function run()
    {
        BiIndicador::firstOrCreate(
            ['codigo' => 'TAXA_ANALFABETISMO'],
            [
                'nome' => 'Taxa de analfabetismo',
                'unidade' => '%',
                'fonte' => 'Censo Escolar / IBGE',
                'descricao' => 'Percentual da população não alfabetizada (15+)',
            ]
        );
    }
}
