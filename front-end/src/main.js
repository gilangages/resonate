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

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      component: LandingPage,
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
          path: "global",
          component: DashboardGlobal,
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
  ],
});

router.beforeEach((to, from, next) => {
  // Ambil token dari localStorage
  const token = localStorage.getItem("token");

  // Cek apakah halaman yang dituju butuh login (requiresAuth)
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    // Jika butuh login tapi token tidak ada atau kosong ""
    if (!token || token === '""') {
      next("/login"); // Redirect ke login
    } else {
      next(); // Izinkan masuk
    }
  } else {
    // Jika user sudah login tapi malah mau akses halaman login/register
    if (token && token !== '""' && (to.path === "/login" || to.path === "/register")) {
      next("/dashboard"); // Tendang balik ke dashboard
    } else {
      next(); // Halaman bebas akses (seperti landing page)
    }
  }
});

createApp(App).use(router).mount("#app");
export default router;
