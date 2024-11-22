import state from "src/store/module-example/state";
import { api } from "../../../boot/axios";

export default {
  props: {
    serviceId: {
      type: Number,
      default: null,
    },
  },

  data() {
    return {
      service: {
        name: "",
        description: "",
        duration: "",
        price: "",
        startTime: "",
        endTime: "",
        availability: [], // Selected days
        media: null,
      },
      days: ["Su", "M", "T", "W", "TH", "F", "S"],
      hoveredDays: [],
      clockModalVisible: false,
      selectedTimeType: null,
      timePicker: ["", ""],
      isEditMode: false,
      imagePreviewUrl: this.existingMedia || null,
      submitted: false,
    };
  },

  mounted() {
    if (this.serviceId) {
      this.isEditMode = true;
      this.fetchServiceDetails(this.serviceId);
    }
  },
  methods: {
    updateImage(newImagePath) {
      this.service.media = newImagePath;
    },

    getImageSrc() {
      if (this.service.media instanceof File) {
        return this.imagePreview;
      }

      if (
        typeof this.service.media === "string" &&
        !this.service.media.startsWith("http")
      ) {
        return `${api.defaults.baseURL}/services/uploads/${this.service.media}`;
      }
      return this.service.media || null;
    },
    removeMedia() {
      this.service.media = null;
    },
    openClockModal(timeType) {
      this.selectedTimeType = timeType;
      this.timePicker[timeType === "start" ? 0 : 1] =
        this.service[`${timeType}Time`] || "";
      this.clockModalVisible = true;
    },

    saveTimeSelection() {
      const selectedTimeIndex = this.selectedTimeType === "start" ? 0 : 1;
      const selectedTime = this.timePicker[selectedTimeIndex];

      // Debugging: Log the selected time
      console.log(
        "Selected Time for",
        this.selectedTimeType,
        ":",
        selectedTime
      );

      // Check if the selected time is valid
      if (selectedTime && /^\d{1,2}:\d{2} [AP]M$/.test(selectedTime)) {
        this.service[this.selectedTimeType + "Time"] = selectedTime;
        console.log(
          "Saving Time Selection:",
          this.selectedTimeType,
          selectedTime
        );
        this.clockModalVisible = false; // Close the modal only if valid
      } else {
        alert("Please select a valid time."); // Alert for invalid time
      }
    },

    cancelTimeSelection() {
      this.clockModalVisible = false;
    },

    toggleAvailability(day) {
      const index = this.service.availability.indexOf(day);

      if (index > -1) {
        this.service.availability.splice(index, 1);
      } else {
        this.service.availability.push(day);
      }
      console.log(this.service.availability);
    },

    showNotify(status, action, message) {
      this.$q.notify({
        position: "top",
        classes: status
          ? "q-notification success-notif"
          : "q-notification error-notif",
        html: true,
        message: status
          ? `<div class="text-bold">${action} Successful!</div> ${message}`
          : `<div class="text-bold">Error!</div> ${message}`,
      });
    },

    async fetchServiceDetails(serviceId) {
      try {
        const response = await api.get(
          `/services/read_single.php?service_id=${serviceId}`
        );
        const serviceData = response.data;

        // Log the response to debug
        console.log("Service data fetched:", serviceData);

        // Assign values to the form fields
        this.service = {
          ...this.service,
          name: serviceData.name,
          description: serviceData.description,
          duration: serviceData.duration,
          price: serviceData.price,
          startTime: serviceData.start_time,
          endTime: serviceData.end_time,
          availability: serviceData.availability
            ? serviceData.availability.split(", ")
            : [],
        };
        // Check if media data exists and assign it
        if (serviceData.media) {
          this.service.media = serviceData.media;
          this.imagePreviewUrl = serviceData.media;
        }

        // Set the time picker values
        this.timePicker[0] = serviceData.start_time;
        this.timePicker[1] = serviceData.end_time;
      } catch (error) {
        console.error("Error fetching service details:", error);
      }
    },

    triggerFileUpload() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.service.media = file;
        this.imagePreview = URL.createObjectURL(file);
        this.uploadImage(file).then((newImageName) => {
          this.$set(this.service, "media", newImageName);
        });
      } else {
        this.service.media = null;
      }
    },

    validateForm() {
      return (
        this.service.name &&
        this.service.description &&
        this.service.duration &&
        this.service.price &&
        this.service.startTime &&
        this.service.endTime &&
        this.service.media
      );
    },
    async submitForm() {
      this.submitted = true;
      if (!this.validateForm()) {
        return;
      }
      if (!this.service.media) {
        return;
      }

      const formData = new FormData();
      formData.append("name", this.service.name);
      formData.append("description", this.service.description);
      formData.append("duration", this.service.duration);
      formData.append("price", this.service.price);
      formData.append("startTime", this.service.startTime);
      formData.append("endTime", this.service.endTime);

      const availabilityString = this.service.availability.join(", ");
      formData.append("availability", availabilityString);

      const provider_id = sessionStorage.getItem("provider_id");
      if (!provider_id) {
        console.error("No provider_id found in sessionStorage.");
        return;
      }
      formData.append("provider_id", provider_id);

      if (this.service.media && this.service.media instanceof File) {
        formData.append("media", this.service.media);
      }
      console.log("Form Data being submitted:", Array.from(formData.entries()));
      try {
        let response;
        if (this.isEditMode) {
          response = await api.post(
            `/services/update.php?service_id=${this.serviceId}`,
            formData,
            { headers: { "Content-Type": "multipart/form-data" } }
          );

          // Show success notification for update
          this.showNotify(
            true,
            "Updated",
            "This service has been successfully updated."
          );
        } else {
          response = await api.post("/services/create.php", formData, {
            headers: { "Content-Type": "multipart/form-data" },
          });

          // Show success notification for add
          this.showNotify(
            true,
            "Added",
            "This service has been successfully added."
          );
        }

        console.log("Service saved:", response.data);
        console.log("Price:", this.service.price);
        console.log("Duration:", this.service.duration);

        this.$emit("service-submitted");
        this.resetForm(); // Reset the form here
      } catch (error) {
        console.error("Error saving service:", error);
        this.showNotify(
          false,
          "Error",
          "Failed to save the service. Please try again."
        );
      }
    },

    resetForm() {
      this.service = {
        name: "",
        description: "",
        duration: "",
        price: "",
        startTime: "",
        endTime: "",
        availability: [],
        media: null,
      };
      this.timePicker = ["", ""];
      this.imagePreviewUrl = null;
      this.submitted = false;
    },
  },
};
