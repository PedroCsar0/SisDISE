<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GrupoParametro;
use App\Models\ItemParametro;

class ParametroTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ItemParametro::truncate();
        GrupoParametro::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // --- 1. DIREITOS HUMANOS E TRABALHO ---

        $g1_1 = GrupoParametro::create(['codigo' => 'Sdt1', 'nome' => 'Proteção dos direitos humanos e trabalho', 'peso' => 2.0, 'nota_maxima_grupo' => 70]);
        $g1_1->itemParametros()->createMany([
            ['codigo_item' => '1.1.1', 'descricao' => 'Condições de trabalho (seguras e saudáveis)',
             'recomendacao' => 'Implementar Programas de Saúde Ocupacional (PCMSO) e Prevenção de Riscos (PGR). Fornecer EPIs adequados e realizar manutenções preventivas no ambiente de trabalho.'],
            ['codigo_item' => '1.1.2', 'descricao' => 'Discriminação no trabalho',
             'recomendacao' => 'Criar um Código de Ética e Conduta claro. Instituir processos de recrutamento às cegas e canais de denúncia anônimos para casos de discriminação.'],
            ['codigo_item' => '1.1.3', 'descricao' => 'Trabalho forçado, infantil ou análogo à escravidão',
             'recomendacao' => 'Realizar auditorias periódicas em toda a cadeia de fornecedores. Exigir documentação regular de todos os contratados e terceirizados.'],
            ['codigo_item' => '1.1.4', 'descricao' => 'Preocupação com a saúde, habitação e educação dos trabalhadores',
             'recomendacao' => 'Oferecer planos de saúde, auxílio-educação ou parcerias com instituições de ensino. Melhorar a ergonomia e as áreas de descanso.'],
            ['codigo_item' => '1.1.5', 'descricao' => 'Inclusão/contratação de vítimas de violência',
             'recomendacao' => 'Desenvolver políticas de RH focadas na reintegração social e parcerias com ONGs locais para empregabilidade de grupos vulneráveis.'],
            ['codigo_item' => '1.1.6', 'descricao' => 'Respeito à adesão a sindicatos por trabalhadores',
             'recomendacao' => 'Garantir a liberdade de associação sindical sem represálias. Manter diálogo aberto e transparente com representantes sindicais.'],
            ['codigo_item' => '1.1.7', 'descricao' => 'Respeito à diversidade religiosa, de gênero e racial',
             'recomendacao' => 'Promover workshops de conscientização sobre diversidade. Criar comitês de diversidade para rever políticas internas.'],
            ['codigo_item' => '1.1.8', 'descricao' => 'Preocupação com o deslocamento forçado de pessoas',
             'recomendacao' => 'Avaliar o impacto social de novos empreendimentos antes da instalação. Garantir compensação justa em casos inevitáveis de uso do solo.'],
            ['codigo_item' => '1.1.9', 'descricao' => 'Contribuição econômica com a comunidade local',
             'recomendacao' => 'Priorizar a contratação de mão de obra e fornecedores locais para fomentar a economia da região onde a empresa opera.'],
            ['codigo_item' => '1.1.10', 'descricao' => 'Integração/debate com a comunidade local',
             'recomendacao' => 'Realizar reuniões periódicas com a comunidade para ouvir demandas e apresentar relatórios de impacto social.'],
            ['codigo_item' => '1.1.11', 'descricao' => 'Disponibilidade de serviço da empresa ao acesso às pessoas em condição de vulnerabilidade',
             'recomendacao' => 'Criar programas de tarifa social ou doar parte da produção/serviços para instituições que atendem vulneráveis.'],
            ['codigo_item' => '1.1.12', 'descricao' => 'Oportunidade para as mulheres da comunidade',
             'recomendacao' => 'Estabelecer metas para aumentar a liderança feminina. Apoiar projetos locais de empreendedorismo feminino.'],
            ['codigo_item' => '1.1.13', 'descricao' => 'Relação entre os serviços oferecidos pela empresa na melhoria das condições de vida das pessoas em vulnerabilidade',
             'recomendacao' => 'Alinhar o core business da empresa aos ODS da ONU, desenvolvendo produtos que resolvam problemas sociais reais.'],
            ['codigo_item' => '1.1.14', 'descricao' => 'Políticas de redução do uso da força na segurança da empresa',
             'recomendacao' => 'Treinar equipes de segurança em direitos humanos e gestão de conflitos não-violenta. Rever protocolos de vigilância.'],
        ]);

        $g1_2 = GrupoParametro::create(['codigo' => 'Sdt2', 'nome' => 'Abusos aos direitos humanos', 'peso' => 1.0, 'nota_maxima_grupo' => 30]);
        $g1_2->itemParametros()->createMany([
            ['codigo_item' => '1.2.1', 'descricao' => 'Priorização de negócios com empresas que adotam estratégias de mitigação de abusos aos Direitos Humanos',
             'recomendacao' => 'Estabelecer critérios de "homologação de fornecedores" que exijam certificações ou compromissos formais com Direitos Humanos.'],
            ['codigo_item' => '1.2.2', 'descricao' => 'Políticas de proteção dos Direitos Humanos de seus funcionários',
             'recomendacao' => 'Formalizar e divulgar uma Política de Direitos Humanos interna. Incluir cláusulas de proteção nos contratos de trabalho.'],
            ['codigo_item' => '1.2.3', 'descricao' => 'Sistema de monitoramento da garantia aos Direitos Humanos',
             'recomendacao' => 'Criar indicadores de desempenho (KPIs) sociais e realizar auditorias internas anuais focadas em direitos humanos.'],
            ['codigo_item' => '1.2.4', 'descricao' => 'Empresa mantém diálogo/projeto de direitos humanos com grupos da sociedade',
             'recomendacao' => 'Apoiar ou financiar projetos sociais externos que defendam direitos fundamentais na comunidade.'],
            ['codigo_item' => '1.2.5', 'descricao' => 'Possui políticas para evitar ações de segurança que descumpram os direitos humanos',
             'recomendacao' => 'Revisar contratos com empresas de segurança terceirizada para garantir alinhamento com princípios humanitários.'],
            ['codigo_item' => '1.2.6', 'descricao' => 'Considera possíveis impactos sobre os direitos humanos no encerramento de relações comerciais',
             'recomendacao' => 'Realizar análise de impacto antes de encerrar contratos grandes que possam afetar a subsistência de comunidades dependentes.'],
        ]);

        //--- 2. MEIO AMBIENTE ---

        $g2_1 = GrupoParametro::create(['codigo' => 'Sma1', 'nome' => 'Ações preventivas aos desafios ambientais', 'peso' => 2.0, 'nota_maxima_grupo' => 20]);
        $g2_1->itemParametros()->createMany([
            ['codigo_item' => '2.1.1', 'descricao' => 'Mantém códigos/práticas de produção com minimização de agressão à saúde ou ambiente',
             'recomendacao' => 'Adotar práticas de Produção Mais Limpa (P+L). Substituir insumos tóxicos por alternativas biodegradáveis ou menos nocivas.'],
            ['codigo_item' => '2.1.2', 'descricao' => 'Possui comitê/sistema de supervisão dos riscos à saúde ou ambiente',
             'recomendacao' => 'Instituir um comitê de sustentabilidade ou CIPA ativa com foco ambiental. Realizar avaliações de risco ambiental regulares.'],
            ['codigo_item' => '2.1.3', 'descricao' => 'Fornece apoio econômico a pesquisas ou ações sustentáveis',
             'recomendacao' => 'Destinar uma porcentagem do orçamento para P&D verde ou patrocinar projetos ambientais locais.'],
            ['codigo_item' => '2.1.4', 'descricao' => 'Participação colaborativa no compartilhamento de conhecimento e experiência sobre ações de sustentabilidade',
             'recomendacao' => 'Participar em fóruns setoriais, associações comerciais ou grupos de trabalho sobre ESG para troca de boas práticas.'],
        ]);

        $g2_2 = GrupoParametro::create(['codigo' => 'Sma2', 'nome' => 'Iniciativas de responsabilidade ambiental', 'peso' => 2.0, 'nota_maxima_grupo' => 25]);
        $g2_2->itemParametros()->createMany([
            ['codigo_item' => '2.2.1', 'descricao' => 'Possui visão, políticas e estratégias da empresa, com inclusão do desenvolvimento sustentável',
             'recomendacao' => 'Revisar a Missão, Visão e Valores para incluir explicitamente a sustentabilidade. Criar um manifesto de sustentabilidade.'],
            ['codigo_item' => '2.2.2', 'descricao' => 'Desenvolve metas e indicadores de sustentabilidade (econômicos, ambientais, sociais)',
             'recomendacao' => 'Definir metas SMART para redução de água, energia e resíduos. Monitorar mensalmente e publicar os resultados.'],
            ['codigo_item' => '2.2.3', 'descricao' => 'Possui programa de produção e consumo sustentável',
             'recomendacao' => 'Implementar a análise de ciclo de vida dos produtos. Otimizar embalagens e reduzir o desperdício no processo produtivo.'],
            ['codigo_item' => '2.2.4', 'descricao' => 'Trabalha com designers de produtos e fornecedores que melhoram o desempenho ambiental',
             'recomendacao' => 'Incorporar o Ecodesign no desenvolvimento de novos produtos. Priorizar fornecedores com certificação ISO 14001.'],
            ['codigo_item' => '2.2.5', 'descricao' => 'Monitoramento do progresso da incorporação de princípios de sustentabilidade nas práticas de negócios',
             'recomendacao' => 'Publicar um Relatório de Sustentabilidade anual (padrão GRI ou simplificado) para transparência com stakeholders.'],
        ]);

        $g2_3 = GrupoParametro::create(['codigo' => 'Sma3', 'nome' => 'Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas', 'peso' => 1.0, 'nota_maxima_grupo' => 30]);
        $g2_3->itemParametros()->createMany([
            ['codigo_item' => '2.3.1', 'descricao' => 'Mantém política corporativa ou individual da empresa sobre o uso de tecnologias ambientalmente sustentáveis',
             'recomendacao' => 'Criar política de TI Verde e digitalização de processos para reduzir uso de papel e recursos físicos.'],
            ['codigo_item' => '2.3.2', 'descricao' => 'Disponibiliza informações do desempenho ambiental e os benefícios do uso de tecnologias sustentáveis',
             'recomendacao' => 'Educar os consumidores sobre os atributos ambientais dos produtos. Usar rótulos ecológicos claros.'],
            ['codigo_item' => '2.3.3', 'descricao' => 'Possui sistema de avaliação de ciclo de vida de novas tecnologias e produtos',
             'recomendacao' => 'Realizar estudos de ACV (Avaliação do Ciclo de Vida) para identificar os pontos de maior impacto ambiental.'],
            ['codigo_item' => '2.3.4', 'descricao' => 'Possui programa de avaliação de tecnologias ambientais',
             'recomendacao' => 'Investir em modernização do parque tecnológico para equipamentos com maior eficiência energética (Selo Procel A).'],
            ['codigo_item' => '2.3.5', 'descricao' => 'Estabelece critérios de investimento e política para fornecedores e contratados, garantindo critérios ambientais mínimos',
             'recomendacao' => 'Inserir cláusulas de conformidade ambiental em todos os contratos de compra e prestação de serviços.'],
            ['codigo_item' => '2.3.6', 'descricao' => 'Coopera com parceiros do setor para difundir a melhor tecnologia para outras organizações',
             'recomendacao' => 'Estabelecer parcerias de "simbiose industrial" onde os resíduos de uma empresa viram insumo para outra.'],
        ]);

        //--- 3. ANTICORRUPÇÃO ---

        $g3_1 = GrupoParametro::create(['codigo' => 'Sac1', 'nome' => 'Ações de combate a corrupção', 'peso' => 2.0, 'nota_maxima_grupo' => 20]);
        $g3_1->itemParametros()->createMany([
            ['codigo_item' => '3.1.1', 'descricao' => 'Possui políticas e programas anticorrupção em suas organizações e suas operações comerciais',
             'recomendacao' => 'Implementar um Programa de Compliance e Integridade. Treinar todos os funcionários sobre a lei anticorrupção.'],
            ['codigo_item' => '3.1.2', 'descricao' => 'Mantém monitoramento do progresso anticorrupção',
             'recomendacao' => 'Realizar due diligence (diligência prévia) de integridade em parceiros de negócios e grandes clientes.'],
            ['codigo_item' => '3.1.3', 'descricao' => 'Unir forças com empresas do setor e outras partes interessadas nos esforços anticorrupção',
             'recomendacao' => 'Aderir a pactos setoriais de integridade ou associar-se a institutos de ética empresarial.'],
            ['codigo_item' => '3.1.4', 'descricao' => 'Assinatura do "Apelo à Ação Anticorrupção"',
             'recomendacao' => 'Tornar-se signatário formal do Pacto Global da ONU e comprometer-se publicamente com a transparência.'],
        ]);
    }
}
