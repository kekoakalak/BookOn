<template>
  <q-card class="q-pa-sm" flat style="border-radius: 15px">
    <q-card-section>
      <div class="chart-container">
        <canvas ref="chartRef"></canvas>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const chartRef = ref(null);
const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
});

let chartInstance = null;

onMounted(() => {
  initializeChart();
});

watch(
  () => props.data,
  (newData) => {
    if (chartInstance) {
      updateChartData(newData);
    }
  },
  { immediate: true }
);

function initializeChart() {
  if (chartRef.value) {
    const ctx = chartRef.value.getContext("2d");

    chartInstance = new Chart(ctx, {
      type: "line", // Adjust chart type as needed
      data: {
        labels: props.data.map(item => item.name),
        datasets: [
          {
            label: "Appointments",
            data: props.data.map(item => item.value),
            borderColor: "teal",
            backgroundColor: "rgba(0, 128, 128, 0.1)",
            borderWidth: 1,
            fill: true,
            tension: 0.4,
            pointRadius: 0,

          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true },
        },
      },
    });
  }
}

function updateChartData(newData) {
  if (chartInstance) {
    chartInstance.data.labels = newData.map(item => item.name);
    chartInstance.data.datasets[0].data = newData.map(item => item.value);
    chartInstance.update();
  }
}
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 150px;
  position: relative;
}
</style>
