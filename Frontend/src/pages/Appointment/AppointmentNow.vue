<template>
  <q-page class="page-border" >
    <q-card flat>
      <q-card-section class="appointment-form" >
        <q-form >

          <!-- Combined Date and Time Picker -->
           <div>
            <div class="label-text"><strong>Date and Time:</strong></div>
            <q-input v-model="dateTime" readonly required  outlined dense  class="input-field" >
            <template v-slot:append>
              <q-icon name="event" @click="showDatePicker = true" />
              <q-icon name="access_time" @click="showTimePicker = true" />
            </template>
          </q-input>
           </div>


          <!-- Date Picker Modal -->
          <q-dialog v-model="showDatePicker" persistent class="calendar-picker">
            <q-card class="rounded-card">
              <q-card-section>
                <div class="text-h6 text-center">Select Date</div>
              </q-card-section>
              <q-date v-model="date" mask="YYYY-MM-DD" minimal />
              <q-card-actions align="right">
                <q-btn flat label="Save"  @click="applyDate"  class="btn-date"/>
              </q-card-actions>
            </q-card>
          </q-dialog>

                  <!-- Time Picker Modal -->
          <q-dialog v-model="showTimePicker" persistent class="time-dia">
            <q-card>
              <q-card-section>
                <div class="text-h6 text-center">Select Hours</div>

                <!-- Display selected time in bold -->
                <div v-if="selectedTime" class="text-h6 text-left q-my-md">{{ selectedTime }}</div>

                <!-- Separator line -->
                <q-separator class="q-my-md" />

                <!-- List of times -->
                <q-list style="max-height: 432px; overflow-y: auto;">
                  <q-item
                    v-for="hour in availableHours"
                    :key="hour"
                    clickable
                    @click="selectTime(hour)"
                    :active="hour === selectedTime"
                    class="time-item"
                  >
                    <q-item-section>
                      <div class="text-left">{{ hour }}</div>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card-section>

              <!-- Actions -->
              <q-card-actions align="right">
                <q-btn flat label="Apply" color="positive" @click="applyTime" />
              </q-card-actions>
            </q-card>
          </q-dialog>
          <br>


          <div>
            <div class="label-text"><strong>First Name<span class="text-red"> * </span></strong></div>
          <q-input
          v-model="firstName"
          required
          outlined
          dense
          class="input-field" />
          <br>
          </div>


          <div>
            <div class="label-text"><strong>Last Name <span class="text-red"> * </span> </strong></div>
          <q-input v-model="lastName" required  outlined dense  class="input-field"/>
          <br>

          </div>


          <div>
            <div class="label-text"><strong>Phone Number<span class="text-red"> * </span></strong></div>
          <q-input v-model="mobile"  required outlined dense  class="input-field" mask="###########"/>
          <br>

          </div>

          <!-- Notes Section -->
          <div>
            <div class="label-text"><strong>Notes<span class="text-red"> * </span></strong></div>
          <q-input v-model="notes"  type="textarea" outlined dense  class="input-field"/>
          </div>

          <!-- Price Display -->
          <div class="q-mt-md">
            <p><strong>Price:</strong> {{ price }}</p>
          </div>

          <!-- Confirmation Button -->
          <q-btn type="button" label="Proceed to Confirmation" class="btn-proceed" @click="confirmAppointment" />

          <!-- Confirmation Section with Payment Selection -->
          <div v-if="showConfirmation" class="q-mt-md">
            <q-card>
              <q-card-section>
                <div>
                  <h6>Appointment Confirmation</h6>
                  <p><strong>Date:</strong> {{ date }}</p>
                  <p><strong>Time:</strong> {{ time }}</p>
                  <p><strong>Service ID:</strong> {{ serviceId }}</p>
                  <p><strong>Price:</strong> {{ price }}</p>

                  <!-- Payment Method Selection -->
                  <q-option-group
                    v-model="selectedPaymentMethod"
                    :options="paymentMethods"
                    label="Select Payment Method"
                    type="radio"
                    required
                  />
                </div>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn flat label="Cancel" color="negative" @click="showConfirmation = false" />
                <q-btn type="button" flat label="Proceed to Payment" color="positive" @click="proceedToPayment" />
              </q-card-actions>
            </q-card>
          </div>

          <!-- Payment Confirmation Dialog -->
          <q-dialog v-model="showPaymentDialog" persistent>
            <q-card>
              <q-card-section>
                <p><strong>Payment Confirmation</strong></p>
                <p>You will be redirected outside the app to complete your payment.</p>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn flat label="Cancel" color="negative" @click="showPaymentDialog = false" />
                <q-btn flat label="Confirm" color="positive" @click="redirectToReceipt" />
              </q-card-actions>
            </q-card>
          </q-dialog>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>
