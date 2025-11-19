<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Diagnostico;
use App\Models\GrupoParametro;
use Faker\Factory as Faker;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        $grupos = GrupoParametro::with('itemParametros')->get();
        if ($grupos->isEmpty()) {
            $this->command->error('ERRO: Execute o ParametroTemplateSeeder primeiro!');
            return;
        }

        $this->command->info('--- Criando Cenários de Demonstração ---');

        // =====================================================================
        // 1. CENÁRIO DE EXCELÊNCIA (Score ~1000)
        // =====================================================================
        $this->command->info('1. Criando Empresa de Excelência...');

        $avaliadorTop = User::firstOrCreate(['email' => 'excelencia@sisdise.com'], [
            'name' => 'Avaliador Otimista',
            'password' => Hash::make('senha123'),
            'tipo' => 'Avaliador'
        ]);

        $empresaTop = Empresa::create([
            'user_id' => $avaliadorTop->id,
            'razaoSocial' => 'EcoGreen Future S.A.',
            'cnpj' => '99.999.999/0001-99',
            'setor' => 'Energia Renovável',
            'cidade' => 'Curitiba',
            'estado' => 'PR'
        ]);

        $this->criarDiagnosticoCompleto($avaliadorTop, $empresaTop, $grupos, now(), 'Diagnóstico de Referência (Excelência)', 'high');


        // =====================================================================
        // 2. CENÁRIO CRÍTICO (Score ~100)
        // =====================================================================
        $this->command->info('2. Criando Empresa Crítica...');

        $avaliadorCritico = User::firstOrCreate(['email' => 'critico@sisdise.com'], [
            'name' => 'Avaliador Rigoroso',
            'password' => Hash::make('senha123'),
            'tipo' => 'Avaliador'
        ]);

        $empresaBad = Empresa::create([
            'user_id' => $avaliadorCritico->id,
            'razaoSocial' => 'Indústrias Fumaça Ltda',
            'cnpj' => '11.111.111/0001-11',
            'setor' => 'Mineração',
            'cidade' => 'Cubatão',
            'estado' => 'SP'
        ]);

        $this->criarDiagnosticoCompleto($avaliadorCritico, $empresaBad, $grupos, now(), 'Auditoria de Conformidade (Crítico)', 'low');


        // =====================================================================
        // 3. GESTORES LIVRES
        // =====================================================================
        $this->command->info('3. Criando Gestores Livres...');
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => $faker->firstName . ' ' . $faker->lastName,
                'email' => "gestor.livre{$i}@cliente.com",
                'password' => Hash::make('senha123'),
                'tipo' => 'Gestor Empresarial',
                'empresa_id' => null,
            ]);
        }


        // =====================================================================
        // 4. ESTUDO DE CASO REAL (TCC - Fernandópolis) - SCORE 532
        // =====================================================================
        $this->command->info('4. Criando Estudo de Caso Real (532 Pontos)...');

        $avaliadorTCC = User::firstOrCreate(['email' => 'tcc@sisdise.com'], [
            'name' => 'Pedro Vanzela (Avaliador)',
            'password' => Hash::make('senha123'),
            'tipo' => 'Avaliador'
        ]);

        $empresaReal = Empresa::create([
            'user_id' => $avaliadorTCC->id,
            'razaoSocial' => 'Transportadora Modelo de Fernandópolis',
            'cnpj' => '45.123.456/0001-78',
            'setor' => 'Logística e Transporte',
            'cidade' => 'Fernandópolis',
            'estado' => 'SP'
        ]);

        // ATENÇÃO: Estratégia 'exact_532'
        $this->criarDiagnosticoCompleto($avaliadorTCC, $empresaReal, $grupos, now()->subDays(2), 'Diagnóstico Oficial - Estudo de Caso', 'exact_532');


        $this->command->info('Concluído! Dados de demonstração criados.');
    }

    /**
     * Motor de criação de diagnóstico simulado
     */
    private function criarDiagnosticoCompleto($user, $empresa, $grupos, $data, $titulo, $strategy = 'random')
    {
        $diagnostico = Diagnostico::create([
            'user_id'       => $user->id,
            'empresa_id'    => $empresa->id,
            'titulo'        => $titulo,
            'dataAnalise'   => $data,
            'escoreFinal'   => 0,
            'classificacao' => 'Processando...',
        ]);

        $principioSdt = $diagnostico->principios()->create(['nomePrincipio' => 'Direitos humanos e trabalho', 'escoreObtido' => 0]);
        $principioSma = $diagnostico->principios()->create(['nomePrincipio' => 'Meio ambiente', 'escoreObtido' => 0]);
        $principioSac = $diagnostico->principios()->create(['nomePrincipio' => 'Anticorrupção', 'escoreObtido' => 0]);

        $mapPrincipioGrupo = [
            'Sdt1' => $principioSdt, 'Sdt2' => $principioSdt,
            'Sma1' => $principioSma, 'Sma2' => $principioSma, 'Sma3' => $principioSma,
            'Sac1' => $principioSac,
        ];

        // Matriz de Notas para dar EXATAMENTE 532 pontos
        // Ordem: Sdt1(14), Sdt2(6), Sma1(4), Sma2(5), Sma3(6), Sac1(4)
        $notas532 = [
            // G1 (Sdt1): Soma 42 -> Escore 120
            3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3,
            // G2 (Sdt2): Soma 9 -> Escore 30
            1, 2, 1, 2, 1, 2,
            // G3 (Sma1): Soma 18 -> Escore 180
            4, 5, 4, 5,
            // G4 (Sma2): Soma 14 -> Escore 112
            3, 2, 3, 3, 3,
            // G5 (Sma3): Soma 12 -> Escore 40
            2, 2, 2, 2, 2, 2,
            // G6 (Sac1): Soma 5 -> Escore 50
            1, 2, 1, 1
        ];
        $notaIndex = 0;

        foreach ($grupos as $grupo) {
            $pi = $grupo->peso;
            $nmax = $grupo->nota_maxima_grupo;
            $somaNotasGrupo_Eni = 0;

            foreach ($grupo->itemParametros as $item) {
                $notaItem = 0;

                if ($strategy === 'high') {
                    $notaItem = 5;
                } elseif ($strategy === 'low') {
                    $notaItem = 1;
                } elseif ($strategy === 'exact_532') {
                    // Usa a matriz fixa
                    $notaItem = $notas532[$notaIndex] ?? 0;
                    $notaIndex++;
                } else {
                    $notaItem = rand(2, 4);
                }

                $somaNotasGrupo_Eni += $notaItem;

                $diagnostico->itens()->create([
                    'item_parametro_id' => $item->id,
                    'nota' => $notaItem
                ]);
            }

            $escoreGrupo_si = 0;
            if ($nmax > 0) {
                $escoreGrupo_si = 100 * ($somaNotasGrupo_Eni / $nmax) * $pi;
            }

            $principioPai = $mapPrincipioGrupo[$grupo->codigo];
            $principioPai->parametroAvaliacaos()->create([
                'descricao'     => $grupo->nome,
                'nota'          => $somaNotasGrupo_Eni,
                'peso'          => $pi,
                'escore_obtido' => $escoreGrupo_si,
            ]);

            $principioPai->escoreObtido += $escoreGrupo_si;
        }

        $principioSdt->save();
        $principioSma->save();
        $principioSac->save();

        $escoreFinal_Sf = $principioSdt->escoreObtido + $principioSma->escoreObtido + $principioSac->escoreObtido;

        $classificacao = "Sustentável";
        if ($escoreFinal_Sf <= 250) { $classificacao = "Sustentabilidade inexistente"; }
        elseif ($escoreFinal_Sf <= 500) { $classificacao = "Baixa sustentabilidade"; }
        elseif ($escoreFinal_Sf <= 750) { $classificacao = "Sustentabilidade moderada"; }

        $diagnostico->update([
            'escoreFinal'   => $escoreFinal_Sf,
            'classificacao' => $classificacao,
        ]);
    }
}
