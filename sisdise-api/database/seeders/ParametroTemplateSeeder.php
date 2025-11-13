<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importe o DB
use App\Models\GrupoParametro;
use App\Models\ItemParametro;

class ParametroTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Popula o banco com os 6 Grupos de Parâmetros e os 39 Itens de Avaliação
     * Baseado nas Tabelas 2 e 4 do Manual Técnico SisDISE.
     */
    public function run(): void
    {
        // Limpa as tabelas antes de popular para evitar duplicatas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ItemParametro::truncate();
        GrupoParametro::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        //--- 1. DIREITOS HUMANOS E TRABALHO ---

        // GRUPO 1.1: Proteção dos direitos humanos e trabalho [cite: 149]
        $g1_1 = GrupoParametro::create([
            'codigo' => 'Sdt1',
            'nome' => 'Proteção dos direitos humanos e trabalho',
            'peso' => 2.0,
            'nota_maxima_grupo' => 70, // 14 itens * 5 = 70 [cite: 149]
        ]);
        
        // Adiciona os 14 Itens do Grupo 1.1 [cite: 122]
        $g1_1->itemParametros()->createMany([
            ['codigo_item' => '1.1.1', 'descricao' => 'Condições de trabalho (seguras e saudáveis)'],
            ['codigo_item' => '1.1.2', 'descricao' => 'Discriminação no trabalho'],
            ['codigo_item' => '1.1.3', 'descricao' => 'Trabalho forçado, infantil ou análogo a escravidão'],
            ['codigo_item' => '1.1.4', 'descricao' => 'Preocupação com a saúde, habitação e educação dos trabalhadores'],
            ['codigo_item' => '1.1.5', 'descricao' => 'Inclusão/contratação de vítimas de violência'],
            ['codigo_item' => '1.1.6', 'descricao' => 'Respeito à adesão a sindicatos por trabalhadores'],
            ['codigo_item' => '1.1.7', 'descricao' => 'Respeito à diversidade religiosa, de gênero e racial'],
            ['codigo_item' => '1.1.8', 'descricao' => 'Preocupação com o deslocamento forçado de pessoas'],
            ['codigo_item' => '1.1.9', 'descricao' => 'Contribuição econômica com a comunidade local'],
            ['codigo_item' => '1.1.10', 'descricao' => 'Integração/debate com a comunidade local'],
            ['codigo_item' => '1.1.11', 'descricao' => 'Disponibilidade de serviço da empresa ao acesso às pessoas em condição de vulnerabilidade'],
            ['codigo_item' => '1.1.12', 'descricao' => 'Oportunidade para as mulheres da comunidade'],
            ['codigo_item' => '1.1.13', 'descricao' => 'Relação entre os serviços oferecidos pela empresa na melhoria das condições de vida das pessoas em vulnerabilidade'],
            ['codigo_item' => '1.1.14', 'descricao' => 'Politicas de redução do uso da força na segurança da empresa'],
        ]);

        // GRUPO 1.2: Abusos aos direitos humanos [cite: 149]
        $g1_2 = GrupoParametro::create([
            'codigo' => 'Sdt2',
            'nome' => 'Abusos aos direitos humanos',
            'peso' => 1.0,
            'nota_maxima_grupo' => 30, // 6 itens * 5 = 30 [cite: 149]
        ]);

        // Adiciona os 6 Itens do Grupo 1.2 [cite: 122]
        $g1_2->itemParametros()->createMany([
            ['codigo_item' => '1.2.1', 'descricao' => 'Priorização de negócios com empresas que adotam estratégias de mitigação de abusos aos Direitos Humanos.'],
            ['codigo_item' => '1.2.2', 'descricao' => 'Políticas de proteção dos Direitos Humanos de seus funcionários'],
            ['codigo_item' => '1.2.3', 'descricao' => 'Sistema de monitoramento da garantia aos Direitos Humanos'],
            ['codigo_item' => '1.2.4', 'descricao' => 'Empresa mantém diálogo/projeto de direitos humanos com grupos da sociedade'],
            ['codigo_item' => '1.2.5', 'descricao' => 'Possui políticas para evitar ações de segurança que descumpram os direitos humanos'],
            ['codigo_item' => '1.2.6', 'descricao' => 'Considera possíveis impactos sobre os direitos humanos no encerramento de relações comerciais'],
        ]);

        //--- 2. MEIO AMBIENTE ---

        // GRUPO 2.1: Ações preventivas aos desafios ambientais [cite: 149]
        $g2_1 = GrupoParametro::create([
            'codigo' => 'Sma1',
            'nome' => 'Ações preventivas aos desafios ambientais',
            'peso' => 2.0,
            'nota_maxima_grupo' => 20, // 4 itens * 5 = 20 [cite: 149]
        ]);

        // Adiciona os 4 Itens do Grupo 2.1 [cite: 122]
        $g2_1->itemParametros()->createMany([
            ['codigo_item' => '2.1.1', 'descricao' => 'Mantem códigos/práticas de produção com minimização de agressão à saúde ou ambiente'],
            ['codigo_item' => '2.1.2', 'descricao' => 'Possui comitê/sistema de supervisão dos riscos à saúde ou ambiente'],
            ['codigo_item' => '2.1.3', 'descricao' => 'Fornece apoio econômico a pesquisas ou ações sustentáveis'],
            ['codigo_item' => '2.1.4', 'descricao' => 'Participação colaborativa no compartilhamento de conhecimento e experiência sobre ações de sustentabilidade'],
        ]);

        // GRUPO 2.2: Iniciativas de responsabilidade ambiental [cite: 149]
        $g2_2 = GrupoParametro::create([
            'codigo' => 'Sma2',
            'nome' => 'Iniciativas de responsabilidade ambiental',
            'peso' => 2.0,
            'nota_maxima_grupo' => 25, // 5 itens * 5 = 25 [cite: 149]
        ]);

        // Adiciona os 5 Itens do Grupo 2.2 [cite: 122]
        $g2_2->itemParametros()->createMany([
            ['codigo_item' => '2.2.1', 'descricao' => 'Possui visão, políticas e estratégias da empresa, com inclusão do desenvolvimento sustentável'],
            ['codigo_item' => '2.2.2', 'descricao' => 'Desenvolve metas e indicadores de sustentabilidade (econômicos, ambientais, sociais)'],
            ['codigo_item' => '2.2.3', 'descricao' => 'Possui programa de produção e consumo sustentável'],
            ['codigo_item' => '2.2.4', 'descricao' => 'Trabalha com designers de produtos e fornecedores que melhoram o desempenho ambiental'],
            ['codigo_item' => '2.2.5', 'descricao' => 'Monitoramento do progresso da incorporação de princípios de sustentabilidade nas práticas de negócios'],
        ]);

        // GRUPO 2.3: Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas [cite: 149]
        $g2_3 = GrupoParametro::create([
            'codigo' => 'Sma3',
            'nome' => 'Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas',
            'peso' => 1.0,
            'nota_maxima_grupo' => 30, // 6 itens * 5 = 30 [cite: 149]
        ]);

        // Adiciona os 6 Itens do Grupo 2.3 [cite: 122]
        $g2_3->itemParametros()->createMany([
            ['codigo_item' => '2.3.1', 'descricao' => 'Mantem politica corporativa ou individual da empresa sobre o uso de tecnologias ambientalmente sustentáveis'],
            ['codigo_item' => '2.3.2', 'descricao' => 'Disponibiliza informações do desempenho ambiental e os benefícios do uso de tecnologias sustentáveis'],
            ['codigo_item' => '2.3.3', 'descricao' => 'Possui sistema de avaliação de ciclo de vida de novas tecnologias e produtos'],
            ['codigo_item' => '2.3.4', 'descricao' => 'Possui programa de avaliação de tecnologias ambientais'],
            ['codigo_item' => '2.3.5', 'descricao' => 'Estabelece critérios de investimento e política para fornecedores e contratados, garantindo critérios ambientais mínimos'],
            ['codigo_item' => '2.3.6', 'descricao' => 'Coopera com parceiros do setor para difundir a melhor tecnologia para outras organizações'],
        ]);


        //--- 3. ANTICORRUPÇÃO ---

        // GRUPO 3.1: Ações de combate a corrupção [cite: 149]
        $g3_1 = GrupoParametro::create([
            'codigo' => 'Sac1',
            'nome' => 'Ações de combate a corrupção',
            'peso' => 2.0,
            'nota_maxima_grupo' => 20, // 4 itens * 5 = 20 [cite: 149]
        ]);

        // Adiciona os 4 Itens do Grupo 3.1 [cite: 122]
        $g3_1->itemParametros()->createMany([
            ['codigo_item' => '3.1.1', 'descricao' => 'Possui políticas e programas anticorrupção em suas organizações e suas operações comerciais'],
            ['codigo_item' => '3.1.2', 'descricao' => 'Mantem monitoramento do progresso anticorrupção'],
            ['codigo_item' => '3.1.3', 'descricao' => 'Unir forças com empresas do setor e outras partes interessadas nos esforços anticorrupção'],
            ['codigo_item' => '3.1.4', 'descricao' => 'Assinatura do "Apelo à Ação Anticorrupção" (documento das empresas aos governos para combater a corrupção e promover uma governança eficaz para uma economia global sustentável e inclusiva)'],
        ]);
    }
}