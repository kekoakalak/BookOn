const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      {
        path: "/",
        name: "Home",
        component: () => import("pages/IndexPage.vue"), // Dashboard (Provider)
      },

      {
        path: "/",
        name: "CustomerDashboard",
        component: () => import("pages/CustomerDashboard.vue"), // Dashboard (User)
      },
      {
        path: "/profile",
        name: "UserProfile",
        component: () => import("pages/UserProfile.vue"), // Create this component if needed
      },
      {
        path: "services",
        component: () => import("pages/Services/ServiceManagement.vue"),
        name: "Services",
      },

      {
        path: "/reviews",
        component: () => import("pages/MyReviews.vue"),
        name: "FetchReviews",
      },

      {
        path: "/reviews/:serviceId",
        name: "AllReviews",
        component: () => import("src/pages/Services/ServiceReviews.vue"),
      },

      {
        path: "/service/:id",
        name: "ServiceDetails",
        component: () => import("pages/Services/ServiceDetails.vue"),
      },
      {
        path: "/analytics",
        name: "analytics",
        component: () => import("pages/Services/AnalyticsReport.vue"),
      },
      {
        path: "/edit-service/:id",
        name: "EditService",
        component: () => import("pages/Services/AddServiceForm.vue"),
        props: true,
        beforeEnter: (to, from, next) => {
          if (from.name !== "Home") {
            next({ name: "Home" }); // Redirect to 'Home' if coming from another page
          } else {
            next(); // Allow access if coming from 'Home'
          }
        },
      },
      {
        path: "/appointment/:serviceId",
        name: "AppointmentNow",
        component: () => import("pages/Appointment/AppointmentNow.vue"),
        props: true,
      },
      {
        path: "/appointment-confirmation",
        name: "AppointmentConfirmation",
        component: () =>
          import("pages/Appointment/AppointmentConfirmation.vue"),
      },
      {
        path: "/appointments",
        name: "MyAppointments",
        component: () => import("pages/Appointment/MyAppointments.vue"),
      },
      {
        path: "/appointment-details/:appointment_id",
        name: "AppointmentDetails",
        component: () => import("pages/Appointment/AppointmentDetails.vue"),
      },
      {
        path: '/receipt',
        name: 'ReceiptPage',
        component: () => import("pages/Appointment/ReceiptPage.vue"),
        props: route => ({ price: route.params.price }) 
      },      
    ],
  },
  {
    path: "/login",
    name: "LoginPage",
    component: () => import("pages/LoginPage.vue"),
  },
  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/ErrorNotFound.vue"),
  },
];

export default routes;
