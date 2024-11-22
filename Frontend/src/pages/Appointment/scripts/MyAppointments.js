import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useQuasar } from "quasar";
import { api } from "../../../boot/axios";

export default {
  setup() {
    const $q = useQuasar();
    const loading = ref(true);
    const appointments = ref([]);
    const errorMessage = ref(null);
    const dialogVisible = ref(false);
    const showFeedbackDialog = ref(false);
    const feedbackComments = ref("");
    const currentAppointmentId = ref(null);
    const currentServiceId = ref(null);
    const currentProviderId = ref(null);
    const rating = ref(0);
    const currentAppointment = ref(null);
    const longPressTimeout = ref(null);
    const userId = sessionStorage.getItem("user_id");
    const userType = sessionStorage.getItem("user_type");

    // Filter state
    const showFilter = ref(false);
    const filter = ref({
      startDate: null,
      endDate: null,
    });

        // Apply filter only when the user clicks the Apply button
    const applyFilter = () => {
      loading.value = true;
      fetchAppointments();
      showFilter.value = false;  // Close the filter dialog

    };


    // Fetch appointments with optional filter
    const fetchAppointments = async () => {
      const { startDate, endDate } = filter.value;

      // Prepare the API URL with filters
      let apiUrl = `/appointments/read.php?user_id=${userId}`;
      if (userType === "provider") {
        apiUrl = `/appointments/read.php?provider_id=${userId}`;
      }

      // Add date filters if provided
      if (startDate && endDate) {
        apiUrl += `&start_date=${formatDate(startDate)}&end_date=${formatDate(endDate)}`;
      }

      try {
        const response = await api.get(apiUrl);
        if (response.data.success) {
          appointments.value = response.data.appointments;
        } else {
          errorMessage.value = response.data.error || "Failed to fetch appointments.";
        }
      } catch (error) {
        errorMessage.value = "An error occurred while fetching appointments.";
      } finally {
        loading.value = false;
      }
    };

    // Format date to YYYY-MM-DD
    const formatDate = (date) => {
      const d = new Date(date);
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      const year = d.getFullYear();
      return `${year}-${month}-${day}`;
    };

    // Apply preset date ranges
    const applyPreset = (type) => {
      const now = new Date();
      switch (type) {
        case 'allTime':
          filter.value.startDate = null;
          filter.value.endDate = null;
          break;
        case 'today':
          filter.value.startDate = formatDate(now);
          filter.value.endDate = formatDate(now);
          break;
        case 'yesterday':
          now.setDate(now.getDate() - 1);
          filter.value.startDate = formatDate(now);
          filter.value.endDate = formatDate(now);
          break;
        case 'past7':
          filter.value.startDate = formatDate(new Date(now.setDate(now.getDate() - 6)));
          filter.value.endDate = formatDate(new Date());
          break;
        case 'past30':
          filter.value.startDate = formatDate(new Date(now.setDate(now.getDate() - 29)));
          filter.value.endDate = formatDate(new Date());
          break;
      }
      fetchAppointments();
    };


    // Open Feedback Dialog
    const openFeedbackDialog = (appointmentId, serviceId, providerId) => {
      currentAppointmentId.value = appointmentId;
      currentServiceId.value = serviceId;
      currentProviderId.value = providerId;
      showFeedbackDialog.value = true;
    };

    // Set Star Rating
    const setRating = (value) => {
      rating.value = rating.value === value ? 0 : value;
    };

    const router = useRouter();

    onMounted(async () => {
      if (!userId || !userType) {
        errorMessage.value = "User ID or type is not available in session. Please login again.";
        return;
      }

      loading.value = true;

      // Fetch appointments with the current filter settings
      fetchAppointments();
    });


    // Start long press action
    const startLongPress = (appointment) => {
      longPressTimeout.value = setTimeout(() => {
        currentAppointment.value = appointment;
        dialogVisible.value = true;
      }, 600);
    };

    // Cancel long press if user releases early
    const cancelLongPress = () => {
      if (longPressTimeout.value) {
        clearTimeout(longPressTimeout.value);
        longPressTimeout.value = null;
      }
    };

    // Get appointment status color
    const getStatusColor = (status) => {
      switch (status) {
        case "Completed":
          return "green";
        case "Cancelled":
          return "red";
        case "Pending":
        default:
          return "yellow";
      }
    };

    // Change appointment status
    const changeStatus = async (appointmentId, status) => {
      try {
        const response = await api.post("/appointments/update.php", {
          appointment_id: appointmentId,
          status: status,
        });
        if (response.data.success) {
          const updatedAppointment = appointments.value.find(
            (appointment) =>
              appointment.appointment.appointment_id === appointmentId
          );
          if (updatedAppointment) {
            updatedAppointment.appointment.status = status;
          }
          dialogVisible.value = false;
        } else {
          errorMessage.value =
            response.data.error || "Failed to update appointment status.";
        }
      } catch (error) {
        errorMessage.value = "An error occurred while updating the status.";
      }
    };

    const goToAppointmentDetails = (appointmentId) => {
      if (!appointmentId) {
        console.error("Appointment ID is missing");
        return;
      }
      router.push({
        name: "AppointmentDetails",
        params: { appointment_id: appointmentId },
      });
    };

    const showNotify = (status, message) => {
      $q.notify({
        position: "top",
        classes: status
          ? "q-notification success-notif"
          : "q-notification error-notif",
        html: true,
        message: status
          ? `<div class="text-bold">Successfully reviewed dental filling service!</div> <span style="color: #a9a9a9;">${message}</span>`
          : `<div class="text-bold">Failed to review!</div> <span style="color: #a9a9a9;">${message}</span>`,
      });
    };

    const submitFeedback = async () => {
      const userId = sessionStorage.getItem("user_id");

      // Check for required fields
      if (!currentAppointmentId.value || !userId || !rating.value) {
        console.error("Error: Missing required fields");
        return;
      }

      try {
        const response = await api.post(
          "/ratings/create.php",
          {
            appointment_id: currentAppointmentId.value,
            service_id: currentServiceId.value,
            provider_id: currentProviderId.value,
            user_id: userId,
            feedback: feedbackComments.value,
            star_rating: rating.value,
          },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );

        if (response.data.success) {
          showNotify(true, "You have successfully rated this service.");
          showFeedbackDialog.value = false;
        } else {
          console.error("Error submitting feedback:", response.data.error);
        }
      } catch (error) {
        console.error("An error occurred:", error);
      }
    };

    const formatDuration = (duration) => {
      if (duration >= 60) {
        const hours = duration / 60;
        return hours === 1 ? "1 Hour" : `${hours} Hours`;
      } else {
        return `${duration} Minute${duration > 1 ? "s" : ""}`;
      }
    };



    const fetchServices = () => {
      console.log("Fetching services with filter:", filter.value);
      // Implement the API logic to fetch services based on filter values
    };

    return {
      loading,
      appointments,
      errorMessage,
      dialogVisible,
      showFeedbackDialog,
      feedbackComments,
      currentAppointmentId,
      currentServiceId,
      currentProviderId,
      rating,
      currentAppointment,
      longPressTimeout,
      userId,
      userType,
      openFeedbackDialog,
      setRating,
      router,
      goToAppointmentDetails,
      startLongPress,
      cancelLongPress,
      getStatusColor,
      changeStatus,
      submitFeedback,
      formatDuration,
      showFilter,
      filter,
      applyFilter,
      applyPreset,
      fetchServices,
    };
  },
};
