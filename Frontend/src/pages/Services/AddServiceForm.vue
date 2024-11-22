<template>
  <form @submit.prevent="submitForm" class="service-form">
    <div class="form-group">
      <q-label
        class="upload-btn flex flex-column justify-center items-center q-my-sm q-pa-lg"
        @click="triggerFileUpload"
      >
        <div>
          <q-img
            class="upload-icon bg-white text-teal small-img"
            src="../../assets/icon/add_a_photo.svg"
            style="max-width: 40px; max-height: 40px"
          />
          <div class="q-mt-xs">
            <span class="text-teal text-weight-semibold">Upload </span>
            <span class="text-custom-color text-weight-semisbold"
              >Images here</span
            >
          </div>
        </div>
      </q-label>

      <input
        type="file"
        @change="handleFileUpload"
        id="media"
        ref="fileInput"
        style="display: none"
      />

      <div v-if="service.media" class="image-preview q-my-sm">
        <div class="uploaded-files">Uploaded Files</div>

        <div
          class="img-name img-name-container row flex items-center justify-between"
        >
          <!-- Image and Text Group -->
          <div class="flex items-center" style="width: 80%">
            <q-img
              v-if="getImageSrc()"
              :src="getImageSrc()"
              :key="getImageSrc()"
              alt="Image Preview"
              style="
                width: 80px;
                height: 60px;
                object-fit: cover;
                border-radius: 4px;
                margin-right: 8px;
              "
              class="service-media"
              :error-src="'/path/to/fallback-image.png'"
            />

            <!-- Name or Media Text with Truncation -->
            <div class="q-ml-sm text-truncate">
              {{ service.media.name || service.media }}
            </div>
          </div>

          <!-- Close Icon with Click Event -->
          <q-icon
            name="close"
            class="cursor-pointer close-icon"
            @click="removeMedia"
          />
        </div>
      </div>

      <p v-if="!service.media && submitted" class="error-message shake">
        A media file is required.
      </p>
    </div>
    <div class="form-group">
      <q-label>Service Name: <span class="required-asterisk">*</span></q-label>
      <q-input
        dense
        v-model="service.name"
        id="name"
        type="text"
        placeholder="Service Name"
        borderless
        class="input q-pb-none q-mb-md"
      />
      <p v-if="submitted && !service.name" class="error-message shake">
        Service name is required.
      </p>
    </div>

    <div class="form-group">
      <q-label
        >Service Description: <span class="required-asterisk">*</span></q-label
      >
      <q-input
        v-model="service.description"
        dense
        id="description"
        type="textarea"
        placeholder="Enter Description"
        borderless
        class="input q-pb-none q-mb-md"
      />
      <p v-if="submitted && !service.description" class="error-message shake">
        Description is required.
      </p>
    </div>

    <div class="form-group">
      <q-label for="duration"
        >Duration: <span class="required-asterisk">*</span></q-label
      >
      <q-input
        v-model="service.duration"
        dense
        id="duration"
        placeholder="Enter Duration"
        borderless
        type="text"
        class="input q-pb-none q-mb-md"
      />
      <p v-if="submitted && !service.duration" class="error-message shake">
        Duration is required.
      </p>
    </div>

    <div class="form-group">
      <q-label for="price"
        >Price: <span class="required-asterisk">*</span></q-label
      >
      <q-input
        v-model="service.price"
        dense
        type="number"
        id="price"
        placeholder="Enter Price"
        borderless
        class="input q-pb-none q-mb-md"
      />
      <p
        v-if="submitted && (!service.price || isNaN(service.price))"
        class="error-message shake"
      >
        A valid price is required.
      </p>
    </div>

    <div class="form-group">
      <q-label>Availability: <span class="required-asterisk">*</span></q-label>
      <div class="availability-details">
        <div class="form-group time-inputs">
          <div class="time-input-wrapper">
            <button
              type="button"
              @click="openClockModal('start')"
              class="time-btn"
            >
              {{ service.startTime || "--:-- AM" }}
            </button>
            <p
              v-if="submitted && !service.startTime"
              class="error-message shake"
            >
              Time is required.
            </p>
          </div>

          <span>to</span>

          <div class="time-input-wrapper">
            <button
              type="button"
              @click="openClockModal('end')"
              class="time-btn"
            >
              {{ service.endTime || "--:-- AM" }}
            </button>
            <p v-if="submitted && !service.endTime" class="error-message shake">
              Time is required.
            </p>
          </div>
        </div>

        <div class="availability-days">
          <span
            v-for="day in days"
            :key="day"
            :class="{
              'active-day': service.availability.includes(day),
            }"
            @click="toggleAvailability(day)"
          >
            {{ day }}
          </span>
        </div>
        <p
          v-if="submitted && service.availability.length === 0"
          class="error-message shake"
        >
          At least one availability day is required.
        </p>
      </div>
    </div>

    <div class="form-actions">
      <q-btn
        rounded
        dense
        flat
        no-caps
        type="submit"
        class="save-btn full-width q-mt-lg q-py-sm"
        :label="isEditMode ? 'Update Service' : 'Save Service'"
      />
    </div>
  </form>

  <div v-if="clockModalVisible" class="clock-modal-overlay">
    <div class="clock-modal">
      <div class="clock-time-picker">
        <q-time
          flat
          rounded
          dense
          v-model="timePicker[selectedTimeType === 'start' ? 0 : 1]"
          mask="hh:mm A"
          class="clock-picker"
          text-color="black"
        />
      </div>
      <div class="clock-actions">
        <q-btn
          @click="cancelTimeSelection"
          label="Cancel"
          class="cancel-clock-btn"
          rounded
          flat
          dense
          no-caps
        />
        <q-btn
          @click="saveTimeSelection"
          label="Save"
          class="save-clock-btn"
          rounded
          flat
          dense
          no-caps
        />
      </div>
    </div>
  </div>
</template>

<script src="./scripts/AddServiceForm.js"></script>

<style scoped>
@import url(./styles/AddServiceForm.scss);
</style>
