<template>
  <main class="home-page">
    <h1>Wiki TDA</h1>
    <p>Conocimiento que conecta, tecnología que transforma.</p>
    <v-btn to="/login" color="primary" variant="flat">Iniciar sesión</v-btn>
  </main>
</template>

<script setup>
</script>

<style scoped>
.home-page {
  display: grid;
  min-height: 60vh;
  align-content: center;
  justify-items: center;
  gap: 16px;
  color: rgb(var(--v-theme-on-background));
  text-align: center;
}

.home-page p {
  color: rgb(var(--v-theme-on-surface-variant));
}
</style>
