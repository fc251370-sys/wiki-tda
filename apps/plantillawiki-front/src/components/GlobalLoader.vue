<template>
  <v-overlay
    :model-value="loadingStore.isLoading"
    class="align-center justify-center"
    persistent
    scrim="rgba(15, 23, 42, 0.45)"
  >
    <div
      class="global-loader"
      role="status"
      aria-live="polite"
      aria-label="Cargando información"
    >
      <v-progress-circular
        indeterminate
        color="primary"
        :size="56"
        :width="5"
      />
      <span class="global-loader__text">Cargando información...</span>
    </div>
  </v-overlay>
</template>

<script setup>
import { useLoadingStore } from '../store/useLoadingStore';

const loadingStore = useLoadingStore();
</script>

<style scoped>
.global-loader {
  display: flex;
  min-width: 190px;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 28px 32px;
  border-radius: 16px;
  background: rgb(var(--v-theme-surface));
  box-shadow: 0 12px 36px rgb(15 23 42 / 18%);
}

.global-loader__text {
  color: rgb(var(--v-theme-on-surface));
  font-size: 0.95rem;
  font-weight: 500;
}
</style>
