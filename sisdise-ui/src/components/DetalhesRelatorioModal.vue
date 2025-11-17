<template>
  <div
    @click.self="$emit('close')"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
  >
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <header class="flex justify-between items-center p-4 border-b">
        <h2 class="text-xl font-bold text-gray-900">Visão Detalhada por Parâmetro</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">&times;</button>
      </header>

      <main class="p-6 space-y-4">
        <p class="text-sm text-gray-600">
          Esta é a análise detalhada do escore, dividida pelos 6 parâmetros de sustentabilidade
          que compõem o cálculo final do diagnóstico.
        </p>

        <div class="border rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parâmetro de Sustentabilidade</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pontos (Obtidos / Máx.)</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Escore Ponderado (si)</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="param in parametrosDetalhados" :key="param.id" class="hover:bg-gray-50">

                <td class="px-4 py-3">
                  <p class="font-medium text-gray-900">{{ param.codigo }}</p>
                  <p class="text-sm text-gray-600">{{ param.descricao }}</p>
                </td>

                <td class="px-4 py-3 text-sm text-gray-600">{{ param.nota }} / {{ param.nmax }}</td>

                <td class="px-4 py-3 text-right font-medium text-gray-900">
                  {{ param.escore_obtido.toFixed(0) }}
                </td>

              </tr>
            </tbody>
          </table>
        </div>
      </main>

      <footer class="p-4 bg-gray-50 border-t">
        <button @click="$emit('close')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
          Fechar
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  diagnostico: {
    type: Object,
    required: true
  }
});

// Mapeamento de Nomes e Nmax (Baseado no Manual/PDF)
const metadados = {
  'Proteção dos direitos humanos e trabalho': { codigo: 'Proteção (Trabalho)', nmax: 70 },
  'Abusos aos direitos humanos': { codigo: 'Abusos (Trabalho)', nmax: 30 },
  'Ações preventivas aos desafios ambientais': { codigo: 'Ações Preventivas (Amb.)', nmax: 20 },
  'Iniciativas de responsabilidade ambiental': { codigo: 'Iniciativas (Amb.)', nmax: 25 },
  'Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas': { codigo: 'Tecnologias (Amb.)', nmax: 30 },
  'Ações de combate a corrupção': { codigo: 'Anticorrupção', nmax: 20 },
};

// Esta lógica está 100% correta
const parametrosDetalhados = computed(() => {
  if (!props.diagnostico || !props.diagnostico.principios) return [];

  // O backend (DiagnosticoController) guarda os 6 grupos calculados
  // dentro de 'principios.parametro_avaliacaos'
  return props.diagnostico.principios.flatMap(principio =>
    principio.parametro_avaliacaos.map(param => {
      const meta = metadados[param.descricao] || { codigo: 'N/A', nmax: 0 };
      return {
        ...param, // param.nota, param.escore_obtido (vindos do backend)
        codigo: meta.codigo,
        nmax: meta.nmax
      };
    })
  );
});
</script>
