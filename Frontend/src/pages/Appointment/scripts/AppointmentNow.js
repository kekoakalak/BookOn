import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { api } from '../../../boot/axios';


export default {
  setup() {
    const firstName = ref("");
    const lastName = ref("");
    const email = ref("");
    const mobile = ref("");
    const notes = ref("");
    const date = ref("");
    const selectedProvider = ref(null); // New state for selected provider

    const providers = ref([]); // List of providers

    const route = useRoute();
    const router = useRouter();

    const serviceId = route.params.serviceId;

    const fetchProviders = async () => {
      try {
        const response = await api.get(
          "/providers/read.php"
        );
        providers.value = response.data;
      } catch (error) {
        console.error("Error fetching providers:", error);
      }
    };

    // This method returns the full name of the provider
    const getProviderFullName = (provider) => {
      return `${provider.first_name} ${provider.last_name}`;
    };

    onMounted(() => {
      fetchProviders(); // Fetch providers when component is mounted
    });

    const submitAppointment = async () => {
      try {
        const userId = sessionStorage.getItem("user_id");
        if (!userId) {
          alert("User ID not found in session storage.");
          return;
        }

        if (!selectedProvider.value) {
          alert("Please select a provider.");
          return;
        }

        const appointmentData = {
          first_name: firstName.value,
          last_name: lastName.value,
          email: email.value,
          mobile: mobile.value,
          notes: notes.value,
          date: date.value,
          service_id: serviceId,
          user_id: userId,
          provider_id: selectedProvider.value, // Include selected provider ID
        };

        await api.post(
          "/appointments/create.php",
          appointmentData
        );
        router.push({ name: "AppointmentConfirmation" });
      } catch (error) {
        console.error("Error submitting appointment:", error);
      }
    };

    return {
      firstName,
      lastName,
      email,
      mobile,
      notes,
      date,
      selectedProvider,
      providers,
      getProviderFullName,
      submitAppointment,
    };
  },
};
