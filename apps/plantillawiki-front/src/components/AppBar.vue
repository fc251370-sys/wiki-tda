<template>
  <v-app-bar color="surface" elevation="0" class="app-bar">
    <v-app-bar-nav-icon
      color="primary"
      aria-label="Alternar menú"
      @click="emit('toggle-drawer')"
    >
      <v-icon icon="mdi-menu" />
    </v-app-bar-nav-icon>

    <v-breadcrumbs :items="breadcrumbs" class="app-bar__breadcrumbs">
      <template #divider>
        <v-icon icon="mdi-chevron-right" size="small" />
      </template>
      <template #item="{ item }">
        <v-breadcrumbs-item
          :to="item.to"
          :disabled="item.disabled"
          :class="{ 'app-bar__current-page': item.disabled }"
        >
          {{ item.title }}
        </v-breadcrumbs-item>
      </template>
    </v-breadcrumbs>

    <v-spacer />

    <v-chip
      variant="outlined"
      color="secondary"
      prepend-icon="mdi-calendar"
      class="app-bar__organization"
    >
      FESOL 2026
    </v-chip>

    <ThemeToggle />

    <v-btn
      icon="mdi-bell-outline"
      variant="text"
      color="secondary"
      aria-label="Notificaciones"
      disabled
      class="app-bar__notifications"
    />

    <v-menu v-if="isAuthenticated" location="bottom end">
      <template #activator="{ props: menuProps }">
        <v-btn
          v-bind="menuProps"
          icon
          variant="text"
          aria-label="Abrir menú de usuario"
          class="app-bar__profile-button"
        >
          <v-avatar color="secondary" size="38">
            <span class="app-bar__initials">{{ userInitials }}</span>
          </v-avatar>
        </v-btn>
      </template>

      <v-list min-width="220" class="app-bar__profile-menu">
        <v-list-item
          :title="userName"
          :subtitle="userEmail"
          prepend-icon="mdi-account-circle-outline"
        />
        <v-divider />
        <v-list-item
          title="Cerrar sesión"
          prepend-icon="mdi-logout"
          :disabled="isLoggingOut"
          @click="cerrarSesion"
        />
      </v-list>
    </v-menu>

    <v-btn
      v-else
      to="/login"
      color="primary"
      variant="flat"
      prepend-icon="mdi-login"
      class="app-bar__action"
    >
      Iniciar sesión
    </v-btn>
  </v-app-bar>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useLoginStore } from '../store/auth/useLoginStore';
import { useAlertStore } from '../store/useAlertStore';
import ThemeToggle from './ThemeToggle.vue';

const router = useRouter();
const authStore = useLoginStore();
const alertStore = useAlertStore();

const props = defineProps({
  nameButton: {
    default: 'Inicio',
    type: String,
  },
});
const emit = defineEmits(['toggle-drawer']);
const isAuthenticated = ref(Boolean(localStorage.getItem('token')));
const user = ref(readStoredUser());
const isLoggingOut = ref(false);

const breadcrumbs = computed(() => {
  if (props.nameButton === 'Inicio') {
    return [{ title: 'Inicio', disabled: true }];
  }

  return [
    { title: 'Inicio', to: '/' },
    { title: props.nameButton, disabled: true },
  ];
});

const userName = computed(() => user.value?.name || 'Usuario');
const userEmail = computed(() => user.value?.email || '');
const userInitials = computed(() => {
  const initials = userName.value
    .trim()
    .split(/\s+/)
    .slice(0, 2)
    .map((part) => part[0])
    .join('');

  return initials.toUpperCase() || 'U';
});

function readStoredUser() {
  const storedUser = localStorage.getItem('user');

  if (!storedUser) {
    return null;
  }

  try {
    return JSON.parse(storedUser);
  } catch (error) {
    console.error('No se pudo leer el usuario de la sesión.', error);
    return null;
  }
}

const cerrarSesion = async () => {
  if (isLoggingOut.value) {
    return;
  }

  isLoggingOut.value = true;
  try {
    await authStore.logout();
  } catch (error) {
    console.error('No se pudo invalidar la sesión en el servidor.', error);
    alertStore.showAlert(
      'La sesión se cerró en este dispositivo, pero no se pudo confirmar con el servidor.',
      'warning'
    );
  } finally {
    isAuthenticated.value = false;
    user.value = null;
    isLoggingOut.value = false;
    await router.push('/');
  }
};
</script>

<style scoped>
.app-bar {
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  color: rgb(var(--v-theme-on-surface));
}

.app-bar__breadcrumbs {
  padding: 0 8px;
}

.app-bar__breadcrumbs :deep(.v-breadcrumbs-item) {
  color: rgb(var(--v-theme-secondary));
}

.app-bar__breadcrumbs :deep(.app-bar__current-page) {
  color: rgb(var(--v-theme-on-surface));
  font-weight: 600;
}

.app-bar__organization {
  margin-inline-end: 12px;
  font-weight: 500;
}

.app-bar__notifications {
  margin-inline-end: 4px;
}

.app-bar__profile-button {
  margin-inline: 4px 20px;
}

.app-bar__initials {
  color: rgb(var(--v-theme-on-secondary));
  font-size: 0.85rem;
  font-weight: 600;
}

.app-bar__profile-menu {
  margin-top: 8px;
}

@media (max-width: 600px) {
  .app-bar__organization,
  .app-bar__notifications {
    display: none;
  }

  .app-bar__breadcrumbs {
    padding-inline: 0;
  }

  .app-bar__action {
    margin-inline: 4px 12px;
  }

  .app-bar__profile-button {
    margin-inline: 0 8px;
  }
}
</style>
