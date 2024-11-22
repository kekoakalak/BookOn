<template>
  <q-page class="service-management" style="background-color: #ebebeb">
    <div class="header"></div>

    <AddServiceForm
      v-if="showAddServiceForm"
      :service-id="Number(selectedService?.id)"
      @service-submitted="handleServiceForm"
      @form-closed="toggleAddServiceForm"
    />

    <q-pull-to-refresh v-if="!showAddServiceForm" @refresh="refreshServices">
      <div class="service-list">
        <!-- If no services are found, display a message -->
        <div v-if="services.length === 0" class="no-services-found">
          <q-icon name="report_problem" class="q-mr-sm" /> No services found.
        </div>

        <q-card
          v-for="service in services"
          :key="service.id"
          class="service-card"
          bordered
          @click="viewServiceDetails(service.id)"
          @mousedown="userType === 'provider' ? startLongPress(service) : null"
          @touchstart="userType === 'provider' ? startLongPress(service) : null"
          @mouseup="cancelLongPress"
          @touchend="cancelLongPress"
        >
          <!-- Service Media Image -->
          <q-img
            v-if="service.media"
            :src="
              service.media
                ? getImageSrc(service.media)
                : 'http://localhost/BookOn/bookon-backend/api/services/uploads/' +
                  service.media
            "
            alt="Service Media"
            class="service-media"
            style="
              border-bottom-left-radius: 12px;
              border-bottom-right-radius: 12px;
            "
            ratio="16:9"
          />

          <!-- Service Content -->
          <q-card-section class="q-pa-none">
            <div class="column q-ma-none q-gutter-xs">
              <div class="q-mx-sm">
                <q-item-label class="text-h6">{{ service.name }}</q-item-label>
                <q-item-label caption>
                  {{ service.duration }} minutes
                </q-item-label>
              </div>
              <div class="q-mx-sm q-pt-md">
                <q-item-label caption>
                  ₱{{ service.price }} <br />
                </q-item-label>
                <q-item-label>
                  <q-rating
                    v-model="service.stars"
                    max="5"
                    color="orange"
                    size="xs"
                    readonly
                    class="q-ma-none q-pa-none q-mb-sm"
                  />
                  <span v-if="service.reviewCount" class="review-count">
                    ({{ service.reviewCount }})
                  </span>
                  <span v-else class="review-count">(0)</span>
                </q-item-label>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- q-dialog for Edit/Delete Action -->
        <q-dialog
          v-if="userType === 'provider'"
          v-model="dialog"
          :position="position"
          class=""
        >
          <q-card class="dialog q-pa-none">
            <q-card-section
              class="row justify-center q-pa-none q-ma-sm q-mb-md"
            >
              <hr class="custom-hr" />
            </q-card-section>
            <q-card-section class="action q-pa-none q-mb-md">
              <div class="edit-btn" @click="editService(selectedService.id)">
                Edit
              </div>
              <div class="del-btn" @click="handleDeleteClick">Delete</div>
            </q-card-section>
          </q-card>
        </q-dialog>

        <q-dialog class="confirm-dialog" v-model="confirmDelete" persistent>
          <q-card class="main-card">
            <q-card-section
              class="row items-center justify-center q-ma-none q-pa-none"
              ><q-avatar
                class="help-icon q-pb-none q-mb-none q-mt-xl"
                :style="{ backgroundColor: 'var(--help-icon-color)' }"
              >
                <q-img src="../../assets/icon/help.svg"></q-img>
              </q-avatar>
            </q-card-section>
            <q-card-section class="row items-center justify-center q-ma-none">
              <h5 class="q-pa-none q-mt-none q-mb-sm text-weight-semibold">
                Delete Service
              </h5>
              <span class="q-pa-none q-ma-none"
                >Are you sure you want to delete this service?</span
              >
              <span>This action cannot be undone.</span>
            </q-card-section>

            <q-card-actions class="q-mb-lg" align="center">
              <q-btn
                flat
                label="Cancel"
                class="cancel-btn"
                rounded
                @click="cancelDeletion"
              />
              <q-btn
                flat
                rounded
                label="Confirm"
                class="confirm-btn"
                @click="confirmDeletion"
              />
            </q-card-actions>
          </q-card>
        </q-dialog>
      </div>
    </q-pull-to-refresh>
  </q-page>
</template>

<script src="./scripts/ServiceManagement.js"></script>
<style>
@import "./styles/ServiceManagement.scss";
</style>
