import { boot } from "quasar/wrappers";
import axios from "axios";

// Set default header globally (as a fallback)
axios.defaults.headers.common["ngrok-skip-browser-warning"] = "true";

// Create an Axios instance with your ngrok URL
const api = axios.create({
  // baseURL: "http://modest-voice-40913.pktriot.net/BookOn/bookon-backend/api",

  baseURL: "http://localhost/BookOn/bookon-backend/api",


});


// Add an interceptor to include the ngrok-skip-browser-warning header
api.interceptors.request.use((config) => {
  // Ensure all requests include this header
  if (!config.headers["ngrok-skip-browser-warning"]) {
    config.headers["ngrok-skip-browser-warning"] = "true"; // Avoid duplication
  }
  return config;
});

export default boot(({ app }) => {
  // Make axios and api instance available globally in the app
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = api;
});

export { api };
