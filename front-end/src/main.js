import { createApp } from "vue";
import App from "./App.vue";
import "./style.css";
import { createRouter, createWebHistory } from "vue-router";
import Layout from "./components/Layout/Layout.vue";
import UserRegister from "./components/User/UserRegister.vue";
import UserLogin from "./components/User/UserLogin.vue";
import LandingPage from "./components/LandingPage/LandingPage.vue";
import DashboardLayout from "./components/Layout/DashboardLayout.vue";
import UserProfile from "./components/User/UserProfile.vue";
import UserLogout from "./components/User/UserLogout.vue";
import DashboardUser from "./components/Dashboard/DashboardUser.vue";
import DashboardGlobal from "./components/Dashboard/DashboardGlobal.vue";
import NotFound from "./components/NotFound.vue";
import AuthCallback from "./components/User/AuthCallback.vue";
import UserForgotPassword from "./components/User/UserForgotPassword.vue";
import UserResetPassword from "./components/User/UserResetPassword.vue";
import AdminDashboard from "./components/Admin/AdminDashboard.vue";
import { alertError } from "./lib/alert";
import AllNotifications from "./components/Dashboard/AllNotifications.vue";
import About from "./components/LandingPage/Section/About.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      component: LandingPage,
    },
    {
      path: "/about",
      component: About,
    },
    {
      path: "/note/:id",
      component: LandingPage,
      // props: true,
    },
    {
      path: "/",
      component: Layout,
      children: [
        {
          path: "register",
          component: UserRegister,
        },
        {
          path: "login",
          component: UserLogin,
        },
        {
          path: "forgot-password",
          component: UserForgotPassword,
        },
        {
          path: "reset-password",
          component: UserResetPassword,
        },
      ],
    },
    {
      path: "/dashboard",
      component: DashboardLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: "",
          component: DashboardUser,
        },
        {
          path: "admin",
          component: AdminDashboard,
          beforeEnter: (to, from, next) => {
            const user = JSON.parse(localStorage.getItem("user")); // Pastikan simpan data user saat login
            if (user && user.role === "admin") {
              next();
            } else {
              alertError("Hanya admin yang boleh masuk!");
              next("/dashboard/global");
            }
          },
        },
        {
          path: "global",
          component: DashboardGlobal,
        },
        {
          path: "notifications",
          component: AllNotifications,
        },
        {
          path: "users/profile",
          component: UserProfile,
        },
        {
          path: "users/logout",
          component: UserLogout,
        },
      ],
    },
    {
      path: "/:pathMatch(.*)*",
      component: NotFound,
    },
    {
      path: "/auth/callback",
      component: AuthCallback,
    },
  ],
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  // 1. Jika rute butuh auth (dashboard) tapi tidak ada token
  if (to.path.startsWith("/dashboard") && !token) {
    next("/login");
  }
  // 2. Jika rute login/register tapi user SUDAH punya token (biar ga login 2x)
  else if ((to.path === "/login" || to.path === "/register") && token) {
    next("/dashboard");
  }
  // 3. Lanjut normal
  else {
    next();
  }
});

createApp(App).use(router).mount("#app");
