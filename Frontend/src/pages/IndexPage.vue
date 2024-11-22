<template>
  <q-page class="flex q-pa-sm" style="background-color: #ebebeb">
    <div class="overview-container q-pa-md">
      <div class="flex row justify-around q-gutter-md">
        <q-card
          v-for="(stat, index) in stats"
          :key="index"
          flat
          class="stat-card q-pa-sm q-pl-md flex flex-start column"
        >
          <div class="q-ma-none q-mt-md q-pa-none">
            <div class="flex row items-center q-mb-sm">
              <h4 class="q-ma-none text-weight-bolder">
                {{ stat.value }}
              </h4>
            </div>
            <p class="text-weight-medium q-mt-none">
              {{ stat.label }}
            </p>
          </div>
        </q-card>
      </div>
      <div class="chart">
        <h5 class="text-weight-medium q-mb-md q-mt-md">Overview</h5>
        <ChartComponent />
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import ChartComponent from "components/ChartComponent.vue";
import { api } from "../boot/axios";

defineOptions({
  name: "IndexPage",
});

const stats = ref([
  { icon: "thumbs_up_down", value: 0, label: "TOTAL APPOINTMENTS" },
  { icon: "person", value: 0, label: "APPOINT TODAY" },
  { icon: "person", value: 0, label: "TOTAL SERVICES" },
  { icon: "people", value: 0, label: "TOTAL RATINGS" },
]);

const userId = ref(sessionStorage.getItem("user_id"));

const fetchData = async () => {
  try {
    // Fetch total appointments count
    const appointmentRes = await api.get(
      `/appointments/count.php?provider_id=${userId.value}`
    );
    stats.value[0].value = appointmentRes.data.appointment_count || 0;

    // Fetch appointments count for today
    const todaysCount = await api.get(`/appointments/todaysCount.php`);
    stats.value[1].value = todaysCount.data.today_appointment_count || 0;

    // Fetch total services count
    const serviceRes = await api.get(`/services/count.php`);
    stats.value[2].value = serviceRes.data.service_count || 0;

    // Fetch total reviews count
    const reviewRes = await api.get(`/ratings/count.php`);
    stats.value[3].value = reviewRes.data.review_count || 0;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

onMounted(() => {
  fetchData();
});
</script>

<style>
.stat-card {
  width: 150px;
  height: 112px;
  border-radius: 15px;
}
</style>
