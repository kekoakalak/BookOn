<template>
  <q-page class="q-pa-md" style="background-color: #ebebeb">
    <q-card
      class="flex column"
      flat
      bordered
      style="height: 75vh; border-radius: 15px"
    >
      <!-- Loading spinner -->
      <q-card-section v-if="loading">
        <q-spinner color="primary" size="50px" />
        <p>Loading profile...</p>
      </q-card-section>

      <!-- User profile details -->
      <q-card-section v-else-if="user">
        <div class="flex column items-center q-pa-xl">
          <!--
          <img
            :src="user.imgUrl || 'https://picsum.photos/100?random=1'"
            alt="Profile Picture"
            class="review-img q-mb-sm"
            style="border: 2px solid teal"
          />
          -->
          <p class="text-weight-bold text-h5">
            {{ user.first_name }} {{ user.last_name }}
          </p>
        </div>

        <div class="flex column q-mt-md q-gutter-sm">
          <div class="flex row justify-between items-center q-gutter-sm">
            <div class="flex row items-center">
              <q-icon name="mail" size="sm" />
              <p class="q-mb-none q-pa-none q-ml-xs">Email</p>
            </div>
            <p class="q-ma-none">{{ user.email }}</p>
          </div>

          <div class="flex row justify-between items-center q-gutter-sm">
            <div class="flex row items-center">
              <q-icon name="phone" size="sm" />
              <p class="q-mb-none q-pa-none q-ml-xs">Phone</p>
            </div>
            <p class="q-ma-none">{{ user.mobile }}</p>
          </div>

          <div class="flex row justify-between items-center q-gutter-sm">
            <div class="flex row items-center">
              <q-icon name="date_range" size="sm" />
              <p class="q-mb-none q-pa-none q-ml-xs">Joined</p>
            </div>
            <p class="q-ma-none">
              {{ new Date(user.created_at).toLocaleDateString() }}
            </p>
          </div>
        </div>
      </q-card-section>

      <!-- Error message if profile fails to load -->
      <q-card-section v-else>
        <q-alert type="negative" message="Failed to load profile." />
      </q-card-section>

      <q-space></q-space>

      <!-- Logout button -->
      <q-card-section v-if="isUser" style="align-self: flex-start">
        <q-btn
          flat
          no-caps
          icon="logout"
          label="Log Out"
          class="q-pa-none"
          @click="logout"
        />
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { api } from "../boot/axios";

const user = ref(null);
const router = useRouter();
const loading = ref(true); // Loading state
const userId = sessionStorage.getItem("user_id");
const userType = sessionStorage.getItem("user_type");
const isUser = ref(userType === "user");
const isServiceProvider = ref(userType === "provider");

onMounted(async () => {
  try {
    if (userId && userType) {
      let apiUrl = "";

      // Conditionally set the correct API endpoint based on user_type
      if (isUser.value) {
        apiUrl = `users/profile.php?user_id=${userId}`;
      } else if (isServiceProvider.value) {
        apiUrl = `providers/profile.php?provider_id=${userId}`;
      } else {
        console.error("Invalid user type found in session.");
        loading.value = false;
        return;
      }

      // Fetch data from the appropriate API
      const response = await api.get(apiUrl);

      if (response.data.success) {
        user.value = response.data.user; // Set the fetched user data
      } else {
        console.error("Error fetching profile:", response.data.error);
      }
    } else {
      console.error("User ID or User Type not found in session.");
    }
  } catch (error) {
    console.error("Error during profile fetch:", error);
  } finally {
    loading.value = false; // Disable loading spinner
  }
});

// Logout functionality
const logout = () => {
  sessionStorage.removeItem("user_id");
  sessionStorage.removeItem("user_type");
  sessionStorage.removeItem("userEmail");

  if (isUser.value) {
    console.log("Logging out as a User");
  } else if (isServiceProvider.value) {
    console.log("Logging out as a Service Provider");
  }

  router.push({ path: "/login" }).then(() => {
    location.reload();
  });
};
</script>

<style scoped>
.review-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 50%;
}

.text-h5 {
  font-weight: bold;
}
</style>
