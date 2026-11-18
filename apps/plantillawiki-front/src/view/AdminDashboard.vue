<template>
  <v-container fluid>
    <!-- Banner de bienvenida -->
    <v-card color="#1b0b3b" theme="dark" rounded="lg" class="pa-6 mb-6" elevation="2">
      <div class="d-flex align-center justify-space-between flex-wrap ga-4">
        <div>
          <v-chip size="small" color="success" variant="flat" class="mb-2">
            Panel de Control
          </v-chip>
          <h1 class="text-h5 font-weight-bold">
            Bienvenido a Wiki TDA
          </h1>
          <p class="text-body-2 text-grey-lighten-2 mt-1 mb-0">
            Selecciona uno de los módulos de abajo para comenzar a gestionar la plataforma.
          </p>
        </div>
        <v-icon size="64" class="opacity-50 d-none d-sm-flex">
          mdi-view-dashboard-outline
        </v-icon>
      </div>
    </v-card>

    <!-- Título de sección -->
    <div class="text-subtitle-1 font-weight-bold mb-3" style="color: #1b0b3b;">
      Módulos del sistema
    </div>

    <!-- Cuadrícula de accesos rápidos (sin peticiones a la API) -->
    <v-row>
      <v-col
        v-for="modulo in modulos"
        :key="modulo.titulo"
        cols="12"
        sm="6"
        md="4"
      >
        <v-card
          elevation="2"
          rounded="lg"
          class="pa-4 h-100 d-flex flex-column justify-space-between"
          link
          @click="$router.push(modulo.ruta)"
        >
          <div>
            <div class="d-flex align-center mb-3">
              <v-avatar color="#1b0b3b" variant="tonal" size="44" class="me-3">
                <v-icon color="#1b0b3b">{{ modulo.icono }}</v-icon>
              </v-avatar>
              <h2 class="text-h6 font-weight-bold" style="color: #1b0b3b;">
                {{ modulo.titulo }}
              </h2>
            </div>

            <p class="text-body-2 text-medium-emphasis">
              {{ modulo.descripcion }}
            </p>
          </div>

          <div class="d-flex justify-end mt-4">
            <v-btn
              variant="text"
              color="#1b0b3b"
              append-icon="mdi-arrow-right"
              size="small"
            >
              Ir al módulo
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
// Lista estática de accesos directos: cero errores de API o imports
const modulos = [
  {
    titulo: 'Artículos Wiki',
    descripcion: 'Todos los artículos publicados solo para ti',
    icono: 'mdi-book-open-page-variant',
    ruta: '/wiki'
  },
  {
    titulo: 'Eventos',
    descripcion: 'Eventos y actividades de la institución, con información detallada y actualizada.',
    icono: 'mdi-calendar-star',
    ruta: '/evento'
  },
  {
    titulo: 'Proyectos',
    descripcion: 'Revisa los proyectos realizados por los estudiantes.',
    icono: 'mdi-folder-multiple',
    ruta: '/proyecto'
  },
  {
    titulo: 'Carreras',
    descripcion: 'Mira las carreras disponibles en la institución.',
    icono: 'mdi-school',
    ruta: '/carrera'
  }
]
</script>
