<template>
  <div>
    <!-- Botón para volver a las cards de wiki -->
    <v-btn prepend-icon="mdi-arrow-left" variant="text" @click="$router.push('/admin/wiki')">
    Volver
    </v-btn>

    <!-- Buscador de tags -->
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
          v-tooltip:bottom="'Nuevo Tag'"
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

    <!-- Tabla de tags -->
    <data-table-component
      :key="tablaKey"
      :totalPage="pagination?.last_page"
      :page="pagination?.current_page"
      :headers="headers"
      :items="tags"
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
        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verTag(item)" />
        <v-btn
          v-tooltip:bottom="'Editar'"
          class="ml-2"
          density="compact"
          icon="mdi-pencil"
          v-if="item.estado"
          @click="ActualizarTag(item)"
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

    <!-- Diálogo ver tag -->
    <v-dialog v-model="dialogVerTag" persistent>
      <v-card class="mx-auto my-8" elevation="4" width="500" rounded="lg">
        <v-card-item>
          <v-card-title>{{ tag.name }}</v-card-title>
        </v-card-item>
        <v-card-text>
          <div><strong>Slug:</strong> {{ tag.slug }}</div>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" class="text-center">
              <v-btn rounded="lg" variant="outlined" color="error" text @click="dialogVerTag = false">
                Cerrar
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo editar tag -->
    <v-dialog v-model="dialogActualizarTag" max-width="500" persistent>
      <v-card>
        <v-card-title>Editar tag</v-card-title>
        <v-card-text>
          <v-form ref="formEditar">
            <v-text-field
              v-model="form.name"
              label="Nombre"
              :rules="[v => !!v?.trim() || 'El nombre es obligatorio']"
            />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogActualizarTag = false">Cancelar</v-btn>
          <v-btn color="primary" :loading="guardando" @click="guardarEdicion">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Diálogo crear tag -->
    <v-dialog v-model="dialogCrearTag" max-width="500" persistent>
      <v-card>
        <v-card-title >Crear tag</v-card-title>
        <v-card-text>
          <v-form ref="formNueva">
            <v-text-field
              v-model="nuevoTag.name"
              label="Nombre"
              :rules="[
                v => !!v?.trim() || 'El nombre es obligatorio',
                v => v?.trim().length >= 3 || 'Mínimo 3 caracteres'
              ]"
            />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="dialogCrearTag = false">Cancelar</v-btn>
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
// Composable de tags
import { useTags } from './composable/tagComposable.js'

// Crear
const dialogCrearTag = ref(false)
const nuevoTag = ref({ name: '', slug: '' })
const formNueva = ref(null)
const creando = ref(false)

// Ver
const dialogVerTag = ref(false)
const tag = ref(null)

// Editar
const dialogActualizarTag = ref(false)
const form = ref({ id: null, name: ''})
const formEditar = ref(null)
const guardando = ref(false)

// Buscador
const search = ref('')
let searchTimeout

// Filtro
const estadoFiltro = ref('todas')
const filtros = () => ({
  name: search.value?.trim() || undefined,
  estado: estadoFiltro.value === 'todas' ? undefined : estadoFiltro.value === 'activas'
})

// Composable
const { tags, pagination, obtenerTags, actualizarTags, eliminarTags, crearTags } = useTags()

// Headers
const headers = ref([
  { title: 'Nombre', key: 'name' },
  { title: 'Slug', key: 'slug' },
  { title: 'Estado', key: 'estado' },
  { title: 'Acciones', key: 'acciones' },
])

// Filtro
watch(estadoFiltro, async () => {
  await obtenerTags(1, filtros())
})

// Paginación
const paginationMethod = async (page) => {
  await obtenerTags(page, filtros())
}

// Buscador
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    obtenerTags(1, filtros())
  }, 300)
})

// Para que la tabla se redibuje al cambiar datos
const tablaKey = ref(0)

const recargar = async () => {
  await obtenerTags(1, filtros())
  tablaKey.value++
}
onBeforeUnmount(() => clearTimeout(searchTimeout))

// Ver
const verTag = (item) => {
  dialogVerTag.value = true
  tag.value = item
}

// Abrir edición
const ActualizarTag = (item) => {
  form.value = { id: item.id, name: item.name }
  dialogActualizarTag.value = true
  tag.value = item
}

// Guardar edición
const guardarEdicion = async () => {
  const { valid } = await formEditar.value.validate()
  if (!valid) return

  guardando.value = true
  const ok = await actualizarTags(form.value.id, {
    name: form.value.name,
  })
  guardando.value = false

  if (ok) {
    dialogActualizarTag.value = false
    await recargar()
  }
}

// Cambiar estado
const cambiarEstado = async (item) => {
  const ok = await eliminarTags(item.id)
  if (ok) {
    await recargar()
  }
}

// Abrir crear
const abrirCrear = () => {
  nuevoTag.value = { name: ''}
  dialogCrearTag.value = true
}

// Guardar nueva
const guardarNueva = async () => {
  const { valid } = await formNueva.value.validate()
  if (!valid) return

  creando.value = true
  const ok = await crearTags({
    name: nuevoTag.value.name,
  })
  creando.value = false

  if (ok) {
    dialogCrearTag.value = false
    await recargar()
  }
}

// Carga inicial
onMounted(async () => {
  await obtenerTags(1, filtros())
})
</script>