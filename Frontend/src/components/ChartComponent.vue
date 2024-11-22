<template>
  <q-card class="q-pa-sm" flat style="border-radius: 15px">
    <p class="q-ma-none q-pa-sm text-base text-weight-regular">
      Today's Services
    </p>
    <q-card-section class="q-pa-none">
      <div class="chart-container">
        <canvas ref="chartRef"></canvas>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const chartRef = ref(null);

/**
const generateWavyData = (numPoints) => {
  const data = [];
  for (let i = 0; i < numPoints; i++) {
    data.push(Math.sin(i * 0.5) * 300 + 40);
  }
  return data;
};
 */

const chartData = {
  labels: ["April", "May", "June", "July", "August"],
  datasets: [
    {
      label: "Sales",
      borderColor: "teal",
      borderWidth: 1,
      backgroundColor: "rgba(0, 128, 128, 0.1)",
      data: [0, 130, 150, 200, 300, 400],
      fill: true,
      tension: 0.4,
      pointRadius: 0,
    },
  ],
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true },
  },
  scales: {
    y: {
      display: false,
      grid: {
        display: false,
      },
    },
    x: {
      display: true,
      grid: {
        display: false,
      },
    },
  },
};

onMounted(() => {
  if (chartRef.value) {
    const ctx = chartRef.value.getContext("2d");
    if (ctx) {
      new Chart(ctx, {
        type: "line",
        data: chartData,
        options: chartOptions,
      });
    }
  }
});
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 150px;
  position: relative;
}

canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}
</style>
