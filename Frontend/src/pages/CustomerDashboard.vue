<template>
  <q-page class="container q-pa-md">
    <q-card class="dashboard-card">
      <q-card-section>
        <h3 class="title">Welcome to the Customer Dashboard</h3>
        <p class="description">
          Here, you can manage your appointments and view services.
        </p>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup></script>

<style lang="scss" scoped>
.container {
  background-color: #249990;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 20px;
  position: fixed;
}

.dashboard-card {
  background-color: white;
  border-radius: 12px;
  padding: 16px;
  width: 90%;
  max-width: 400px;
  margin-top: 20px;
}

.title {
  color: black;
  font-family: Arial, sans-serif;
  font-weight: 600;
  font-size: 16px;
  text-align: center;
  margin-bottom: 5px;
}

.description {
  color: #555;
  font-family: Arial, sans-serif;
  font-weight: 300;
  font-size: 12px;
  text-align: left;
}
</style>
