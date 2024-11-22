<template>
  <q-layout view="lHh Lpr lFf">
    <q-header style="background-color: white">
      <q-toolbar>
        <q-btn
          dense
          flat
          borderless
          v-if="
            !isProfilePage &&
            !isServiceDetailsPage &&
            !isReviewDetailsPage &&
            !showAddServiceForm &&
            !isUser
          "
          color="black"
          icon="menu"
          @click="drawerOpen"
        >
          <q-menu v-model="drawerOpen" anchor="bottom left" self="top left">
            <q-item clickable @click="goToProfile">
              <q-item-section>
                <q-icon name="account_circle" />
              </q-item-section>
              <q-item-section class="q-pa-none q-pr-md q-ma-none"
                >Profile</q-item-section
              >
            </q-item>
            <q-item clickable @click="logout">
              <q-item-section>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section class="q-pa-none q-pr-md q-ma-none"
                >Logout</q-item-section
              >
            </q-item>
          </q-menu>
        </q-btn>

        <!-- Back button for Profile and Service Details pages -->
        <q-btn
          v-if="
            (isServiceProvider && isProfilePage) ||
            isServiceDetailsPage ||
            isReviewDetailsPage ||
            (isServiceProvider && showAddServiceForm)
          "
          dense
          flat
          borderless
          color="black"
          icon="arrow_back_ios"
          @click="goBack"
        />
        <q-toolbar-title
          class="text-weight-medium text-black text-center q-ma-none q-pa-none"
          >{{ headerTitle }}</q-toolbar-title
        >

        <div
          v-if="
            !isProfilePage &&
            !isServiceDetailsPage &&
            !isReviewDetailsPage &&
            !showAddServiceForm
          "
        >
          <!-- Direct link to Notification Page on click -->
          <q-btn
            v-if="!isUser"
            dense
            flat
            borderless
            icon="notifications"
            color="black"
            @click="goToNotification"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view v-if="!showAddServiceForm" />
      <!-- Conditionally render AddServiceForm based on user_type -->
      <AddServiceForm
        v-if="showAddServiceForm && isServiceProvider"
        @form-close="toggleServiceForm"
        @service-submitted="handleServiceSubmit"
      />
      <div v-if="!showAddServiceForm && services.length > 0 && servicesVisible">
        <div class="service-list">
          <div
            v-for="service in services"
            :key="service.id"
            class="service-card"
            @click="goToServiceDetails(service)"
          >
            <img
              v-if="service.media"
              :src="service.media"
              alt="Service Media"
              class="service-media"
            />
            <h2>{{ service.name }}</h2>
            <p>Price: {{ service.price }} PHP</p>
            <p>Duration: {{ service.duration }}</p>
          </div>
        </div>
      </div>
    </q-page-container>

    <!-- Footer: Only display if not on the Profile or Service Details pages -->
    <q-footer
      v-if="
        !isServiceDetailsPage &&
        !isReviewDetailsPage &&
        !showAddServiceForm &&
        (isUser || !isProfilePage)
      "
      class="footer flex row justify-between q-pa-sm"
      bordered
    >
      <div
        v-for="(link, index) in filteredLinksList"
        :key="link.icon"
        class="footer-button-container q-pa-xs"
        :class="{ 'footer-button-active': selectedIndex === index }"
      >
        <q-btn
          :icon="getIcon(link.icon, index)"
          :to="link.link"
          flat
          round
          @click="handleButtonClick(index)"
          class="button"
        />
        <div
          :class="{ 'footer-label-active': selectedIndex === index }"
          class="footer-label"
        >
          {{ link.label }}
        </div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import AddServiceForm from "../pages/Services/AddServiceForm.vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();
const isLoggedIn = ref(false);
const userType = ref(null);
const isServiceProvider = ref(false);
const isUser = ref(false);
const isProfilePage = ref(false);
const isServiceDetailsPage = ref(false);
const isReviewDetailsPage = ref(false);
const drawerOpen = ref(false);

defineOptions({
  name: "MainLayout",
});

const selectedIndex = ref(0);
const headerTitle = ref("Home");
const showAddServiceForm = ref(false);
const services = ref([]);
const servicesVisible = ref(false);
const linksList = [
  { icon: "home", link: "/", title: "Home", label: "Home" },
  {
    icon: "event",
    link: "/appointments",
    title: "Appointments",
    label: "Appointment",
  },
  { icon: "list", link: "/services", title: "Services", label: "Services" },
  {
    icon: "star",
    link: "/reviews",
    title: "Reviews and Ratings",
    label: "Review",
  },
  {
    icon: "analytics",
    link: "/analytics",
    title: "Analytics and Reporting",
    label: "Analytics",
  },
];

// Computed property to filter and order links based on user type
const filteredLinksList = computed(() => {
  if (isServiceProvider.value) {
    return linksList;
  } else if (isUser.value) {
    return linksList
      .map((link) => {
        if (link.title === "Services") {
          return {
            ...link,
            icon: "person",
            link: "/profile",
            label: "Profile",
          };
        } else if (link.title === "Home") {
          return { ...link, icon: "home", link: "/services" };
        }
        return link;
      })
      .filter((link) => link.title === "Home" || link.title === "Appointments")
      .concat(
        {
          icon: "notifications",
          link: "/notifications",
          title: "Notifications",
          label: "Notifications",
        },
        {
          icon: "person",
          link: "/profile",
          title: "Profile",
          label: "Profile",
        }
      );
  }
  return [];
});

