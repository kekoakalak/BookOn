import AddServiceForm from "../AddServiceForm.vue";
import axios from "axios";
import { api } from "../../../boot/axios";

export default {
  components: {
    AddServiceForm,
  },
  data() {
    return {
      showAddServiceForm: false,
      services: [],
      showOptions: false,
      selectedService: null,
      longPressTimer: null,
      userType: null, // Store the user type
      dialog: false,
      confirmDelete: false,
      position: "bottom",
    };
  },
  mounted() {
    this.fetchServices();
    this.getUserType(); // Get the user type on component mount
  },
  methods: {
    toggleAddServiceForm() {
      this.showAddServiceForm = !this.showAddServiceForm;
      if (!this.showAddServiceForm) {
        this.selectedService = null;
      }
    },

    getImageSrc(imagePath) {
      // Construct the full URL with the ngrok-skip-browser-warning parameter
      return `${api.defaults.baseURL}/services/uploads/${imagePath}?ngrok-skip-browser-warning=true`;
    },


    handleServiceForm(serviceData) {
      this.fetchServices(); // Reload services after form submission
      this.showAddServiceForm = false; // Close the form after submission
      this.selectedService = null; // Clear the selected service data
    },

    async fetchServices() {
      try {
        const response = await api.get("/services/read.php");
        const services = response.data.filter((service) => service.name); // Filter out any services without a name

        // Fetch review counts and calculate star ratings
        for (let service of services) {
          try {
            const reviewResponse = await api.get(
              `/ratings/count-service.php?service_id=${service.id}`
            );
            const reviewCount = reviewResponse.data.review_count || 0;

            // Calculate stars based on review count
            let stars = 0;
            if (reviewCount >= 1000) stars = 5;
            else if (reviewCount >= 500) stars = 4;
            else if (reviewCount >= 200) stars = 3;
            else if (reviewCount >= 100) stars = 2;
            else if (reviewCount > 0) stars = 1;

            service.reviewCount = reviewCount;
            service.stars = stars;
          } catch (error) {
            console.error("Error fetching review count for service:", error);
            service.reviewCount = 0;
            service.stars = 0;
          }
        }
        this.services = services;
      } catch (error) {
        console.error("Error fetching services:", error);
      }
    },

    async refreshServices(done) {
      try {
        const response = await api.get("/services/read.php");
        this.services = response.data;

        // Simulate a delay for better UX
        setTimeout(() => {
          done(); // End the pull-to-refresh action
        }, 1000);
      } catch (error) {
        console.error("Error refreshing services:", error);
        done(); // Ensure the pull-to-refresh closes even on error
      }
    },

    // Method to start long press for providers only
    startLongPress(service) {
      if (this.userType === "provider") {
        this.longPressTimer = setTimeout(() => {
          this.dialog = true;
          this.selectedService = service; // Assign the selected service
        }, 800); // Long press duration
      }
    },

    // Function to cancel long press if user releases early
    cancelLongPress() {
      clearTimeout(this.longPressTimer);
    },

    editService(serviceId) {
      this.showOptions = false;
      this.selectedService = this.services.find(
        (service) => service.id === serviceId
      );
      this.showAddServiceForm = true;
      this.dialog = false;
    },

    handleDeleteClick() {
      this.dialog = false;
      this.confirmDelete = true;
    },

    cancelDeletion() {
      this.confirmDelete = false;
      this.dialog = false;
    },

    showNotify(status, message) {
      this.$q.notify({
        position: "top",
        classes: status
          ? "q-notification success-notif"
          : "q-notification error-notif",
        html: true,
        message: status
          ? `<div class="text-bold">Successfully Deleted!</div> ${message}`
          : `<div class="text-bold">Failed to Delete!</div> ${message}`,
      });
    },

    async confirmDeletion() {
      this.confirmDelete = false;
      this.dialog = false;

      if (!this.selectedService || !this.selectedService.id) {
        console.error("Service not selected or ID missing.");
        return;
      }

      try {
        const appointmentResponse = await api.get(
          `/appointments/read.php?service_id=${this.selectedService.id}`
        );

        // Log the response to understand its structure
        console.log("Appointment Response:", appointmentResponse.data);

        const appointments = appointmentResponse.data;

        // Ensure appointments is an array before calling .some()
        const hasPendingOrOngoing =
          Array.isArray(appointments) &&
          appointments.some(
            (appointment) =>
              appointment.status === "Pending" ||
              appointment.status === "ongoing"
          );

        if (hasPendingOrOngoing) {
          this.$q.notify({
            message:
              "Failed to Delete! This service cannot be deleted because there are appointments associated with it.",
            type: "warning",
            position: "top-right",
          });
          return;
        }

        const response = await api.delete(
          `/services/delete.php?service_id=${this.selectedService.id}`
        );

        if (response.data.success) {
          this.services = this.services.filter(
            (service) => service.id !== this.selectedService.id
          );
          this.showNotify(true, "This service has been successfuly deleted.");
        } else {
          this.$q.notify({
            message: `Error: ${response.data.error}`,
            type: "negative",
            position: "top-right",
          });
        }
      } catch (error) {
        console.error("Error deleting service:", error);
        this.$q.notify({
          message:
            "Failed to Delete! An error occurred while trying to delete the service.",
          type: "negative",
          position: "top-right",
        });
      } finally {
        this.showOptions = false;
      }
    },
    viewServiceDetails(serviceId) {
      this.$router.push({ name: "ServiceDetails", params: { id: serviceId } });
    },

    // Get the user type from sessionStorage
    getUserType() {
      this.userType = sessionStorage.getItem("user_type"); // Fetch the user type
    },

    finalize(reset) {
      timer = setTimeout(() => {
        reset();
      }, 1000);
    },
  },
};
