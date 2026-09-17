<template>
  <v-snackbar
    v-model="alertStore.show"
    :color="alertStore.type"
    :timeout="alertStore.timeout"
    location="top right"
  >
    {{ alertStore.message }}

    <template v-slot:actions>
      <v-btn
        color="white"
        variant="text"
        @click="alertStore.hideAlert"
      >
        Cerrar
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { useAlertStore } from '../store/useAlertStore';

const alertStore = useAlertStore();
</script>
