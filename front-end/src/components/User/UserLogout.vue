<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userLogout } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";
import { onBeforeMount } from "vue";

const token = useLocalStorage("token", "");
const router = useRouter();

async function handleLogout() {
  const response = await userLogout(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    token.value = "";
    await router.push({
      path: "/login",
    });
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

onBeforeMount(async () => {
  await handleLogout();
});
</script>
<template></template>
