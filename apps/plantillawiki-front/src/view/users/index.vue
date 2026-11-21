<template>
    <div>
        <title-component :title="'Listado de usuarios'"></title-component>
        <v-row class="d-flex justify-end">
            <v-col cols="12" md="4">
                <v-text-field v-model="search" variant="outlined" clearable density="compact" placeholder="Buscar" rounded-xl append-inner-icon="mdi-magnify"></v-text-field>
            </v-col>
        </v-row>

        <!-- botón nuevo usuario -->
    <v-row>
      <v-col>
        <v-btn
            v-if="puedeGestionar" 
            v-tooltip:bottom="'Nuevo Usuario'" 
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

        <data-table-component :headers="headers" :items="usuarios" :totalPage="paginacion?.lastPage" :page="paginacion?.currentPage" @update:page="changePage">
            <template v-slot:item.roles="{ item }">
                <v-chip v-for="rol in item.roles" :key="rol" size="small" class="mr-1">
                    {{ rol }}
                </v-chip>
            </template>

            <template v-slot:item.estado="{ item }">
                <v-chip :color="item.estado ? 'success' : 'error'" size="small">
                    {{ item.estado ? 'Activo' : 'Inactivo' }}
                </v-chip>
            </template>

            
            <template v-slot:item.acciones="{ item }">
                <v-row>
                    <v-col cols="12" col-md="4" col-sm="3">
                        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verUsuario(item)"></v-btn>
                        <!--se arreglaron estos botnoes como los demas -->
                        <v-btn v-if="puedeGestionar && item.estado" v-tooltip:bottom="'Editar'" class="ml-2" density="compact" icon="mdi-pencil" @click="abrirEditar(item)"></v-btn>

                        <v-btn v-if="puedeGestionar" v-tooltip:bottom="item.estado ? 'Desactivar' : 'Activar'" class="ml-2" density="compact" :icon="item.estado ? 'mdi mdi-cancel' : 'mdi mdi-check-circle'" :color="item.estado ? 'error' : 'success'" @click="cambiarEstado(item)"></v-btn>
                    </v-col>
                </v-row>
            </template>
        </data-table-component>

        <!--forms-->

        <v-dialog v-model="dialogForm" max-width="500" persistent>
    <v-card>
        <v-card-title>{{ esEdicion ? 'Editar usuario' : 'Crear usuario' }}</v-card-title>
        <v-card-text>
            <v-form ref="formUsuario">
                <v-text-field
                    v-model="form.name"
                    label="Nombre"
                    :rules="[v => !!v?.trim() || 'El nombre es obligatorio']"
                />
                <v-text-field
                    v-model="form.email"
                    label="Correo"
                    type="email"
                    :rules="[v => !!v?.trim() || 'El correo es obligatorio', v => /.+@.+\..+/.test(v) || 'Correo no válido']"
                />
                <v-text-field v-model="form.carnet_or_code" label="Carnet o código" />
                <v-text-field
                    v-model="form.password"
                    label="Contraseña"
                    type="password"
                    :hint="esEdicion ? 'Déjala vacía para no cambiarla' : ''"
                    persistent-hint
                    :rules="esEdicion
                        ? [v => !v || v.length >= 8 || 'Mínimo 8 caracteres']
                        : [v => !!v || 'La contraseña es obligatoria', v => v?.length >= 8 || 'Mínimo 8 caracteres']"
                />
                <v-select
                    v-model="form.role"
                    :items="roles"
                    item-title="title"
                    item-value="value"
                    label="Rol"
                    :rules="[v => !!v || 'El rol es obligatorio']"
                />
                <v-select
                    v-model="form.carrera_id"
                    :items="carreras"
                    item-title="name"
                    item-value="id"
                    label="Carrera (opcional)"
                    clearable
                />
            </v-form>
        </v-card-text>
        <v-card-actions>
            <v-btn @click="dialogForm = false">Cancelar</v-btn>
            <v-btn color="primary" :loading="guardando" @click="guardar">Guardar</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import TitleComponent from '../../components/TitleComponent.vue';
import DataTableComponent from '../../components/DataTableComponent.vue';
import { useUsers } from './composable/userComposable.js';

const { usuarios, paginacion, obtenerUsers, actualizarUsers, eliminarUsers, crearUsers} = useUsers()

import { hasRole } from '../../utils/authSession.js';
import { useCarreras } from '../carrera/composable/carreraComposable.js';

const puedeGestionar = hasRole('admin')
const { carreras, obtenerCarreras } = useCarreras()


const headers = ref([
    { title: 'Nombre', key: 'nombre' },
    { title: 'Correo electronico', key: 'email' },
    { title: 'Carnet', key: 'carnet' },
    { title: 'Rol', key: 'roles' },
    { title: 'Estado', key: 'estado' },
    { title: 'Acciones', key: 'acciones' },
])

const search = ref('')

const roles = [
    {title: 'Administrador', value: 'admin'},
    {title: 'Coordinador', value: 'coordinador'},
    {title: 'Docente', value: 'teacher'},
    {title: 'Estudiante', value: 'student'},
]

const dialogForm = ref(false)
const esEdicion = ref(false)
const formUsuario = ref(null)
const guardando = ref(false)

const form = ref({ id:null, name:'', email:'', carnet_or_code:'', password:'', carrera_id:null, role:null})

const estadoFiltro = ref('todas')

const filtros = () => ({
  title: search.value?.trim() || undefined,
    estado: estadoFiltro.value === 'todas' ? undefined : (estadoFiltro.value === 'activas' ? 1 : 0),
})

const recargar = async (page) => {
    await obtenerUsers(page ?? paginacion.value?.currentPage ?? 1, filtros())
}



let searchTimeout

const verUsuario = (item) => {
    // pendiente: abrir el detalle del usuario
}

const changePage = async (page) => {
    await obtenerUsers(page,  filtros())
}

watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        obtenerUsers(1, filtros())
    }, 300)
})

watch(estadoFiltro, () => obtenerUsers(1, filtros()))

//funciones

const abrirCrear = () => {
    if(!carreras.value.length) obtenerCarreras(1, {estado: true})
    esEdicion.value = false
    form.value = {id:null, name:'', email:'', carnet_or_code:'',password:'', carrera_id:null, role:null}
    dialogForm.value = true
}

const abrirEditar = (item) => {
    if(!carreras.value.length) obtenerCarreras(1, {estado:true})
    esEdicion.value = true
    form.value = { 
        id: item.id,
        name: item.nombre,
        email: item.email,
        carnet_or_code: item.carnet ?? '',
        password: '',
        carrera_id: item.carrera_id ?? null,
        role: item.roles?.[0] ?? null,
    }
    dialogForm.value = true
}

const guardar = async () => {
    const { valid } = await formUsuario.value.validate()
    if (!valid) return

    guardando.value = true
    const datos = {
        name: form.value.name.trim(),
        email: form.value.email.trim(),
        carnet_or_code: form.value.carnet_or_code?.trim() || null,
        carrera_id: form.value.carrera_id,
        role: form.value.role,
    }
    // la contraseña solo se envía si escribieron una
    if (form.value.password) datos.password = form.value.password

    const ok = esEdicion.value
        ? await actualizarUsers(form.value.id, datos)
        : await crearUsers(datos)
    guardando.value = false

    if (ok) {
        dialogForm.value = false
        await recargar(esEdicion.value ? undefined : 1)
    }
}


const cambiarEstado = async (item) => {
    const ok = await eliminarUsers(item.id)
    if(ok) await recargar()
}

onMounted(async () => {
    await obtenerUsers()
})

onBeforeUnmount(() => clearTimeout(searchTimeout))
</script>