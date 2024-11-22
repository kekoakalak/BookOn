<template>
  <q-page class="q-pa-md" style="background-color: #ebebeb">
    <div v-if="reviews.length > 0">
      <div
        v-for="(reviewData, index) in reviews"
        :key="reviewData.review.rating_id"
        class="flex row justify-between"
      >
        <q-card
          flat
          bordered
          class="fixed-card flex column justify-between q-pa-none q-mb-md"
        >
          <q-card-section>
            <!-- Review Header with Provider Name and Review Date -->
            <div class="flex row justify-between q-gutter-none">
              <div class="flex row justify-between items-start">
                <div class="profile row items-center q-gutter-sm">
                  <!-- Display provider's name if logged in as user -->
                  <p
                    v-if="userType === 'user'"
                    class="text-subtitle1 text-weight-bold q-mb-none"
                  >
                    <strong>
                      {{ reviewData.provider.provider_first_name }}
                      {{ reviewData.provider.provider_last_name }}
                    </strong>
                  </p>
                  <!-- Display user's name if logged in as provider -->
                  <p
                    v-if="userType === 'provider'"
                    class="text-subtitle1 text-weight-bold q-mb-none"
                  >
                    <strong>
                      {{ reviewData.user.user_first_name }}
                      {{ reviewData.user.user_last_name }}
                    </strong>
                  </p>
                </div>
              </div>
              <q-card-section class="flex row q-ml-none q-pa-none">
                <p
                  class="q-pa-none text-weight-thin text-caption"
                  :style="{ color: 'gray' }"
                >
                  {{
                    new Date(
                      Date.parse(reviewData.review.created_at)
                    ).toLocaleDateString("en-US", {
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                    })
                  }}
                </p>
              </q-card-section>
            </div>

            <!-- Star Rating -->
            <q-rating
              v-model="reviewData.review.star_rating"
              max="5"
              color="orange"
              size="sm"
              readonly
              class="q-ma-none q-pa-none q-mb-sm"
            />

            <!-- Service Details -->
            <div v-if="reviewData.service" class="q-pa-none q-ma-none">
              <p class="q-mb-none text-caption text-weight-thin">
                Service: {{ reviewData.service.service_name }}
              </p>
            </div>

            <!-- Feedback with Truncated Text -->
            <q-card-section class="q-pa-none q-ma-none">
              <p
                v-if="!isExpanded[index]"
                class="review-description q-pa-none q-ma-none"
              >
                {{ truncatedReview(reviewData.review.feedback, index) }}
              </p>
              <p v-else>
                {{ reviewData.review.feedback || "No feedback provided" }}
              </p>
            </q-card-section>

            <!-- Toggle Button for Truncated View -->
            <q-card-section
              class="flex justify-end q-pa-none"
              :class="{ 'q-mx-md': $q.screen.gt.sm }"
              style="width: 100%"
            >
              <!-- Only show button if feedback length exceeds max length -->
              <q-btn
                v-if="
                  reviewData.review.feedback &&
                  reviewData.review.feedback.length > 100
                "
                flat
                class="review-more-btn q-pa-none q-ma-none text-weight-bold"
                @click="toggleExpand(index)"
                no-caps
                :label="isExpanded[index] ? 'See less' : 'See more'"
              />
            </q-card-section>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div v-else>
      <p class="text-center">No reviews available.</p>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { api } from "../boot/axios";

const router = useRouter();
const reviews = ref([]);
const isExpanded = ref([]); // Track expanded state for each review

const userId = sessionStorage.getItem("user_id");
const userType = sessionStorage.getItem("user_type");

// Fetch all reviews on page load
onMounted(async () => {
  try {
    const response = await api.get(`/ratings/read.php`);
    if (response.data.success) {
      reviews.value = response.data.reviews;
      isExpanded.value = Array(reviews.value.length).fill(false); // Initialize expanded state
    } else {
      console.error("Error fetching reviews:", response.data.error);
    }
  } catch (error) {
    console.error("Error fetching reviews:", error);
  }
});

// Truncate feedback if it exceeds a specific length
const truncatedReview = (feedback, index) => {
  const maxLength = 100; // Define max length for truncation
  return feedback && feedback.length > maxLength
    ? feedback.substring(0, maxLength) + "..."
    : feedback;
};

// Toggle expand/collapse state for each review
const toggleExpand = (index) => {
  isExpanded.value[index] = !isExpanded.value[index];
};
</script>

<style scoped>
.text-center {
  text-align: center;
}

.fixed-card {
  width: 100%;
  border-radius: 15px;
}

.review-date {
  font-size: 12px;
  color: #999;
}

.stars {
  display: flex;
}

.service-description {
  font-size: 14px;
  color: #777;
}
</style>
