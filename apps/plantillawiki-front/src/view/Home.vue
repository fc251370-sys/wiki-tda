<template>
  <main class="home-page">
    <h1>Wiki TDA</h1>
    <p>Conocimiento que conecta, tecnología que transforma.</p>
    <v-btn to="/login" color="primary" variant="flat">Iniciar sesión</v-btn>
  </main>


  <v-container>
    <title-component :title="'Articulos de la ULS'" />
    <v-row v-if="puedeGestionar" class="mb-6">
      <v-col cols="12" md="4">
        <v-card link @click="irA('articulo')">
          <v-card-title>Artículos</v-card-title>
          <v-card-text>Administrar los artículos de la wiki</v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="text" @click.stop="irA('articulo')">
              Entrar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <v-divider v-if="puedeGestionar" class="mb-6"></v-divider>

    <div v-if="cargando" class="text-center my-8">
      <v-progress-circular indeterminate color="#1b0b3b"></v-progress-circular>
    </div>
    <v-alert
      v-else-if="articulosVisibles.length === 0"
      type="info"
      variant="tonal"
    >
      No hay artículos publicados disponibles en este momento.
    </v-alert>
    <v-row v-else>
      <v-col
        v-for="item in articulosVisibles"
        :key="item.id"
        cols="12"
        sm="6"
        md="4"
      >
        <v-hover v-slot="{ isHovering, props }">
          <v-card
            class="mx-auto h-100 d-flex flex-column"
            max-width="344"
            v-bind="props"
            elevation="2"
          >
            <v-sheet
              color="#1b0b3b"
              class="pa-4 d-flex justify-space-between align-center"
            >
              <v-chip
                size="small"
                color="white"
                variant="flat"
                class="font-weight-bold"
                style="color: #1b0b3b;"
              >
                {{ item.category?.name || 'Sin categoría' }}
              </v-chip>

              <v-chip
                size="small"
                :color="item.estado ? 'success' : 'error'"
                variant="flat"
              >
                {{ item.estado ? 'Activo' : 'Inactivo' }}
              </v-chip>
            </v-sheet>

            <v-card-text class="flex-grow-1 pt-4">
              <h2 class="text-h6 font-weight-bold my-0" style="color: #1b0b3b;">
                {{ item.title }}
              </h2>
              <div class="text-caption text-medium-emphasis mb-3">
                /{{ item.slug }}
              </div>
              <p class="text-body-2 mb-0">
                {{ item.content?.length > 120 ? item.content.slice(0, 120) + '...' : item.content }}
              </p>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-title class="d-flex align-center py-2">
              <v-icon size="small" color="#1b0b3b" class="me-2">
                mdi-account-circle
              </v-icon>
              <span class="text-body-2 font-weight-medium">
                {{ item.author?.name || 'Admin' }}
              </span>
            </v-card-title>
            <!--efecto-->
            <v-overlay
              :model-value="!!isHovering"
              class="align-center justify-center"
              scrim="#1b0b3b"
              contained
            >
              <v-btn
                variant="flat"
                color="white"
                @click="abrirArticulo(item)"
              >
                Leer artículo
              </v-btn>
            </v-overlay>
          </v-card>
        </v-hover>
      </v-col>
    </v-row>

    <v-dialog v-model="dialogVer" max-width="650">
      <v-card v-if="articuloSeleccionado" rounded="lg">
        <v-sheet color="#1b0b3b" class="pa-4 d-flex justify-space-between align-center">
          <v-chip size="small" color="white" variant="flat" style="color: #1b0b3b;">
            {{ articuloSeleccionado.category?.name }}
          </v-chip>
          <span class="text-caption text-white">
            Por: {{ articuloSeleccionado.author?.name }}
          </span>
        </v-sheet>

        <v-card-title class="text-h5 pt-4" style="color: #1b0b3b;">
          {{ articuloSeleccionado.title }}
        </v-card-title>
        <v-card-subtitle>/{{ articuloSeleccionado.slug }}</v-card-subtitle>

        <v-card-text class="pt-4 text-body-1" style="white-space: pre-line;">
          {{ articuloSeleccionado.content }}
        </v-card-text>

        <v-card-actions class="justify-end pa-4">
          <v-btn variant="outlined" color="error" @click="dialogVer = false">
            Cerrar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>

import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import TitleComponent from '../components/TitleComponent.vue'
import { canManageWiki } from '../utils/authSession.js'

import { articulosPublicos } from '../services/wiki/articulo.service.js'

const articulos = ref([])

const puedeGestionar = canManageWiki()


const router = useRouter()



const cargando = ref(true)
const dialogVer = ref(false)
const articuloSeleccionado = ref(null)



onMounted(async () => {
  try {
    cargando.value = true
    const res = await articulosPublicos()
    articulos.value = res.data
  } catch (error){
    console.error(error)
  }
  finally {
    cargando.value = false
  }
})

// Filtra para mostrar publicados y activpos
const articulosVisibles = computed(() => {
  if (!Array.isArray(articulos.value)) return []
  return articulos.value.filter(
    (item) => item.is_published && item.estado
  )
})

const irA = (entidad) => router.push(`/wiki/${entidad}`)

const abrirArticulo = (item) => {
  articuloSeleccionado.value = item
  dialogVer.value = true
}

</script>

<style scoped>
.home-page {
  display: grid;
  min-height: 20vh;
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