const formatDate = (date) => {
  const options = { year: "numeric", month: "numeric", day: "numeric" };
  return date.toLocaleDateString(undefined, options);
};

// Function to update header title and page visibility based on path and user type
const updateHeaderTitle = (path, type) => {
  switch (path) {
    case "/profile":
      headerTitle.value = "Profile";
      isProfilePage.value = true;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
    case "/appointments":
      headerTitle.value = "Appointments";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
    case "/services":
      headerTitle.value = "Services";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
    case `/service/${route.params.id}`:
      headerTitle.value = "Service Details";
      isProfilePage.value = false;
      isServiceDetailsPage.value = true;
      isReviewDetailsPage.value = false;
      break;
    case "/reviews":
      headerTitle.value = "Reviews and Ratings";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
    case `/reviews/${route.params.serviceId}`:
      headerTitle.value = "Reviews";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = true;
      break;
    case "/analytics":
      headerTitle.value =
        type === "provider" ? "Analytics and Reporting" : "Home";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
    case `/appointment/${route.params.serviceId}`:
      headerTitle.value = "Appointment Booking";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;

    case `/appointment-details/${route.params.appointment_id}`:
      headerTitle.value = "Appointment Details";
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;

    default:
      headerTitle.value = formatDate(new Date());
      isProfilePage.value = false;
      isServiceDetailsPage.value = false;
      isReviewDetailsPage.value = false;
      break;
  }
};

// Watch the route path and user type to update header title dynamically
watch(
  [() => route.path, userType],
  ([newPath, type]) => {
    updateHeaderTitle(newPath, type);
  },
  { immediate: true } // Ensures the title updates on initial load
);

// Function to go to the profile page
const goToNotification = () => {
  router.push({ path: "/notification" });
};

const goToProfile = () => {
  router.push({ path: "/profile" });
};

// Go back to the previous page
const goBack = () => {
  if (showAddServiceForm.value) {
    showAddServiceForm.value = false;
    servicesVisible.value = true;
  } else {
    // Goes back to the previous page
    router.go(-1);
  }
  // Update the header and footer visibility
  setTimeout(() => updateHeaderTitle(route.path, userType.value), 0);
};

// Check if the user is logged in and retrieve their user_type
onMounted(() => {
  const userId = sessionStorage.getItem("user_id");
  const type = sessionStorage.getItem("user_type");

  if (userId) {
    isLoggedIn.value = true;
    userType.value = type;
    isServiceProvider.value = userType.value === "provider";
    isUser.value = userType.value === "user";
  } else {
    isLoggedIn.value = false;
  }
});

// Button click handling logic
const handleButtonClick = (index) => {
  selectedIndex.value = index;
  headerTitle.value = filteredLinksList.value[index].title;

  if (filteredLinksList.value[index].title === "Services") {
    // Toggle the AddServiceForm if the add_circle icon is shown
    if (getIcon("list", index) === "add_circle") {
      showAddServiceForm.value = !showAddServiceForm.value; // Toggle the form visibility
      servicesVisible.value = !showAddServiceForm.value; // Hide service list if form is visible
      headerTitle.value = showAddServiceForm.value
        ? "Add a Service"
        : "Services";
    } else {
      showAddServiceForm.value = false; // Hide AddServiceForm when clicking list icon
      servicesVisible.value = true; // Show the service list
    }
  } else {
    showAddServiceForm.value = false;
    servicesVisible.value = false;
  }
};

// Icon handling logic
const getIcon = (icon, index) => {
  if (
    filteredLinksList.value[index].title === "Services" &&
    servicesVisible.value &&
    isServiceProvider.value
  ) {
    return "add_circle";
  }
  return icon;
};

// Toggle service form visibility (used by AddServiceForm component)
const toggleServiceForm = () => {
  showAddServiceForm.value = !showAddServiceForm.value;
};

// Handle service submission
const handleServiceSubmit = (newService) => {
  services.value.push({ ...newService, id: Date.now() });
  servicesVisible.value = true;
  toggleServiceForm();
};

// Logout functionality
const logout = () => {
  sessionStorage.removeItem("user_id");
  sessionStorage.removeItem("user_type");
  sessionStorage.removeItem("userEmail");
  router.push({ path: "/login" }).then(() => {
    location.reload();
  });
};

// Go to service details page
const goToServiceDetails = (service) => {
  router.push({
    name: "ServiceDetails",
    params: { id: service.id },
    query: { service: JSON.stringify(service) },
  });
};
</script>

<style lang="scss">
.button {
  color: black;
}

.footer {
  background-color: white;
}

.service-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.service-card {
  flex: 1 1 calc(42%);
  max-width: calc(42%);
  margin-left: 3%;
  margin-top: 3%;
  margin-bottom: 0%;
  width: 100%;
  max-width: 175px;
  display: flex;
  flex-direction: column;
  padding: 5px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;

  &:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }
}

.service-card h2 {
  font-size: 1.2rem;
  font-weight: bold;
  text-align: left;
  margin: 0;
}

.service-card p {
  font-size: 0.9rem;
  margin: 0;
  text-align: left;
}

.service-media {
  width: 100%;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.footer-button-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.footer-label {
  font-size: 0.6rem;
  color: black;
}

.footer-button-active .button,
.footer-label-active {
  color: teal;
}
</style>
