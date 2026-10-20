<script setup>
import GlobalAlert from './components/GlobalAlert.vue';
import GlobalLoader from './components/GlobalLoader.vue';
</script>

<template>
  <v-app>
    <GlobalAlert />
    <GlobalLoader />
    <router-view></router-view>
  </v-app>
</template>