<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api } from '../../boot/axios';

const dateTime = ref(""); // Combined date-time
const firstName = ref("");  // First name field
const lastName = ref("");   // Last name field
const mobile = ref("");     // Mobile number
const notes = ref(""); // Notes field
const showDatePicker = ref(false);
const showTimePicker = ref(false);
const showConfirmation = ref(false);
const showPaymentDialog = ref(false);
const price = ref("200");
const serviceId = ref("");  // Service ID
const selectedPaymentMethod = ref(null);
const paymentMethods = [
  { label: "Visa", value: "Visa" },
  { label: "PayPal", value: "PayPal" },
  { label: "GCash", value: "GCash" },
];
const date = ref("");  // Date model
const time = ref("");  // Time model
const selectedTime = ref(""); // Selected time for list-based time picker

const route = useRoute();
const router = useRouter();
const userId = sessionStorage.getItem("user_id"); // Retrieve user ID
const providerId = "someProviderId"; // You can adjust this as necessary

// List of available times
const availableHours = [
  "08:00 AM", "08:30 AM", "09:00 AM", "09:30 AM",
  "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM",
  "12:00 PM", "12:30 PM", "01:00 PM", "01:30 PM",
  "02:00 PM", "02:30 PM", "03:00 PM", "03:30 PM",
  "04:00 PM", "04:30 PM", "05:00 PM", "05:30 PM",
  "06:00 PM", "06:30 PM", "07:00 PM", "07:30 PM"
];

// Handle selecting a time from the list
const selectTime = (hour) => {
  selectedTime.value = hour;
};

// Apply the selected time
const applyTime = () => {
  time.value = selectedTime.value; // Set the chosen time
  dateTime.value = `${date.value} ${time.value}`; // Update combined date-time
  showTimePicker.value = false;
};

// Apply the selected date
const applyDate = () => {
  showDatePicker.value = false;
  dateTime.value = `${date.value} ${time.value}`;
};

// Confirm appointment details before proceeding
const confirmAppointment = () => {
  if (dateTime.value && firstName.value && lastName.value && mobile.value) {
    serviceId.value = route.params.serviceId; // Ensure service ID is passed correctly
    showConfirmation.value = true;
  } else {
    alert("Please fill out all required details.");
  }
};

// Proceed to payment and send appointment data
const proceedToPayment = async () => {
  if (!selectedPaymentMethod.value) {
    alert("Please select a payment method");
    return;
  }
  const appointmentData = {
    appointment_date: `${date.value} ${time.value}`, // Change key to match PHP file
    service_id: serviceId.value,
    user_id: userId,
    provider_id: providerId,
    first_name: firstName.value,
    last_name: lastName.value,
    mobile: mobile.value,
    notes: notes.value,
    payment_method: selectedPaymentMethod.value
  };

  try {
    const response = await api.post('appointments/create.php', appointmentData);
    console.log(response.data); // Success response from backend
    showPaymentDialog.value = true;
  } catch (error) {
    console.error("Error creating appointment:", error);
    alert("There was an error creating your appointment. Please try again.");
  }
};

// Redirect to the receipt page after payment
const redirectToReceipt = () => {
  showPaymentDialog.value = false;
  router.push({ name: "ReceiptPage", params: { price: price.value } });
};
</script>

<style scoped>
@import "./styles/AppointmentNow.scss";
</style>
