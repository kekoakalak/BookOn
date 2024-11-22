import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api } from '../../../boot/axios';

export default {
  setup() {
    const route = useRoute();
    const router = useRouter();
    const appointmentId = route.params.appointment_id;

    const loading = ref(true);
    const appointment = ref(null);
    const service = ref(null);
    const details = ref(null);
    const errorMessage = ref(null);
    const formattedDate = ref("");

    onMounted(async () => {
      try {
        const appointmentResponse = await api.get(
          `/appointments/read_single.php?appointment_id=${appointmentId}`
        );

        if (appointmentResponse.data.appointment) {
          appointment.value = appointmentResponse.data.appointment;
          details.value = appointmentResponse.data.details;

          // Format the appointment date and time with AM/PM
          const appointmentDate = new Date(appointment.value.appointment_date);
          const dateString = appointmentDate.toLocaleDateString();

          // Extract hours and minutes
          let hours = appointmentDate.getHours();
          const minutes = appointmentDate.getMinutes().toString().padStart(2, '0');
          const period = hours >= 12 ? 'PM' : 'AM';
          hours = hours % 12 || 12; // Convert to 12-hour format and handle midnight (0 -> 12)

          const timeString = `${hours}:${minutes} ${period}`;
          formattedDate.value = `${dateString} ${timeString}`;

          // Fetch service details based on service_id from appointment
          const serviceId = appointment.value.service_id;
          const serviceResponse = await api.get(
            `/services/read_single.php?service_id=${serviceId}`
          );
          console.log("Service Response:", serviceResponse.data); // Log the response

          if (serviceResponse.data) {
            service.value = serviceResponse.data;
          } else {
            errorMessage.value = "Service not found.";
          }
        } else {
          errorMessage.value = "Appointment not found.";
        }
      } catch (error) {
        errorMessage.value = "An error occurred while fetching appointment details.";
      } finally {
        loading.value = false;
      }
    });


    const goBack = () => {
      router.back();
    };

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

    const getImageSrc = (media) => {
      return media
        ? `${api.defaults.baseURL}/services/uploads/${media}?ngrok-skip-browser-warning=true`
        : null;
    };


    return {
      loading,
      appointment,
      service,
      details,
      errorMessage,
      formattedDate,
      goBack,
      getStatusColor,
      getImageSrc,
    };
  },
};
