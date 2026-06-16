<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Certifica-se que a coluna existe antes de migrar os dados
        if (! Schema::hasColumn('avaliacao_atividades', 'questao_unificada')) {
            Schema::table('avaliacao_atividades', function (Blueprint $table) {
                $table->text('questao_unificada')->nullable()->after('avaliacao_atuacao_equipe');
            });
        }

        $registros = DB::table('avaliacao_atividades')->get();

        foreach ($registros as $reg) {
            $partes = [];

            if (filled($reg->avaliacao_logistica)) {
                $partes[] = 'Avaliação da Logística: '.$reg->avaliacao_logistica;
            }
            if (filled($reg->avaliacao_acolhimento_sme)) {
                $partes[] = 'Avaliação do acolhimento e apoio da SME: '.$reg->avaliacao_acolhimento_sme;
            }
            if (filled($reg->avaliacao_atuacao_equipe)) {
                $partes[] = 'Atuação da Equipe do IPF: '.$reg->avaliacao_atuacao_equipe;
            }
            if (filled($reg->avaliacao_planejamento)) {
                $partes[] = 'Desenvolvimento da Ação (Planejamento): '.$reg->avaliacao_planejamento;
            }
            if (filled($reg->avaliacao_recursos_materiais)) {
                $partes[] = 'Recursos Materiais: '.$reg->avaliacao_recursos_materiais;
            }
            if (filled($reg->avaliacao_links_presenca)) {
                $partes[] = 'Links e QR codes: '.$reg->avaliacao_links_presenca;
            }
            if (filled($reg->avaliacao_destaques)) {
                $partes[] = 'Destaques: '.$reg->avaliacao_destaques;
            }

            if (! empty($partes)) {
                $unificado = implode(' ; ', $partes);

                DB::table('avaliacao_atividades')
                    ->where('id', $reg->id)
                    ->update(['questao_unificada' => $unificado]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se desejar limpar o campo ao reverter, mas geralmente mantemos o dado se a coluna for removida por outra migração
        DB::table('avaliacao_atividades')->update(['questao_unificada' => null]);
    }
};
