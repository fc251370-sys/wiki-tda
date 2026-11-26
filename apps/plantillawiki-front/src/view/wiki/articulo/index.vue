<template>
  <div>
    <title-component :title="'Listado de artículos'"></title-component>

    <!-- botón volver -->
    <v-btn prepend-icon="mdi-arrow-left" variant="text" @click="$router.push('/wiki')">
      Volver
    </v-btn>

    <!-- buscador -->
    <v-row class="d-flex justify-end">
      <v-col cols="12" md="4">
        <v-text-field
          v-model="search"
          variant="outlined"
          clearable
          density="compact"
          placeholder="Buscar"
          rounded-xl
          append-inner-icon="mdi-magnify"
        ></v-text-field>
      </v-col>
    </v-row>

    <!-- botón nuevo artículo + filtro de estado -->
    <v-row>
      <v-col>
        <v-btn
          v-tooltip:bottom="'Nuevo Artículo'"
          color="primary"
          icon="mdi-plus"
          @click="abrirCrear"
        />
      </v-col>
      <v-col cols="auto">
        <v-btn-toggle v-model="estadoFiltro" mandatory color="primary" density="compact" divided>
          <v-btn value="todas">Todas</v-btn>
          <v-btn value="activas">Activas</v-btn>
          <v-btn value="inactivas">Inactivas</v-btn>
        </v-btn-toggle>
      </v-col>
    </v-row>

    <!-- tabla de artículos -->
    <data-table-component
      :headers="headers"
      :items="articulos"
      :totalPage="pagination?.last_page"
      :page="pagination?.current_page"
      @update:page="changePage"
    >
      <!-- estado -->
      <template v-slot:item.estado="{ item }">
        <v-chip :color="item.estado ? 'success' : 'error'" size="small">
          {{ item.estado ? 'Activo' : 'Inactivo' }}
        </v-chip>
      </template>

      <!-- publicado -->
      <template v-slot:item.is_published="{ item }">
        <v-btn
          size="small"
          rounded
          :color="item.is_published ? 'primary' : 'grey'"
          variant="flat"
          @click="cambiarPublicado(item)"
        >
          {{ item.is_published ? 'Publicado' : 'Despublicado' }}
        </v-btn>
      </template>

      <!-- acciones -->
      <template v-slot:item.acciones="{ item }">
        <v-row>
          <v-col cols="12" col-md="4" col-sm="3">
            <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verArticulo(item)"></v-btn>

            <v-btn
              v-if="item.estado"
              v-tooltip:bottom="'Editar'"
              class="ml-2"
              density="compact"
              icon="mdi-pencil"
              @click="abrirEditar(item)"
            ></v-btn>

            <v-btn
              v-tooltip:bottom="item.estado ? 'Desactivar' : 'Activar'"
              class="ml-2"
              density="compact"
              :icon="item.estado ? 'mdi mdi-cancel' : 'mdi mdi-check-circle'"
              :color="item.estado ? 'error' : 'success'"
              @click="cambiarEstado(item)"
            ></v-btn>
          </v-col>
        </v-row>
      </template>
    </data-table-component>

    <!-- diálogo ver artículo -->
    <v-dialog v-model="dialogVer" max-width="650" persistent>
      <v-card>
        <v-card-title>{{ articulo.title }}</v-card-title>
        <v-card-text>
          <div><strong>Slug:</strong> {{ articulo.slug }}</div>
          <div><strong>Contenido:</strong> {{ articulo.content }}</div>
          <div><strong>Publicado:</strong> {{ articulo.is_published ? 'Sí' : 'No' }}</div>
          <div><strong>Categoría:</strong> {{ articulo.category?.name }}</div>
          <div><strong>Autor:</strong> {{ articulo.author?.name }}</div>
          <div class="mt-2">
            <strong>Tags:</strong>
            <v-chip v-for="tag in articulo.tags" :key="tag.id" size="small" class="ml-1">
              {{ tag.name }}
            </v-chip>
          </div>
          <div class="flex gap-4 justify-center mt-4">
            <v-btn @click="dialogVer = false" color="red" variant="outlined">Cerrar</v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- formulario crear / editar -->
    <v-dialog v-model="dialogForm" max-width="650" persistent>
      <v-card>
        <v-card-title>{{ esEdicion ? 'Editar artículo' : 'Crear artículo' }}</v-card-title>
        <v-card-text>
          <v-form ref="formArticulo">
            <v-row>
              <v-col cols="12" md="6" sm="12">
                <v-text-field
                  v-model="form.title"
                  label="Título"
                  :rules="[
                    v => !!v?.trim() || 'El título es obligatorio',
                    v => v?.trim().length >= 3 || 'Mínimo 3 caracteres'
                  ]"
                />
              </v-col>
              <v-col cols="12" md="6" sm="12">
                <v-select
                  v-model="form.category_id"
                  :items="categorias"
                  item-title="name"
                  item-value="id"
                  label="Categoría"
                  :rules="[v => !!v || 'La categoría es obligatoria']"
                />
              </v-col>
              <v-col cols="12">
                <v-textarea v-model="form.content" label="Contenido" rows="4" />
              </v-col>
              <v-col cols="12" md="6" sm="12">
                <v-select
                  v-model="form.tags"
                  :items="tags"
                  item-title="name"
                  item-value="id"
                  label="Tags"
                  multiple
                  chips
                  clearable
                />
              </v-col>
              <v-col cols="12" md="6" sm="12">
                <v-switch v-model="form.is_published" label="Publicado" color="primary" />
              </v-col>
            </v-row>
          </v-form>
          <div class="flex gap-4 justify-center">
            <v-btn @click="dialogForm = false" color="red" variant="outlined">Cancelar</v-btn>
            <v-btn color="secondary" :loading="guardando" @click="guardar">Guardar</v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import TitleComponent from '../../../components/TitleComponent.vue'
