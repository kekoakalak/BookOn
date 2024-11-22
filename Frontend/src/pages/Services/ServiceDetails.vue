<template>
  <q-page class="q-pa-md" style="background-color: #f5f5f5">
    <div v-if="service">
      <q-card flat style="background-color: #f5f5f5">
        <!-- Service Media Image -->
        <img
          v-if="service.media"
          :src="service.media ? getImageSrc(service.media) : 'http://localhost/BookOn/bookon-backend/api/services/uploads/' + service.media"
          alt="Service Media"
          class="service-media"
        />


        <!-- Service Title, Price, and Rating -->
        <q-card-section>
          <div class="service-header">
            <h5 class="service-title"><strong>{{ service.name }}</strong></h5>
            <strong>PHP {{ service.price }}</strong>
          </div>
          <div class="duration-rating">
            <div class="service-duration">
              <q-icon name="schedule" /> {{ service.duration || "30 minutes to 1 hour" }}
            </div>
            <div class="service-rating">
              <q-icon name="star" color="white" />
              <span>
                <q-icon name="star" color="yellow" />{{ calculateAverageRating() || 4.5 }}
              </span>
            </div>
          </div>

          <div>
            <strong>Description</strong>
            <p class="service-description">
              {{ expandDescription ? service.description : service.description.slice(0, 100) + '...' }}
            </p>
          </div>
          <q-btn flat :label="expandDescription ? 'Show Less' : 'More'" color="primary" @click="expandDescription = !expandDescription" v-if="service.description && service.description.length > 100" />

          <!-- Availability Section -->
          <p class="availability-header">Availability</p>
          <p>{{ service.availability || "Monday to Friday at 8:00 AM to 5:00 PM" }}</p>

          <!-- Location Section -->
          <div class="location">
            <h5 class="clinic-name">
              <img src="../../assets//dental-log.jpg"  class="clinic-logo" />Dentistree, Dental Clinic
            </h5>
              <div class="location-info">
                <q-icon name="place" class="address-icon" />
                <span>{{ service.location || "Legazpi City, Albay" }}</span>
            </div>
          </div>


            <!-- Similar Services -->
          <div v-if="randomServices.length === 0">
            <strong class="similar-txt">Similar To</strong>
            <div class="similar-services-container">

              <q-card class="similar-service-card">
                <!-- Service Media Image -->
                <img
                  v-if="service.media"
                  :src="service.media ? getImageSrc('meowrro.jpg') : 'http://localhost/BookOn/bookon-backend/api/services/uploads/meowrro.jpg'"
                  alt="Service Media"
                  class="service-medias"
                />
                <div class="similar-to-data">
                  <strong>Sword Training</strong>
                  <p>Duration: 30 mins to 1 hr</p>
                  <p>Price: 10000.00</p>
                </div>
              </q-card>

              <q-card class="similar-service-card">
                <img
                  v-if="service.media"
              :src="service.media ? getImageSrc('Dentures.jpg') : 'http://localhost/BookOn/bookon-backend/api/services/uploads/Dentures.jpg'"
                  alt="Service Medias"
                  class="service-medias"
                />
                <div class="similar-to-data">
                  <strong>Dentures</strong>
                  <p>Duration: 30 mins to 1 hr</p>
                  <p>Price: 9000.00</p>
                </div>
              </q-card>
            </div>
          </div>


            <div v-else>
              <div class="similar-services-container">
                <q-card v-for="service in randomServices" :key="service.name" class="similar-service-card">
                  <img :src="service.media" alt="Service Image" class="service-image" />
                  <div class="q-card-title">{{ service.name }}</div>
                  <div class="q-card-subtitle">{{ service.duration }} - ${{ service.price }}</div>
                </q-card>
              </div>
            </div>


                    <!-- Reviews Section -->
                    <q-card class="review-card" >
                      <div>
                        <p class="reviews-header">Reviews ({{ reviews.length }})</p>
                        <p v-if="reviews.length === 0">No reviews available.</p>

                        <ul class="review-list"> <!-- Add a class here -->
                          <li v-for="review in reviews" :key="review.id">
                            <strong v-if="review.userName">{{ review.userName }}</strong>
                            <strong>{{ review.rating }}</strong>
                            <p>
                              <q-icon name="star" color="yellow" />{{ review.star_rating }}
                            </p>
                            <p>{{ review.feedback }}</p>
                          </li>
                        </ul>
                        <q-btn flat label="See All Reviews" color="primary" @click="goToReviews" />
                      </div>
                    </q-card>



      </q-card-section>

        <!-- Book Appointment Button -->
        <q-card-actions align="left">
          <q-btn
            color="teal"
            @click="goToAppointment"
            label="Book an Appointment"
            class="book-appointment-button"
          />
        </q-card-actions>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api } from '../../boot/axios';

const route = useRoute();
const router = useRouter();
const service = ref(null);
const randomServices = ref([]);
const reviews = ref([]);
const serviceId = ref(Number(route.params.id));
const usersData = ref([]);
const providersData = ref([]);

