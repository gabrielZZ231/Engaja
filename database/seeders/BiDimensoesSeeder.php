<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BiDimensao;
use App\Models\BiDimensaoValor;

class BiDimensoesSeeder extends Seeder
{
    public function run()
    {
        $dimensoes = [
            'SEXO' => [
                'nome' => 'Sexo',
                'valores' => [
                    'MAS' => 'Masculino',
                    'FEM' => 'Feminino',
                ],
            ],
            'RACA' => [
                'nome' => 'Raça / Cor',
                'valores' => [
                    'BRANCA' => 'Branca',
                    'PRETA' => 'Preta',
                    'PARDA' => 'Parda',
                    'INDIGENA' => 'Indígena',
                ],
            ],
            'RESIDENCIA' => [
                'nome' => 'Residência',
                'valores' => [
                    'RURAL' => 'Rural',
                    'URBANA' => 'Urbana',
                    'FAVELA' => 'Favela',
                ],
            ],
        ];

        foreach ($dimensoes as $codigo => $data) {
            $dimensao = BiDimensao::firstOrCreate(
                ['codigo' => $codigo],
                ['nome' => $data['nome']]
            );

            foreach ($data['valores'] as $valCodigo => $valNome) {
                BiDimensaoValor::firstOrCreate(
                    [
                        'dimensao_id' => $dimensao->id,
                        'codigo' => $valCodigo,
                    ],
                    ['nome' => $valNome]
                );
            }
        }
    }
}

