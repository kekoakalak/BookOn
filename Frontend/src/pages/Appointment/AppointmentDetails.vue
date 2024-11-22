<template>
  <q-page class="q-pa-md">
    <!-- Back Button -->
    <q-btn
      flat
      icon="arrow_back"
      label="Back"
      @click="goBack"
      class="q-mb-md"
    />

    <!-- Loading Spinner -->
    <q-card v-if="loading">
      <q-card-section>
        <q-spinner color="primary" size="50px" />
        <p>Loading appointment details...</p>
      </q-card-section>
    </q-card>

    <!-- Error Message -->
    <q-card v-if="errorMessage">
      <q-card-section>
        <p class="text-negative">{{ errorMessage }}</p>
      </q-card-section>
    </q-card>


    <!-- Appointment and Service Details Display -->
    <q-card v-if="appointment && service && details" class="q-mb-md" flat>
      <q-card-section>
        <!-- Service Media Image -->
        <div class="appointment-img-container">
      <q-card class="appointment-img">
        <img
          v-if="service.media"
          :src="getImageSrc(service.media)"
          alt="Service Media"
          class="service-medias"
        />
        <div class="appointment-img-data">
          <h6 class="service-name">{{ service.name || 'Service Name' }}</h6>
         <!-- Location Section -->
         <div class="location">
            <strong class="clinic-name">
            Dentistree, Dental Clinic
            </strong>
              <div class="location-info">
                <q-icon name="place" class="address-icon" />
                <span>{{ service.location || "Legazpi City, Albay | 2km" }}</span>
            </div>
          </div>
        </div>
      </q-card>
    </div>

        <!-- Client Information -->
         <div class="client-card">
          <p><strong>Client Information</strong></p>
          <p><strong>{{ details.first_name }} {{ details.last_name }}</strong></p>

          <!-- Date, Time, Duration, Phone, and Payment Information -->
          <div class="appointment-info">
            <div class="info-row">
              <q-icon name="calendar_today" size="sm" class="icons"/><p class="label">Date:</p>
              <p class="value">{{ formattedDate.split(" ")[0] }}</p>
            </div>
            <div class="info-row">
              <q-icon name="access_time" size="sm" class="icons"/> <p class="label">Time:</p>
              <p class="value">{{ formattedDate.split(" ")[1] }}</p>
            </div>
            <div class="info-row">
              <q-icon name="schedule" size="sm" class="icons"/>  <p class="label">Duration:</p>
              <p class="value">{{ service.duration }}</p>
            </div>
            <div class="info-row">
              <q-icon name="phone" size="sm" class="icons"/> <p class="label">Phone:</p>
              <p class="value">{{ details.mobile }}</p>
            </div>
            <div class="info-row">
              <p class="label">Payment Method:</p>
              <p class="value">{{ details.payment_method }}</p>
            </div>
          </div>
       </div>

        <!-- Separator -->
        <q-separator inset />

        <!-- Notes Section -->
       <p class="notes-txt"> <q-icon name="sticky_note_2" size="sm" class="icons"/>Notes</p>
        <p class="notes-txt">{{ details.notes }}</p>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script src="./scripts/AppointmentDetails.js"></script>

<style scoped>
@import "./styles/AppointmentDetails.scss";
</style>
