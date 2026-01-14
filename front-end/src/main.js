import { createApp } from "vue";
import App from "./App.vue";
import "./style.css";
import { createRouter, createWebHistory } from "vue-router";
import Layout from "./components/Layout/Layout.vue";
import UserRegister from "./components/User/UserRegister.vue";
import Test from "./components/Test.vue";
import UserLogin from "./components/User/UserLogin.vue";
import LandingPage from "./components/LandingPage/LandingPage.vue";
import DashboardLayout from "./components/Layout/DashboardLayout.vue";
import UserProfile from "./components/User/UserProfile.vue";
import UserLogout from "./components/User/UserLogout.vue";
import DashboardUser from "./components/Dashboard/DashboardUser.vue";
import NoteEdit from "./components/Note/NoteEdit.vue";

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
      children: [
        {
          path: "",
          component: DashboardUser, //ini nanti ke global
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

createApp(App).use(router).mount("#app");
