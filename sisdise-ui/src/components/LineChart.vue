<template>
  <Line :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { defineProps } from 'vue';
import { Line } from 'vue-chartjs';

// Chart.js 3+ requer que registremos os componentes que vamos usar
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale
);

// Definimos que este componente espera receber 'chartData' e 'chartOptions'
// da página "pai" (o DashboardView)
defineProps({
  chartData: {
    type: Object,
    required: true,
  },
  chartOptions: {
    type: Object,
    default: () => ({
      responsive: true,
      maintainAspectRatio: false,
    }),
  },
});
</script>
