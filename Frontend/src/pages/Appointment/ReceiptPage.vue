<template>
    <q-page class="q-pa-md">
      <q-card v-if="!showConfirmation">
        <q-card-section>
          <h4>Payment Receipt</h4>
          <p><strong>Amount Paid:</strong> {{ price }}</p>
          <p><strong>Date:</strong> {{ new Date().toLocaleDateString() }}</p>
          <p><strong>Time:</strong> {{ new Date().toLocaleTimeString() }}</p>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="primary" label="Download Receipt" @click="downloadReceipt" />
        </q-card-actions>
      </q-card>
  
      <!-- Confirmation Message after Download -->
      <q-card v-if="showConfirmation" class="q-mt-md">
        <q-card-section>
          <p><strong>Congratulations!!!</strong> Your service request has been received and is currently being processed.</p>
        </q-card-section>
        <q-card-actions align="center">
          <q-btn color="primary" label="Continue" @click="goToServiceManagement" />
        </q-card-actions>
      </q-card>
    </q-page>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  const price = ref("200"); // Example amount, pass actual amount dynamically
  const showConfirmation = ref(false);
  
  const downloadReceipt = () => {
    const receiptContent = `Payment Receipt\n\nAmount Paid: ${price.value}\nDate: ${new Date().toLocaleDateString()}\nTime: ${new Date().toLocaleTimeString()}`;
    const blob = new Blob([receiptContent], { type: "text/plain" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Receipt.txt";
    link.click();
  
    // Show confirmation message after download
    showConfirmation.value = true;
  };
  
  const goToServiceManagement = () => {
    router.push({ name: 'Services' }); 
  };
  </script>
  