// Function to attach user names to reviews
const attachUserNamesToReviews = () => {
  reviews.value.forEach(review => {
    console.log("Processing review:", review); // Log each review being processed
    if (review.provider_id && review.provider_id !== 0) {
      const provider = providersData.value.find(p => p.id === review.provider_id);
      review.userName = provider ? `${provider.first_name} ${provider.last_name}` : "Unknown Provider";
    } else if (review.user_id) {
      const user = usersData.value.find(u => u.id === review.user_id);
      review.userName = user ? `${user.first_name} ${user.last_name}` : "Unknown User";
    } else {
      review.userName = "Unknown User"; // Default case
    }
  });
};


onMounted(async () => {
  try {
    // Fetch the selected service
    const response = await api.get(`services/read_single.php?service_id=${serviceId.value}`);
    service.value = response.data;
    console.log("Fetched service:", service.value);
  } catch (error) {
    console.error("Error fetching service details:", error);
  }

  try {
    // Fetch reviews for the service
    const reviewsResponse = await api.get(`ratings/read_by_service.php?service_id=${serviceId.value}`);
    reviews.value = reviewsResponse.data;
    console.log("Reviews Data:", reviews.value); // Log reviews


    // THIS IS THE PROBLEM YWAAAAAAAA
    // Fetch all users and providers
    const usersResponse = await api.get("users/read.php");
    const providersResponse = await api.get("providers/read.php");
    usersData.value = usersResponse.data.users || [];
    providersData.value = providersResponse.data || [];
    console.log("Users Data:", usersData.value); // Log users
    console.log("Providers Data:", providersData.value); // Log providers

    // Attach the user's or provider's name to each review
    attachUserNamesToReviews();

  } catch (error) {
    console.error("Error fetching reviews or user/provider data:", error);
  }

});

const getImageSrc = (imagePath) => {
  return imagePath
    ? `${api.defaults.baseURL}/services/uploads/${imagePath}?ngrok-skip-browser-warning=true`
    : null;
};



// Calculate average rating from reviews
const calculateAverageRating = () => {
  if (reviews.value.length === 0) return 0;
  const totalRating = reviews.value.reduce((acc, review) => acc + review.rating, 0);
  return (totalRating / reviews.value.length).toFixed(1);
};

const goBack = () => {
  router.back();
};

const goToReviews = () => {
  // Ensure you are pushing to the router correctly
  router.push({ name: "AllReviews", params: { serviceId: serviceId.value } });
};

const goToAppointment = () => {
  router.push({ name: "AppointmentNow", params: { serviceId: service.value.id } });
};
</script>



<style scoped>

.header-inline {
  display: flex;
  height: 60px;
}

.service-title-header {
  flex-grow: 1;
  font-size: 25px;
  display: flex;
  justify-content: center;
padding-top: 13px;
margin-bottom: 50px;
  margin-right: 60px;
  height: 60px;
}

.service-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
}

.service-media {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;

}
.service-medias {
  width: 40%;
  height: auto;
  object-fit: cover;
  border-radius: 10px;
}
.service-description {
  font-size: 0.9rem;
  color: #666;
}

.availability-header {
  font-weight: bold;
}

.location {
  display: flex;
  flex-direction: column;
  align-items: left;
  align-items: flex-start; /* Align items to the left */
  margin-left: 0; /* Remove any margin if it's being added unintentionally */
  margin-bottom: 20px;
}

.clinic-name {
  font-size: 1rem;
  font-weight: bold;
  margin: 0;
}

.location-info {
  display: flex;
  align-items: center;
  gap: 3px;
  margin-top: 2px;
}
.similar-to-data strong {
    display: block; /* Makes strong element a block element, so it starts on a new line */
    text-align: left; /* Aligns the text to the left */
    padding-left: 15px;

    margin-bottom: 10px; /* Optional: Control spacing if needed */
}

.similar-to-data p {
    font-size: 10px;
    margin: 0; /* Remove margin from paragraphs */
    padding-left: 15px;
    text-align: left; /* Aligns the text to the left */
}

.similar-services-container {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding: 10px 0;
  margin-bottom: 20px;
}

.similar-service-card {
  flex: 0 0 auto;
  display: flex;
  flex-direction: row;
  align-items: center;
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 10px;
  width: 240px;
  height: 100px;
  text-align: center;
  cursor: pointer;
}

.similar-service-media {
  width: 100%;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}

.similar-service-name {
  font-size: 0.9rem;
  font-weight: bold;
  margin: 0;
}

.reviews-header {
  font-size: 1rem;
  font-weight: bold;
}

.review-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
}

.review-text {
  font-size: 0.9rem;
  color: #666;
}



.book-appointment-button {
  width: calc(100% - 70px); /* Full width minus the combined left and right margins */
  position: fixed;
  bottom: 20px; /* Fix it at the bottom of the screen */
  z-index: 1000;
  margin-left: 10.5px;
}


.duration-rating {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.service-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1rem;
  margin-bottom: 0;
}

.clinic-name {
    display: flex; /* Use flexbox to align items horizontally */
    align-items: center; /* Center align items vertically */
}

.clinic-logo {
    width: 15px; /* Adjust the width as needed */
    height: auto; /* Maintain aspect ratio */
    margin-right: 8px; /* Space between the logo and the text */
}

.review-card{
  padding:20px
}

.review-list {
  list-style-type: none; /* Remove bullets */
  padding: 0; /* Remove default padding */
  margin: 0; /* Remove default margin */
}


.address-icon{
  color: #249990;
}



</style>
