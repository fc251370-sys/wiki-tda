<template>
  <div>
    <!-- Botón para volver a las cards de wiki -->
    <v-btn prepend-icon="mdi-arrow-left" variant="text" @click="$router.push('/admin/wiki')">
    Volver
    </v-btn>

    <!-- Buscador de artículos -->
    <v-row class="d-flex justify-end">
      <v-col cols="12" md="4">
        <v-text-field
          v-model="search"
          variant="outlined"
          clearable
          density="compact"
          rounded-xl
          append-inner-icon="mdi-magnify"
        />
      </v-col>
    </v-row>

    <!-- Botón nuevo + filtro de estado -->
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

    <!-- Tabla de artículos -->
    <data-table-component
      :key="tablaKey"
      :totalPage="pagination?.last_page"
      :page="pagination?.current_page"
      :headers="headers"
      :items="articulos"
      @update:page="paginationMethod"
    >
      <!-- Chip de estado -->
      <template v-slot:item.estado="{ item }">
        <v-chip :color="item.estado ? 'success' : 'error'">
          {{ item.estado ? 'Activo' : 'Inactivo' }}
        </v-chip>
      </template>

      <!-- Chip de publicado -->
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

      <!-- Botones de acción -->
      <template v-slot:item.acciones="{ item }">
        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verArticulo(item)" />
        <v-btn
          v-tooltip:bottom="'Editar'"
          class="ml-2"
          density="compact"
          icon="mdi-pencil"
          v-if="item.estado"
          @click="ActualizarArticulo(item)"
        />
        <v-btn
          v-tooltip:bottom="item.estado ? 'Desactivar' : 'Activar'"
          class="ml-2"
          density="compact"
          :icon="item.estado ? 'mdi-cancel' : 'mdi-check-circle'"
          :color="item.estado ? 'error' : 'success'"
          @click="cambiarEstado(item)"
        />
      </template>
    </data-table-component>

    <!-- Diálogo ver artículo -->
    <v-dialog v-model="dialogVerArticulo" persistent>
      <v-card class="mx-auto my-8" elevation="4" width="600" rounded="lg">
        <v-card-item>
          <v-card-title>{{ articulo.title }}</v-card-title>
        </v-card-item>
        <v-card-text>
          <div><strong>Slug:</strong> {{ articulo.slug }}</div>
          <div><strong>Contenido:</strong> {{ articulo.content }}</div>
          <div><strong>Publicado:</strong> {{ articulo.is_published ? 'Sí' : 'No' }}</div>
          <div><strong>Categoría:</strong> {{ articulo.category?.name }}</div>
          <div><strong>Autor:</strong> {{ articulo.author?.name }}</div>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" class="text-center">
              <v-btn rounded="lg" variant="outlined" color="error" text @click="dialogVerArticulo = false">
                Cerrar
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo editar artículo -->
    <v-dialog v-model="dialogActualizarArticulo" max-width="600" persistent>
      <v-card>
        <v-card-title>Editar artículo</v-card-title>
        <v-card-text>
          <v-form ref="formEditar">
            <v-text-field
              v-model="form.title"
              label="Título"
              :rules="[v => !!v?.trim() || 'El título es obligatorio']"
            />
            <v-textarea v-model="form.content" label="Contenido" rows="4" />
            <v-select
              v-model="form.category_id"
              :items="categorias"
              item-title="name"
              item-value="id"
              label="Categoría"
              :rules="[v => !!v || 'La categoría es obligatoria']"
            />
            <v-select
              v-model="form.tags"
              :items="tags"
              item-title="name"
              item-value="id"
              label="Tags"
              multiple
              chips
            />
            <v-switch v-model="form.is_published" label="Publicado" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogActualizarArticulo = false">Cancelar</v-btn>
          <v-btn color="primary" :loading="guardando" @click="guardarEdicion">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo crear artículo -->
    <v-dialog v-model="dialogCrearArticulo" max-width="600" persistent>
      <v-card>
        <v-card-title>Crear artículo</v-card-title>
        <v-card-text>
          <v-form ref="formNueva">
            <v-text-field
              v-model="nuevoArticulo.title"
              label="Título"
              :rules="[
                v => !!v?.trim() || 'El título es obligatorio',
                v => v?.trim().length >= 3 || 'Mínimo 3 caracteres'
              ]"
            />
            <v-textarea v-model="nuevoArticulo.content" label="Contenido" rows="4" />
            <v-select
              v-model="nuevoArticulo.category_id"
              :items="categorias"
              item-title="name"
              item-value="id"
              label="Categoría"
              :rules="[v => !!v || 'La categoría es obligatoria']"
            />
            <v-select
              v-model="nuevoArticulo.tags"
              :items="tags"
              item-title="name"
              item-value="id"
              label="Tags"
              multiple
              chips
            />
            <v-switch v-model="nuevoArticulo.is_published" label="Publicado" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogCrearArticulo = false">Cancelar</v-btn>
          <v-btn color="primary" :loading="creando" @click="guardarNueva">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
