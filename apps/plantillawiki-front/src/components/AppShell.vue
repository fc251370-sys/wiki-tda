<template>
  <div v-if="isLoginPage" class="login-viewport">
    <router-view />
  </div>

  <template v-else>
    <AppBar
      :name-button="pageTitle"
      @toggle-drawer="toggleDrawer"
    />

    <MenuLateral
      v-model:drawer="drawer"
      :permanent="!mobile"
      :rail="!mobile && rail"
      @navigate="closeMobileDrawer"
    />

    <v-main class="app-shell__main">
      <v-container fluid class="app-shell__content">
        <router-view />
      </v-container>
    </v-main>

    <v-footer
      v-if="showFooter"
      app
      class="app-shell__footer"
      color="surface"
      height="60"
    >
      <div class="w-100 text-center">
        &copy; 2026 Wiki TDA. Todos los derechos reservados.
      </div>
    </v-footer>
  </template>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useDisplay } from 'vuetify';
import { useRoute } from 'vue-router';
import AppBar from './AppBar.vue';
import MenuLateral from './MenuLateral.vue';

defineProps({
  showFooter: {
    type: Boolean,
    default: false,
  },
});

const route = useRoute();
const { mobile } = useDisplay();
const drawer = ref(!mobile.value);
const rail = ref(false);
const isLoginPage = computed(() => route.name === 'Login');

const pageTitle = computed(() => {
  const titles = {
    Home: 'Inicio',
    Dashboard: 'Dashboard',
    '/carrera': 'Carreras',
    '/evento': 'Eventos',
    '/proyecto': 'Proyectos',
    '/wiki': 'Wiki',
    Usuarios: 'Usuarios',
    Configuracion: 'Configuración',
    Login: 'Iniciar sesión',
  };

  return titles[route.name] ?? 'Wiki TDA';
});

const toggleDrawer = () => {
  if (mobile.value) {
    drawer.value = !drawer.value;
    return;
  }

  rail.value = !rail.value;
};

const closeMobileDrawer = () => {
  if (mobile.value) {
    drawer.value = false;
  }
};

watch(mobile, (isMobile) => {
  drawer.value = !isMobile;
  rail.value = false;
});
</script>

<style scoped>
.login-viewport {
  min-height: 100vh;
  width: 100%;
}

.app-shell__main {
  background: rgb(var(--v-theme-background));
}

.app-shell__content {
  padding: 28px;
}

.app-shell__footer {
  border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  color: rgb(var(--v-theme-on-surface-variant));
}

@media (max-width: 600px) {
  .app-shell__content {
    padding: 16px;
  }
}
</style>
