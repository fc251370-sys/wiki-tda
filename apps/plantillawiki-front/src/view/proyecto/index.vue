<template>

    <h1>Proyecto</h1>
    <v-tabs v-model="tabProyecto" color="primary">
    <v-tab value="todos">Todos</v-tab>
    <v-tab value="activos">Activos</v-tab>
    <v-tab value="inactivos">Inactivos</v-tab>
    </v-tabs>

    <v-text-field
            v-model="busqueda"
            label="Buscar proyecto"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            clearable
        />
    <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        class="my-3"
        @click="dialogAgregarProyecto = true"
    >
    Agregar proyecto
</v-btn>

   <!-- Tarjetas de proyectos activos -->
<h2 class="mb-4">
    {{ tabProyecto === 'todos' ? 'Todos los proyectos' : tabProyecto === 'activos' ? 'Proyectos activos' : 'Proyectos inactivos' }}
</h2>

<v-row>
    <v-col
        v-for="proyecto in proyectosFiltrados"
        :key="proyecto.id"
        cols="12"
        sm="6"
        md="4"
    >
        <v-card class="pa-3" rounded="lg" elevation="2">
            <v-card-title>
                {{ proyecto.title }}
            </v-card-title>

            <v-card-text>
                {{ proyecto.description || 'Sin descripción' }}

                <div class="mt-3">
                    <strong>Estado:</strong> {{ proyecto.status }}
                </div>
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    v-tooltip:bottom="'Ver'"
                    icon="mdi-eye"
                    density="compact"
                    @click="verProyecto(proyecto)"
                />
                <v-btn
                    v-tooltip:bottom="'Editar'"
                    icon="mdi-pencil"
                    color="primary"
                    density="compact"
                    @click="editarProyecto(proyecto)"
                />
                <v-btn
                    v-if="proyecto.estado"
                    v-tooltip:bottom="'Desactivar'"
                    icon="mdi-cancel"
                    color="error"
                    density="compact"
                    @click="cambiarEstado(proyecto)"
                />

                <v-btn
                    v-if="!proyecto.estado"
                    v-tooltip:bottom="'Activar'"
                    icon="mdi-check-circle"
                    color="success"
                    density="compact"
                    @click="activarEstado(proyecto)"
                />
            </v-card-actions>
        </v-card>
    </v-col>
</v-row>

    <!-- Ventana para ver los detalles del proyecto -->
    <v-dialog v-model="dialogVerProyecto" max-width="500" persistent>
        <v-card>
            <v-card-title>
                Detalles del proyecto
            </v-card-title>

            <v-card-text>
                <p><strong>ID:</strong> {{ proyecto?.id }}</p>
                <p><strong>Nombre:</strong> {{ proyecto?.title }}</p>
                <p><strong>Descripción:</strong> {{ proyecto?.description }}</p>
                <p><strong>Estado:</strong> {{ proyecto?.status }}</p>
                <p><strong>Repositorio:</strong> {{ proyecto?.repository_url }}</p>

    <div class="mt-4">
        <strong>Archivos del proyecto:</strong>

        <p v-if="!proyecto?.files?.length">
            No hay archivos adjuntos.
        </p>

        <ul v-else>
            <li
    v-for="archivo in proyecto.files.filter(archivo => archivo.estado)"
    :key="archivo.id"
>
    {{ archivo.original_name }}

    <v-btn
        size="small"
        color="primary"
        @click="verArchivo(archivo)"
    >
        Visualizar
    </v-btn>

    <v-btn
        size="small"
        color="success"
        @click="bajarArchivo(archivo)"
    >
        Descargar
    </v-btn>

    
    <v-btn
        size="small"
        color="warning"
        @click="$event.currentTarget.parentElement.querySelector('input').click()"
    >
        Reemplazar
    </v-btn>

    <v-btn
        size="small"
        color="error"
        @click="borrarArchivo(archivo)"
    >
        Eliminar
    </v-btn>

    <input
    type="file"
    ref="archivoNuevo"
    style="display: none"
    @change="reemplazarArchivo($event, archivo)"
    >

