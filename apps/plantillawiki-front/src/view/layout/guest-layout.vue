<template>
  <v-app>
    <v-app-bar >
      <v-toolbar-title>My App</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn text to="/">Home</v-btn>
      <v-btn text to="/login">Login</v-btn>
    </v-app-bar>

    <v-main >
      <v-container class="max-w-7xl mx-auto py-8">
        <router-view></router-view>
      </v-container>
    </v-main>

    <v-footer app class="bg-gray-200 text-center py-4 border-t border-gray-300" height="60">
      <div class="w-full">
        &copy; 2026 My App. All rights reserved.
      </div>
    </v-footer>
  </v-app>
</template>

<script setup>
// Guest layout logic here
</script>

<style scoped>
/* Tailwind classes are used in the template, so custom CSS is minimized */
</style>