import { createApp } from "vue";
import App from "./App.vue";
import "./style.css";
import { createRouter, createWebHistory } from "vue-router";
import Layout from "./components/Layout/Layout.vue";
import UserRegister from "./components/User/UserRegister.vue";
import Test from "./components/Test.vue";
import UserLogin from "./components/User/UserLogin.vue";
import LandingPage from "./components/LandingPage/LandingPage.vue";
import LayoutDashboard from "./components/Layout/DashboardLayout.vue";
import UserProfile from "./components/User/UserProfile.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/test",
      component: Test,
    },
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
      component: LayoutDashboard,
      children: [
        {
          path: "users/profile",
          component: UserProfile,
        },
      ],
    },
  ],
});

createApp(App).use(router).mount("#app");
