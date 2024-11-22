<template>
  <q-page class="q-pa-md" style="background-color: gainsboro">
    <!-- Back Button
    <q-btn
      flat
      icon="arrow_back"
      label="Back"
      @click="goBack"
      class="q-mb-md"
    />
    -->

    <div v-if="reviews.length > 0">
      <h5 class="text-center">Reviews for {{ serviceName }}</h5>

      <div v-for="review in reviews" :key="review.rating_id" class="q-mb-md">
        <q-card flat bordered class="q-pa-md q-mb-md review-card">
          <q-card-section>
            <div class="row items-center q-mb-xs">
              <!-- User Name Placeholder (adjust based on actual user name data) -->
              <div class="col-8">
                <p>
                  <strong
                    >{{ review.first_name }} {{ review.last_name }}</strong
                  >
                </p>
              </div>
              <div class="col-4 text-right">
                <p class="review-date">
                  {{ new Date(review.created_at).toLocaleDateString() }}
                </p>
              </div>
            </div>

            <!-- Star Rating -->
            <div class="row q-mb-sm">
              <div class="col-auto">
                <div class="stars">
                  <q-icon
                    v-for="n in 5"
                    :key="n"
                    :name="n <= review.star_rating ? 'star' : 'star_border'"
                    size="20px"
                    color="orange"
                    class="q-mx-xs"
                  />
                </div>
              </div>
            </div>

            <!-- Service and Feedback -->
            <div>
              <p class="review-feedback">
                {{ review.feedback || "No feedback provided" }}
              </p>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div v-else>
      <p class="text-center">No reviews available for this service.</p>
    </div>
  </q-page>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { api } from "../../boot/axios";

const route = useRoute();
const router = useRouter();
const reviews = ref([]);
const serviceName = ref("");

// Fetch reviews on page load
onMounted(async () => {
  const serviceId = route.params.serviceId;
  try {
    // Fetch service name for display
    const serviceResponse = await api.get(
      `/services/read_single.php?service_id=${serviceId}`
    );
    serviceName.value = serviceResponse.data.name;

    // Fetch reviews
    const response = await api.get(
      `/ratings/read_by_service.php?service_id=${serviceId}`
    );
    reviews.value = response.data;
  } catch (error) {
    console.error("Error fetching reviews:", error);
  }
});

// Go back to the previous page
/**
const goBack = () => {
  router.back();
};
 */
</script>

<style scoped>
.text-center {
  text-align: center;
}

.review-card {
  border-radius: 15px;
}

.review-date {
  font-size: 12px;
  color: #999;
}

.stars {
  display: flex;
}

.review-feedback {
  font-size: 14px;
  color: #555;
}
</style>
