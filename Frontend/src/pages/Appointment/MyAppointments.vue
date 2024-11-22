<template>
  <q-page class="q-pa-md" style="background-color: #ebebeb">
    <div class="row items-center justify-end q-mb-md">
      <div class="row items-center">
        <q-btn flat icon="filter_list" @click="showFilter = true" />
      </div>
    </div>


    <q-dialog v-model="showFilter" persistent position="bottom">
      <q-card class="rounded-card" style="min-width: 350px; max-width: 450px">
        <q-card-section>
          <div class="text-h6">Filters</div>
        </q-card-section>

        <q-card-section>
          <q-btn-group class="btn-group" flat>
            <q-btn
              class="date-range"
              label="All Time"
              flat
              @click="applyPreset('allTime')"
            />
            <q-btn
              class="date-range"
              label="Today"
              flat
              @click="applyPreset('today')"
            />
            <q-btn
              class="date-range"
              label="Yesterday"
              flat
              @click="applyPreset('yesterday')"
            />
          </q-btn-group>

          <q-btn-group class="btn-group" flat>
            <q-btn
              class="date-range"
              label="Past 7 Days"
              flat
              @click="applyPreset('past7')"
            />
            <q-btn
              class="date-range"
              label="Past 30 Days"
              flat
              @click="applyPreset('past30')"
            />
          </q-btn-group>
        </q-card-section>

        <div class="separator-or flex items-center">
          <q-separator class="separator-line" />
          <span class="or-text">or</span>
          <q-separator class="separator-line" />
        </div>

        <q-card-section>
          <div class="date-text">
            <div class="text-h6">Date Range</div>
          </div>
          <br />
          <div class="row from-to q-col-gutter-md">
            <div class="q-col-6">
              <label class="q-mb-xs">From</label>
              <q-input
                class="from-to-input"
                v-model="filter.startDate"
                label="Start Date"
                readonly
              >
                <template v-slot:append>
                  <q-icon name="event" @click="openStartDatePicker" />
                </template>
              </q-input>
            </div>
            <div class="q-col-6">
              <label class="q-mb-xs">To</label>
              <q-input
                class="from-to-input"
                filled
                v-model="filter.endDate"
                label="End Date"
                readonly
              >
                <template v-slot:append>
                  <q-icon name="event" @click="openEndDatePicker" />
                </template>
              </q-input>
            </div>
          </div>
        </q-card-section>

        <q-card-actions>
          <q-btn class="btn-apply" label="Apply" @click="applyFilter" />
        </q-card-actions>
      </q-card>
    </q-dialog>


    <q-dialog v-model="showStartDatePicker" persistent class="calendar-picker">
      <q-card class="rounded-card">
        <q-card-section>
          <div class="text-h6 text-center">Select Date</div>
        </q-card-section>
        <q-date v-model="filter.startDate" @input="saveStartDate" minimal />
        <q-card-actions align="right">
          <q-btn
            class="btn-date-save"
            label="Save"
            @click="saveStartDate(filter.startDate)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showEndDatePicker" persistent>
      <q-card class="rounded-card">
        <q-card-section>
          <div class="text-h6 text-center">Select Date</div>
        </q-card-section>
        <q-date v-model="filter.endDate" @input="saveEndDate" minimal />
        <q-card-actions align="right">
          <q-btn
            class="btn-date-save"
            label="Save"
            @click="saveEndDate(filter.endDate)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>



    <q-card v-if="loading">
      <q-card-section>
        <q-spinner color="primary" size="50px" />
        <p>Loading appointments...</p>
      </q-card-section>
    </q-card>

    <div v-if="appointments.length > 0">
      <!-- Loop through appointments and generate q-cards -->
      <div
        v-for="(appointmentData, index) in appointments"
        :key="index"
        class="q-mb-md"
      >
        <!-- Long press functionality on q-card -->
        <q-card
          flat
          bordered
          rounded
          class="app-list"
          style="border-radius: 15px"
          @click="
            goToAppointmentDetails(appointmentData.appointment.appointment_id)
          "
          @mousedown="
            userType === 'provider'
              ? startLongPress(appointmentData.appointment)
              : null
          "
          @touchstart="
            userType === 'provider'
              ? startLongPress(appointmentData.appointment)
              : null
          "
          @mouseup="cancelLongPress"
          @touchend="cancelLongPress"
          v-ripple
        >
          <q-card-section>
            <div class="row items-center q-pb-md">
              <!-- Service Name -->
              <div class="col position-relative">
                <q-badge
                  v-if="userType === 'user'"
                  :label="appointmentData.appointment.status"
                  :color="getStatusColor(appointmentData.appointment.status)"
                  class="absolute-top-right q-mt-lg q-mr-sm text-weight-bold"
                />
                <p
                  v-else
                  class="absolute-top-right q-mt-md q-mr-md text-weight-bold text-caption"
                >
                  Appointment
                </p>

                <h6 v-if="userType === 'provider'" class="q-ma-none">
                  {{ appointmentData.details.first_name }}
                  {{ appointmentData.details.last_name }}
                </h6>
                <h6 v-if="userType === 'user'" class="q-ma-none">
                  {{ appointmentData.details.first_name }}
                  {{ appointmentData.details.last_name }}
                </h6>
                <div class="flex column q-mt-sm q-gutter-sm">
                  <div class="q-ma-none q-pa-none row q-gutter-sm">
                    <img
                      v-if="appointmentData.service.media"
                      :src="`http://localhost/BookOn/bookon-backend/api/services/uploads/${appointmentData.service.media}`"
                      style="
                        width: 4.5rem;
                        height: 4.5rem;
                        border-radius: 10px;
                        object-fit: cover;
                      "
                      :alt="appointmentData.service.service_name"
                    />
                    <div class="flex column justify-center">
                      <h6 class="q-ma-none q-pa-none">
                        {{ appointmentData.service.service_name }}
                      </h6>
                      <p class="q-ma-none text-caption">
                        {{ formatDuration(appointmentData.service.duration) }}
                      </p>
                      <p class="text-weight-bold q-ma-none">
                        Php. {{ appointmentData.service.price }}
                      </p>
                    </div>
                  </div>
                  <div
                    v-if="userType === 'provider'"
                    class="row justify-between items-center q-mt-none"
                  >
                    <p class="q-mb-none text-caption q-mt-sm">
                      {{
                        new Date(
                          appointmentData.date.appointment_date
                        ).toLocaleDateString("en-US", {
                          year: "numeric",
                          month: "long",
                          day: "numeric",
                        })
                      }}
                    </p>
                    <p class="text-weight-bold q-mb-none">
                      Php. {{ appointmentData.service.price }}
                    </p>
                  </div>
                </div>

                <div v-if="userType === 'user'" class="column items-end">
                  <p
                    class="text-weight-bold q-ma-none q-mb-sm"
                    v-if="appointmentData.appointment.status === 'Completed'"
                  >
                    Php. {{ appointmentData.service.price }}
                  </p>
                  <q-btn
                    v-if="appointmentData.appointment.status === 'Completed'"
                    color="teal"
                    @click.stop="
                      openFeedbackDialog(
                        appointmentData.appointment.appointment_id,
                        appointmentData.appointment.service_id,
                        appointmentData.appointment.provider_id
                      )
                    "
                    label="Add Review"
                  />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

          <!-- q-dialog for Approve/Deny Actions -->
      <q-dialog v-model="dialogVisible" :position="position">
        <q-card class="dialog">
          <q-card-section class="row justify-center q-pb-none">
          </q-card-section>
          <q-card-section class="status q-pa-none">
            <!-- <div
              class="option"
              @click="
                changeStatus(currentAppointment.appointment_id, 'Pending')
              "
            >
              Pending
            </div> -->
            <!-- <div
              class="option"
              @click="
                changeStatus(currentAppointment.appointment_id, 'Cancelled')
              "
            >
              Cancelled
            </div> -->
             <!-- Line above "Completed" option -->
            <div class="divider"></div>
            <div
              class="option"
              @click="changeStatus(currentAppointment.appointment_id, 'Completed')"
            ><strong>Completed</strong>

            </div>

            <div
              class="option"
              @click="changeStatus(currentAppointment.appointment_id, 'Pending')"
            >
            <strong>Reschedule</strong>
            </div>
          </q-card-section>
        </q-card>
      </q-dialog>


      <!-- Feedback Dialog -->
      <q-dialog v-model="showFeedbackDialog">
        <q-card style="max-width: 400px; width: 100%; border-radius: 15px">
          <!--
          <q-btn
            icon="close"
            flat
            round
            dense
            class="absolute-top-right q-mt-xs q-mr-xs"
            @click="showFeedbackDialog = false"
          />
          -->
          <q-card-section>
            <div class="text-h6 text-weight-bold">Give Feed Back</div>
          </q-card-section>
          <q-card-section>
            <p class="text-subtitle1 text-weight-bold">
              How would you rate your experience?
            </p>
            <div class="row justify-center q-mt-md">
              <q-icon
                :name="n <= rating ? 'star' : 'star_border'"
                size="xl"
                v-for="n in 5"
                :key="n"
                class="q-mx-xs"
                color="orange"
                @click="setRating(n)"
              />
            </div>
          </q-card-section>
          <q-card-section>
            <q-input
              rounded
              borderless
              type="textarea"
              placeholder="Comments/Suggestion"
              v-model="feedbackComments"
              class="q-pl-md q-pr-md"
              style="border: 1px solid teal; border-radius: 15px"
            />
          </q-card-section>
          <q-card-actions align="right">
            <q-btn
              flat
              bordered
              no-caps
              label="Continue"
              color="white"
              @click="submitFeedback"
              class="q-mb-sm q-mr-sm"
              style="
                background-color: teal;
                padding-left: 16px;
                padding-right: 16px;
              "
            />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>

    <div v-else>
      <q-card>
        <q-card-section>
          <p v-if="errorMessage">{{ errorMessage }}</p>
          <p v-else>You have no appointments yet.</p>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script src="./scripts/MyAppointments.js"></script>
<style scoped>
@import "./styles/MyAppointments.scss";
</style>