// Hooks de Vue
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
// Tabla reutilizable
import DataTableComponent from '../../../components/DataTableComponent.vue'
// Composable de artículos
import { useArticulos } from './composable/articuloComposable.js'
// Composable de categorías (para el select)
import { useCategorias } from '../categoria/composable/categoriaComposable.js'
// Composable de tags (para el select)
import { useTags } from '../tag/composable/tagComposable.js'

// Crear
const dialogCrearArticulo = ref(false)
const nuevoArticulo = ref({ title: '', content: '', category_id: null, tags: [], is_published: false })
const formNueva = ref(null)
const creando = ref(false)

// Ver
const dialogVerArticulo = ref(false)
const articulo = ref(null)

// Editar
const dialogActualizarArticulo = ref(false)
const form = ref({ id: null, title: '', content: '', category_id: null, tags: [], is_published: false })
const formEditar = ref(null)
const guardando = ref(false)

// Buscador
const search = ref('')
let searchTimeout

// Filtro
const estadoFiltro = ref('todas')
const filtros = () => ({
  title: search.value?.trim() || undefined,
  estado: estadoFiltro.value === 'todas' ? undefined : estadoFiltro.value === 'activas'
})

// Composable artículos
const {
  articulos, pagination,
  obtenerArticulos, actualizarArticulos, eliminarArticulos, crearArticulos
} = useArticulos()

// Composable categorías (solo para selects)
const { categorias, obtenerCategorias } = useCategorias()
// Composable tags (solo para selects)
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

// Filtro
watch(estadoFiltro, async () => {
  await obtenerArticulos(1, filtros())
})

// Paginación
const paginationMethod = async (page) => {
  await obtenerArticulos(page, filtros())
}

// Buscador
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    obtenerArticulos(1, filtros())
  }, 300)
})

//funcion de recarga de articulos
const recargar = async () => {
  await obtenerArticulos(1, filtros())
  tablaKey.value++
}

onBeforeUnmount(() => clearTimeout(searchTimeout))

// Ver
const verArticulo = (item) => {
  dialogVerArticulo.value = true
  articulo.value = item
}

// Abrir edición
const ActualizarArticulo = (item) => {
  form.value = {
    id: item.id,
    title: item.title,
    content: item.content,
    category_id: item.category?.id,
    tags: item.tags?.map(t => t.id) || [],
    is_published: item.is_published
  }
  dialogActualizarArticulo.value = true
  articulo.value = item
}

// Guardar edición
const guardarEdicion = async () => {
  const { valid } = await formEditar.value.validate()
  if (!valid) return

  guardando.value = true
  const ok = await actualizarArticulos(form.value.id, {
    title: form.value.title,
    content: form.value.content,
    category_id: form.value.category_id,
    tags: form.value.tags,
    is_published: form.value.is_published
  })
  guardando.value = false

  if (ok) {
    dialogActualizarArticulo.value = false
    await recargar()
  }
}

// Cambiar estado
const tablaKey = ref(0)

const cambiarEstado = async (item) => {
    const ok = await eliminarArticulos(item.id)
    if (ok) {
        await obtenerArticulos(1, filtros())
        tablaKey.value++
    }
}

// Abrir crear
const abrirCrear = () => {
  nuevoArticulo.value = { title: '', content: '', category_id: null, tags: [], is_published: false }
  dialogCrearArticulo.value = true
}

// Guardar nueva
const guardarNueva = async () => {
  const { valid } = await formNueva.value.validate()
  if (!valid) return

  creando.value = true
  const ok = await crearArticulos({
    title: nuevoArticulo.value.title,
    content: nuevoArticulo.value.content,
    category_id: nuevoArticulo.value.category_id,
    tags: nuevoArticulo.value.tags,
    is_published: nuevoArticulo.value.is_published
  })
  creando.value = false

  if (ok) {
    dialogCrearArticulo.value = false
    await recargar()
  }
}

//cambiar estado de publicado 
const cambiarPublicado = async (item) => {
  const ok = await actualizarArticulos(item.id, {
    title: item.title,
    content: item.content,
    category_id: item.category?.id,
    is_published: !item.is_published
  })

  if (ok) {
    await recargar()
  }
}

// Carga inicial
// Carga inicial: artículos + categorías + tags para los selects
onMounted(async () => {
  await Promise.all([
    obtenerArticulos(1, filtros()),
    obtenerCategorias(1, { estado: true }),   // solo activas
    obtenerTags(1, { estado: true }),         // solo activas
  ])
})



</script>