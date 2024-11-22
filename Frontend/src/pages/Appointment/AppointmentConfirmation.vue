<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <h2>Appointment Confirmed!</h2>
        <p>Your appointment has been successfully booked.</p>
        <q-btn @click="goToHome" color="primary" label="Go to Appointments" />
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const goToHome = () => {
  router.push({ name: 'MyAppointments' });
};
</script>