import DataTableComponent from '../../../components/DataTableComponent.vue'
import { useArticulos } from './composable/articuloComposable.js'
import { useCategorias } from '../../wikiconf/categoria/composable/categoriaComposable.js'
import { useTags } from '../../wikiconf/tag/composable/tagComposable.js'

// Composable artículos
const {
  articulos, pagination,
  obtenerArticulos, actualizarArticulos, eliminarArticulos, crearArticulos
} = useArticulos()

// Composables para los selects
const { categorias, obtenerCategorias } = useCategorias()
const { tags, obtenerTags } = useTags()

// Headers
const headers = ref([
  { title: 'Título', key: 'title' },
  { title: 'Categoría', key: 'category.name' },
  { title: 'Autor', key: 'author.name' },
  { title: 'Publicado', key: 'is_published' },
  { title: 'Estado', key: 'estado' },
  { title: 'Acciones', key: 'acciones' },
])

// Buscador y filtro
const search = ref('')
const estadoFiltro = ref('todas')
let searchTimeout

const filtros = () => ({
  title: search.value?.trim() || undefined,
  estado: estadoFiltro.value === 'todas' ? undefined : estadoFiltro.value === 'activas'
})

// Estado de diálogos
const dialogVer = ref(false)
const articulo = ref({})

const dialogForm = ref(false)
const esEdicion = ref(false)
const formArticulo = ref(null)
const guardando = ref(false)

const formVacio = () => ({ id: null, title: '', content: '', category_id: null, tags: [], is_published: false })
const form = ref(formVacio())

// Recarga conservando la página actual (o yendo a la indicada)
const recargar = async (page) => {
  await obtenerArticulos(page ?? pagination.value?.current_page ?? 1, filtros())
}

const changePage = async (page) => {
  await obtenerArticulos(page, filtros())
}

watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    obtenerArticulos(1, filtros())
  }, 300)
})

watch(estadoFiltro, () => obtenerArticulos(1, filtros()))

// Funciones
const verArticulo = (item) => {
  articulo.value = item
  dialogVer.value = true
}

const abrirCrear = () => {
  esEdicion.value = false
  form.value = formVacio()
  dialogForm.value = true
}

const abrirEditar = (item) => {
  esEdicion.value = true
  form.value = {
    id: item.id,
    title: item.title,
    content: item.content,
    category_id: item.category?.id ?? null,
    tags: item.tags?.map(t => t.id) || [],
    is_published: item.is_published
  }
  dialogForm.value = true
}

const guardar = async () => {
  const { valid } = await formArticulo.value.validate()
  if (!valid) return

  guardando.value = true
  const datos = {
    title: form.value.title.trim(),
    content: form.value.content,
    category_id: form.value.category_id,
    tags: form.value.tags,
    is_published: form.value.is_published
  }

  const ok = esEdicion.value
    ? await actualizarArticulos(form.value.id, datos)
    : await crearArticulos(datos)
  guardando.value = false

  if (ok) {
    dialogForm.value = false
    await recargar(esEdicion.value ? undefined : 1)
  }
}

const cambiarEstado = async (item) => {
  const ok = await eliminarArticulos(item.id)
  if (ok) await recargar()
}

const cambiarPublicado = async (item) => {
  const ok = await actualizarArticulos(item.id, {
    title: item.title,
    content: item.content,
    category_id: item.category?.id,
    tags: item.tags?.map(t => t.id) || [],
    is_published: !item.is_published
  })
  if (ok) await recargar()
}

// Carga inicial: artículos + categorías + tags (solo activas) para los selects
onMounted(async () => {
  await Promise.all([
    obtenerArticulos(1, filtros()),
    obtenerCategorias(1, { estado: true }),
    obtenerTags(1, { estado: true }),
  ])
})

onBeforeUnmount(() => clearTimeout(searchTimeout))
</script>