</li>
        </ul>
    </div>
        <div class="mt-4">
    <strong>Adjuntar archivos:</strong>

    <v-file-input
        v-model="archivoSeleccionado"
        label="Seleccionar archivo"
        variant="outlined"
        density="compact"
        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
        prepend-icon="mdi-paperclip"
        show-size
    />
    <v-btn
        color="primary"
        block
        @click="guardarArchivo"
    >
        Subir archivo
    </v-btn>
</div>
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="error"
                    variant="outlined"
                    @click="dialogVerProyecto = false"
                >
                    Cerrar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialogEditarProyecto" max-width="600" persistent>
    <v-card>
        <v-card-title>Editar proyecto</v-card-title>

        <v-card-text>
            <v-text-field
                v-model="proyecto.title"
                label="Nombre del proyecto"
                variant="outlined"
            />

            <v-textarea
                v-model="proyecto.description"
                label="Descripción"
                variant="outlined"
            />

            <v-text-field
                v-model="proyecto.repository_url"
                label="URL del repositorio"
                variant="outlined"
            />
        </v-card-text>

        <v-card-actions>
            <v-spacer />
            <v-btn
                color="error"
                variant="outlined"
                @click="dialogEditarProyecto = false"
            >
                Cancelar
            </v-btn>
            <v-btn color="primary"
                    @click="guardarCambiosProyecto"
            >
                Guardar cambios
            </v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>
    <!-- Ventana para agregar un proyecto -->
    <v-dialog v-model="dialogAgregarProyecto" max-width="600" persistent>
    <v-card>
        <v-card-title>
            Agregar proyecto
        </v-card-title>

        <v-card-text>
            <v-text-field
                v-model="nuevoProyecto.title"
                label="Nombre del proyecto"
                variant="outlined"
            />

            <v-textarea
                v-model="nuevoProyecto.description"
                label="Descripción"
                variant="outlined"
            />

            <v-text-field
                v-model="nuevoProyecto.repository_url"
                label="URL del repositorio"
                variant="outlined"
            />
        </v-card-text>

        <v-card-actions>
            <v-spacer />

            <v-btn
                color="error"
                variant="outlined"
                @click="dialogAgregarProyecto = false"
            >
                Cancelar
            </v-btn>

            <v-btn
                color="primary"
                @click="guardarProyecto"
            >
                Guardar
            </v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { useProyectos } from './composable/proyectoComposable.js'




// Encabezados de la tabla
const headers = [
    { title: 'ID', key: 'id' },
    { title: 'Nombre', key: 'title' },
    { title: 'Descripción', key: 'description' },
    { title: 'Estado', key: 'status' },
    { title: 'Acciones', key: 'acciones' },
]

// Ventana para ver los detalles del proyecto
const dialogVerProyecto = ref(false)

const archivoSeleccionado = ref(null)

const dialogEditarProyecto = ref(false)


const editarProyecto = (item) => {
    proyecto.value = item
    dialogEditarProyecto.value = true
}
// Ventana para agregar un proyecto
const dialogAgregarProyecto = ref(false)
const nuevoProyecto = ref({
    event_subject_id: 1,
    title: '',
    description: '',
    repository_url: ''
})
// Proyecto seleccionado
const proyecto = ref(null)

// Pestaña seleccionada
const tabProyecto = ref('todos')
const busqueda = ref('')
watch(busqueda, (nuevoValor) => {
    obtenerProyectos(1, {
        search: nuevoValor
    })
})

// Filtrar proyectos según la pestaña seleccionada
const proyectosFiltrados = computed(() => {
    if (tabProyecto.value === 'activos') {
        return proyectosActivos.value
    }

    if (tabProyecto.value === 'inactivos') {
        return proyectosDesactivados.value
    }

    return proyectos.value
})

