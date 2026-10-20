<template>
  <v-navigation-drawer
    :model-value="drawer"
    :permanent="permanent"
    :temporary="!permanent"
    :rail="rail"
    :width="264"
    :rail-width="76"
    color="surface"
    class="sidebar"
    @update:model-value="emit('update:drawer', $event)"
  >
    <div class="sidebar__brand">
      <div class="sidebar__logo">
        <v-icon icon="mdi-book-open-page-variant" size="26" />
      </div>
      <div v-if="!rail" class="sidebar__brand-copy">
        <span class="sidebar__brand-title">Wiki TDA</span>
        <span class="sidebar__brand-caption">Conocimiento compartido</span>
      </div>
    </div>

    <v-divider />

    <SidebarMenu @navigate="emit('navigate')" />
  </v-navigation-drawer>
</template>

<script setup>
import SidebarMenu from './SidebarMenu.vue';
defineProps({
  drawer: {
    type: Boolean,
    default: true,
  },
  permanent: {
    type: Boolean,
    default: false,
  },
  rail: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['update:drawer', 'navigate']);
</script>

<style scoped>
.sidebar {
  border-right: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.sidebar__brand {
  display: flex;
  min-height: 88px;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
}

.sidebar__logo {
  display: grid;
  width: 44px;
  height: 44px;
  flex: 0 0 44px;
  place-items: center;
  border-radius: 12px;
  background: rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-on-primary));
}

.sidebar__brand-copy {
  display: flex;
  flex-direction: column;
}

.sidebar__brand-title {
  color: rgb(var(--v-theme-primary));
  font-size: 1.05rem;
  font-weight: 700;
}

.sidebar__brand-caption {
  font-size: 0.72rem;
}
</style>