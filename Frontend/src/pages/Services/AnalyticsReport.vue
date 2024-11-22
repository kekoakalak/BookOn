<template>
  <q-page padding style="background-color: #ebebeb">
    <div class="row items-center justify-end q-mb-md">
      <div class="row items-center">
        <q-btn flat icon="filter_list" @click="showFilter = true" />
        <q-btn flat icon="download" @click="showGenerateReportDialog = true" />
      </div>
    </div>

    <q-card flat bordered class="q-pa-md q-mb-md">
      <q-card-section>
        <div class="text-h6">Services</div>
      </q-card-section>

      <q-list bordered>
        <q-item
          class="item-border"
          v-for="service in services"
          :key="service.name"
          clickable
        >
          <q-item-section>
            <q-item-label>{{ service.name }}</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-badge
              color="white"
              style="font-weight: bold"
              text-color="black"
              class="q-ml-sm"
              >{{ service.total }}</q-badge
            >
          </q-item-section>
        </q-item>
      </q-list>
    </q-card>

    <q-card flat bordered class="q-pa-md q-mb-md">
      <q-card-section class="flex-between">
        <div class="text-h6">Ongoing Appointments</div>
        <div class="text-caption">{{ pendingAppointments }} totals</div>
      </q-card-section>
      <q-card-section>
        <ChartComponent :data="pendingData" />
      </q-card-section>
    </q-card>

    <q-card flat bordered class="q-pa-md">
      <q-card-section class="flex-between">
        <div class="text-h6">Completed Appointments</div>
        <div class="text-caption">{{ completedAppointments }} totals</div>
      </q-card-section>
      <q-card-section>
        <ChartComponent :data="completedData" />
      </q-card-section>
    </q-card>



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






            <!-- Generate Report Dialog -->
        <q-dialog v-model="showGenerateReportDialog" persistent>
          <q-card class="custom-card">
            <!-- Close Button in the top-right corner -->
            <q-btn
              flat
              icon="close"
              size="sm"
              color="black"
              class="close-btn"
              @click="showGenerateReportDialog = false"
              aria-label="Close"
            />

            <q-card-section>
              <!-- Icon above the text -->
              <div class="text-center">
                <q-icon name="download" size="36px" color="#249990" class="dl-icon" />
              </div>
              <div class="text-h6 text-center">Generate Report?</div>
              <p class="text-center">This will generate the report.</p>
            </q-card-section>

            <q-card-actions align="right">
              <q-btn label="Confirm" @click="generateReport" class="custom-btn" />
            </q-card-actions>
          </q-card>
        </q-dialog>




  </q-page>
</template>

<script src="./scripts/AnalyticsReport.js"></script>

<style scoped>
@import url(./styles/AnalyticsReport.scss);
</style>
