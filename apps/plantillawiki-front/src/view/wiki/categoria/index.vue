<template>
  <div>
    <!-- Botón para volver a las cards de wiki -->
    <v-btn prepend-icon="mdi-arrow-left" variant="text" @click="$router.push('/admin/wiki')">
    Volver
    </v-btn>

    <!-- Buscador de categorías -->
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

    <!-- Botón nueva + filtro de estado -->
    <v-row>
      <v-col>
        <v-btn
          v-tooltip:bottom="'Nueva Categoría'"
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

    <!-- Tabla de categorías -->
    <data-table-component
      :key="tablaKey"
      :totalPage="pagination?.last_page"
      :page="pagination?.current_page"
      :headers="headers"
      :items="categorias"
      @update:page="paginationMethod"
    >
      <!-- Chip de estado -->
      <template v-slot:item.estado="{ item }">
        <v-chip :color="item.estado ? 'success' : 'error'">
          {{ item.estado ? 'Activo' : 'Inactivo' }}
        </v-chip>
      </template>

      <!-- Botones de acción -->
      <template v-slot:item.acciones="{ item }">
        <v-btn
          v-tooltip:bottom="'Ver'"
          density="compact"
          icon="mdi-eye"
          @click="verCategoria(item)"
        />
        <v-btn
          v-tooltip:bottom="'Editar'"
          class="ml-2"
          density="compact"
          icon="mdi-pencil"
          v-if="item.estado"
          @click="ActualizarCategoria(item)"
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

    <!-- Diálogo ver categoría -->
    <v-dialog v-model="dialogVerCategoria" persistent>
      <v-card class="mx-auto my-8" elevation="4" width="500" rounded="lg">
        <v-card-item>
          <v-card-title>{{ categoria.name }}</v-card-title>
        </v-card-item>
        <v-card-text>
          <div><strong>Slug:</strong> {{ categoria.slug }}</div>
          <div><strong>Descripción:</strong> {{ categoria.description }}</div>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" class="text-center">
              <v-btn rounded="lg" variant="outlined" color="error" text @click="dialogVerCategoria = false">
                Cerrar
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo editar categoría -->
    <v-dialog v-model="dialogActualizarCategoria" max-width="500" persistent>
      <v-card>
        <v-card-title>Editar categoría</v-card-title>
        <v-card-text>
          <v-form ref="formEditar">
            <v-text-field
              v-model="form.name"
              label="Nombre"
              :rules="[v => !!v?.trim() || 'El nombre es obligatorio']"
            />
            <v-text-field v-model="form.description" label="Descripción" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogActualizarCategoria = false">Cancelar</v-btn>
          <v-btn color="primary" :loading="guardando" @click="guardarEdicion">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo crear categoría -->
    <v-dialog v-model="dialogCrearCategoria" max-width="500" persistent>
      <v-card>
        <v-card-title>Crear categoría</v-card-title>
        <v-card-text>
          <v-form ref="formNueva">
            <v-text-field
              v-model="nuevaCategoria.name"
              label="Nombre"
              :rules="[
                v => !!v?.trim() || 'El nombre es obligatorio',
                v => v?.trim().length >= 3 || 'Mínimo 3 caracteres'
              ]"
            />
            <v-text-field v-model="nuevaCategoria.description" label="Descripción" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogCrearCategoria = false">Cancelar</v-btn>
          <v-btn color="primary" :loading="creando" @click="guardarNueva">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
// ref, onMounted, watch, onBeforeUnmount para el componente
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
// Componente de tabla reutilizable
import DataTableComponent from '../../../components/DataTableComponent.vue'
// Composable de categorías (ya lo creaste)
import { useCategorias } from './composable/categoriaComposable.js'

// Crear categoría
const dialogCrearCategoria = ref(false)
const nuevaCategoria = ref({ name: '', slug: '', description: '' })
const formNueva = ref(null)
const creando = ref(false)

// Ver categoría
const dialogVerCategoria = ref(false)
const categoria = ref(null)

// Editar categoría
const dialogActualizarCategoria = ref(false)
const form = ref({ id: null, name: '', slug: '', description: '' })
const formEditar = ref(null)
const guardando = ref(false)

// Buscador
const search = ref('')
let searchTimeout

// Filtro de estado
const estadoFiltro = ref('todas')
const filtros = () => ({
  name: search.value?.trim() || undefined,
  estado: estadoFiltro.value === 'todas' ? undefined : estadoFiltro.value === 'activas'
})

// Composable
const {
  categorias, pagination,
  obtenerCategorias, actualizarCategorias, eliminarCategorias, crearCategorias
} = useCategorias()

// Headers de la tabla
const headers = ref([
  { title: 'Nombre', key: 'name' },
  { title: 'Slug', key: 'slug' },
  { title: 'Descripción', key: 'description' },
  { title: 'Estado', key: 'estado' },
  { title: 'Acciones', key: 'acciones' },
])

// Cambio de filtro de estado
watch(estadoFiltro, async () => {
  await obtenerCategorias(1, filtros())
})

// Paginación
const paginationMethod = async (page) => {
  await obtenerCategorias(page, filtros())
}

// Buscador con debounce
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    obtenerCategorias(1, filtros())
  }, 300)
})

// Para que la tabla se reaga al cambiar datos y no se quede trabada con coasa viejas
const tablaKey = ref(0)

const recargar = async () => {
  await obtenerCategorias(1, filtros())
  tablaKey.value++
}


onBeforeUnmount(() => clearTimeout(searchTimeout))

// Ver categoría
const verCategoria = (item) => {
  dialogVerCategoria.value = true
  categoria.value = item
}

// Abrir edición
const ActualizarCategoria = (item) => {
  form.value = { id: item.id, name: item.name, description: item.description }
  dialogActualizarCategoria.value = true
  categoria.value = item
}

// Guardar edición
const guardarEdicion = async () => {
  const { valid } = await formEditar.value.validate()
  if (!valid) return

  guardando.value = true
  const ok = await actualizarCategorias(form.value.id, {
    name: form.value.name,
    description: form.value.description
  })
  guardando.value = false

  if (ok) {
    dialogActualizarCategoria.value = false
    await recargar()
  }
}

// Cambiar estado
const cambiarEstado = async (item) => {
  const ok = await eliminarCategorias(item.id)
  if (ok) {
    await recargar()
  }
}

// Abrir crear
const abrirCrear = () => {
  nuevaCategoria.value = { name: '', description: '' }
  dialogCrearCategoria.value = true
}

// Guardar nueva
const guardarNueva = async () => {
  const { valid } = await formNueva.value.validate()
  if (!valid) return

  creando.value = true
  const ok = await crearCategorias({
    name: nuevaCategoria.value.name,
    description: nuevaCategoria.value.description
  })
  creando.value = false

  if (ok) {
    dialogCrearCategoria.value = false
    await recargar()
  }
}

// Carga inicial
onMounted(async () => {
  await obtenerCategorias(1, filtros())
})
</script>