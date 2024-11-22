import { route } from "quasar/wrappers";
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from "vue-router";
import routes from "./routes";

// Authentication check function
function isAuthenticated() {
  return !!sessionStorage.getItem("user_id"); // Check if user_id is present in session storage
}

export default route(function () {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === "history"
    ? createWebHistory
    : createWebHashHistory;

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  });

  // Global route guard for routes that require authentication
  Router.beforeEach((to, from, next) => {
    const authRequiredRoutes = [
      "Home",
      "Services",
      "MyAppointments",
      "UserProfile",
      "AppointmentNow",
      "FetchReviews",
      "analytics",
    ]; // Routes that need authentication
    const userType = sessionStorage.getItem("user_type");

    if (authRequiredRoutes.includes(to.name) && !isAuthenticated()) {
      next({ name: "LoginPage" }); // Redirect to login page if not authenticated
    }
    // Restrict access to Home for non-providers
    else if (to.name === "Home" && userType !== "provider") {
      next({ name: "CustomerDashboard" });
    }
    // Restrict access to CustomerDashboard for non-users
    else if (to.name === "CustomerDashboard" && userType !== "user") {
      next({ name: "Home" });
    } else {
      next(); // Allow access if authenticated and no restrictions apply
    }
  });

  return Router;
});
