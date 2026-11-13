<template>
  <div>
    <!-- Título de la página -->
    <title-component :title="'Wiki'" />

    <!-- Fila de cards -->
    <v-row>
      <!-- Card Categorías -->
      <v-col cols="12" md="4">
        <v-card link @click="irA('categoria')">
          <v-card-title>Categorías</v-card-title>
          <v-card-text>Administrar las categorías de la wiki</v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="text" @click.stop="irA('categoria')">Entrar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Card Tags -->
      <v-col cols="12" md="4">
        <v-card link @click="irA('tag')">
          <v-card-title>Tags</v-card-title>
          <v-card-text>Administrar las etiquetas de la wiki</v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="text" @click.stop="irA('tag')">Entrar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Card Artículos -->
      <v-col cols="12" md="4">
        <v-card link @click="irA('articulo')">
          <v-card-title>Artículos</v-card-title>
          <v-card-text>Administrar los artículos de la wiki</v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="text" @click.stop="irA('articulo')">Entrar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
// Importa el router
import { useRouter } from 'vue-router'
// Componente de título
import TitleComponent from '../../components/TitleComponent.vue'

// Instancia del router
const router = useRouter()

// Navega a la ruta hija (que vive bajo /admin/wiki)
const irA = (entidad) => router.push(`/admin/wiki/${entidad}`)
</script>