// Obtener los proyectos
const {
    proyectos,
    proyectosActivos,
    proyectosDesactivados,
    pagination,
    obtenerProyectos,
    eliminarProyecto,
    activarProyecto,
    crearProyecto,
    actualizarProyecto,
    subirArchivo,
    actualizarArchivo,
    visualizarArchivo,
    descargarArchivo,
    eliminarArchivo,
} = useProyectos()

const guardarProyecto = async () => {
    const respuesta = await crearProyecto(nuevoProyecto.value)

    if (respuesta) {
        dialogAgregarProyecto.value = false

        nuevoProyecto.value = {
            event_subject_id: 1,
            title: '',
            description: '',
            repository_url: ''
        }

        await obtenerProyectos()
    }
}

const guardarCambiosProyecto = async () => {
    const respuesta = await actualizarProyecto(proyecto.value.id, {
        title: proyecto.value.title,
        description: proyecto.value.description,
        repository_url: proyecto.value.repository_url
    })

    if (respuesta) {
        dialogEditarProyecto.value = false
        await obtenerProyectos()
    }
}

const guardarArchivo = async () => {
    if (!archivoSeleccionado.value || !proyecto.value) {
        return
    }

    const respuesta = await subirArchivo(
        proyecto.value.id,
        archivoSeleccionado.value
    )

    if (respuesta) {
        archivoSeleccionado.value = null
        await obtenerProyectos()
        proyecto.value = proyectos.value.find(
            item => item.id === proyecto.value.id
        )
    }
}

    // Reemplazar un archivo
    const reemplazarArchivo = async (event, archivo) => {
        const archivoNuevo = event.target.files[0]
        console.log('Archivo seleccionado:', archivoNuevo)
        if (!archivoNuevo) {
            return
        }

        const respuesta = await actualizarArchivo(
            archivo.id,
            archivoNuevo
        )

        if (respuesta) {
            await obtenerProyectos()

            proyecto.value = proyectos.value.find(
                item => item.id === proyecto.value.id
            )
        }

        event.target.value = ''
    }

    // Mostrar los detalles del proyecto seleccionado
    const verProyecto = (item) => {
        proyecto.value = item
        dialogVerProyecto.value = true
    }
    // Visualizar un archivo
    const verArchivo = async (archivo) => {
    const respuesta = await visualizarArchivo(archivo.id)

    if (respuesta) {
        const url = window.URL.createObjectURL(respuesta)
        window.open(url, '_blank')
    }
}
// Descargar un archivo
const bajarArchivo = async (archivo) => {
    const respuesta = await descargarArchivo(archivo.id)

    if (respuesta) {
        const url = window.URL.createObjectURL(respuesta.data)
        const enlace = document.createElement('a')

        enlace.href = url
        enlace.download = archivo.original_name
        enlace.click()

        window.URL.revokeObjectURL(url)
    }
}

const borrarArchivo = async (archivo) => {
    const confirmar = window.confirm(
        `¿Estás segura de que deseas eliminar el archivo "${archivo.original_name}"?`
    )

    if (!confirmar) {
        return
    }

    const respuesta = await eliminarArchivo(archivo.id)

    if (respuesta) {
        archivo.estado = false
    }
}

// Desactivar un proyecto
const cambiarEstado = async (item) => {
    const confirmar = window.confirm(
        `¿Deseas desactivar el proyecto ${item.title}?`
    )

    if (!confirmar) return

    const ok = await eliminarProyecto(item.id)

    if (ok) {
        await obtenerProyectos()
    }
}


const activarEstado = async (item) => {
    const confirmar = window.confirm(
        `¿Deseas activar el proyecto ${item.title}?`
    )

    if (!confirmar) return

    const ok = await activarProyecto(item.id)

    if (ok) {
        await obtenerProyectos()
    }
}


// Cargar los proyectos
onMounted(async () => {
    await obtenerProyectos()

    console.log('Proyectos activos:', proyectosActivos.value)
    console.log('Proyectos desactivados:', proyectosDesactivados.value)
})
</script>
