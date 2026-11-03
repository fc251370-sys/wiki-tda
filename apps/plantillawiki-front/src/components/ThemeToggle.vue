<template>
  <v-btn
    :icon="isDarkTheme ? 'mdi-weather-sunny' : 'mdi-weather-night'"
    variant="text"
    color="secondary"
    :aria-label="isDarkTheme ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro'"
    :title="isDarkTheme ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro'"
    @click="toggleTheme"
  />
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'

const theme = useTheme()
const isDarkTheme = computed(() => theme.global.name.value === 'dark')

function toggleTheme() {
  const nextTheme = isDarkTheme.value ? 'light' : 'dark'
  theme.global.name.value = nextTheme
  localStorage.setItem('theme', nextTheme)
}
</script>
