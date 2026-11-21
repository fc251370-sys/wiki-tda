<template>
    <div>
        <title-component :title="'Listado de eventos'"></title-component>
        <v-row class="d-flex justify-end">
            <v-col cols="12" md="4">
                <v-text-field v-model="search" label="Buscar por ciclo" variant="outlined" clearable density="compact" rounded-xl append-inner-icon="mdi-magnify" @keyup.enter="buscar" @click:append-inner="buscar" @click:clear="buscar"></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col v-if="esGestor">
                <v-btn v-tooltip:bottom="'Nuevo Evento'" color="primary" icon="mdi-plus" @click="abrirCrear"></v-btn>
            </v-col>
            <v-col v-if="esGestor" cols="auto">
                <v-btn-toggle v-model="activoFiltro" mandatory color="primary" density="compact" divided>
                    <v-btn value="todos">Todos</v-btn>
                    <v-btn value="activo">Ciclo activo</v-btn>
                    <v-btn value="inactivos">No activos</v-btn>
                </v-btn-toggle>
            </v-col>
        </v-row>

        <data-table-component :key="tablaKey" :totalPage="pagination?.last_page" :page="pagination?.current_page" :headers="headers" :items="eventos" @update:page="paginationMethod">
            <template v-slot:item.coordinador="{ item }">
                {{ item.coordinador?.name }}
            </template>
            <template v-slot:item.is_active="{ item }">
                <v-chip :color="item.is_active ? 'success' : 'grey'">
                    {{ item.is_active ? 'Ciclo activo' : 'No activo' }}
                </v-chip>
            </template>
            <template v-slot:item.estado="{ item }">
                <v-chip :color="item.estado ? 'success' : 'error'">
                    {{ item.estado ? 'Habilitado' : 'Deshabilitado' }}
                </v-chip>
            </template>
            <template v-slot:item.acciones="{ item }">
                <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verEvento(item)"></v-btn>

                <v-btn v-if="esGestor && item.estado" v-tooltip:bottom="'Editar'" class="ml-2" density="compact" icon="mdi-pencil" @click="abrirEditar(item)"></v-btn>

                <!-- activar o desactivar el ciclo (solo si el evento esta habilitado) -->
                <v-btn v-if="esGestor && item.estado" v-tooltip:bottom="item.is_active ? 'Desactivar ciclo' : 'Activar ciclo'" class="ml-2" density="compact" :icon="item.is_active ? 'mdi-pause-circle' : 'mdi-play-circle'" :color="item.is_active ? 'warning' : 'success'" @click="alternarActivo(item)"></v-btn>

                <!-- deshabilitar o habilitar el evento -->
                <v-btn v-if="esGestor" v-tooltip:bottom="item.estado ? 'Deshabilitar' : 'Habilitar'" class="ml-2" density="compact" :icon="item.estado ? 'mdi mdi-cancel' : 'mdi mdi-check-circle'" :color="item.estado ? 'error' : 'success'" @click="cambiarEstado(item)"></v-btn>
            </template>
        </data-table-component>

        <!-- ver evento -->
        <v-dialog v-model="dialogVer" max-width="500" persistent>
            <v-card class="mx-auto my-8" elevation="4" width="500" rounded="lg">
                <v-card-item>
                    <v-card-title>{{ eventoSeleccionado?.cicle_name }}</v-card-title>
                </v-card-item>
                <v-card-text>
                    <p>Fecha de inicio: {{ eventoSeleccionado?.start_date }}</p>
                    <p>Coordinador: {{ eventoSeleccionado?.coordinador?.name }}</p>
                    <p>Materias: {{ eventoSeleccionado?.subjects_count }}</p>
                    <p>Ciclo: {{ eventoSeleccionado?.is_active ? 'Activo' : 'No activo' }}</p>
                </v-card-text>
                <v-card-actions>
                    <v-row>
                        <v-col cols="12" class="text-center">
                            <v-btn rounded="lg" variant="outlined" color="error" text @click="dialogVer = false">Cerrar</v-btn>
                        </v-col>
                    </v-row>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- crear / editar evento -->
        <v-dialog v-model="dialogForm" max-width="500" persistent>
            <v-card>
                <v-card-title>{{ esEdicion ? 'Editar evento' : 'Crear evento' }}</v-card-title>
                <v-card-text>
                    <v-alert v-if="!esEdicion" type="info" variant="tonal" density="compact" class="mb-4">
                        El nuevo evento queda como ciclo activo y desactiva el que estaba activo.
                    </v-alert>
                    <v-form ref="formEvento">
                        <v-text-field
                            v-model="form.cicle_name"
                            label="Nombre del ciclo"
                            :rules="[v => !!v?.trim() || 'El nombre del ciclo es obligatorio', v => v?.trim().length >= 3 || 'Mínimo 3 caracteres', v => v?.trim().length <= 50 || 'Máximo 50 caracteres']"
                        />
                        <v-select
                            v-model="form.coordinador_id"
                            :items="coordinadores"
                            item-title="nombre"
                            item-value="id"
                            label="Coordinador"
                            :rules="[v => !!v || 'El coordinador es obligatorio']"
                        />
                        <v-text-field
                            v-model="form.start_date"
                            type="date"
                            label="Fecha de inicio"
                            :rules="[v => !!v || 'La fecha de inicio es obligatoria']"
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
import { ref, onMounted, watch } from 'vue'
import DataTableComponent from '../../components/DataTableComponent.vue';
import TitleComponent from '../../components/TitleComponent.vue';
import { useEventos } from './composable/eventoComposable'
import { hasRole } from '../../utils/authSession'

const {
    eventos,
    pagination,
    coordinadores,
    obtenerEventos,
    obtenerCoordinadores,
    crearEvento,
    actualizarEvento,
    cambiarEstadoEvento,
    alternarActivoEvento,
} = useEventos();

// admin y coordinador gestionan los eventos; docente y estudiante solo ven el evento activo
const esGestor = hasRole('admin', 'coordinador');

const headers = ref(esGestor
    ? [
        { title: 'Ciclo', key: 'cicle_name' },
        { title: 'Inicio', key: 'start_date' },
        { title: 'Coordinador', key: 'coordinador' },
        { title: 'Materias', key: 'subjects_count' },
        { title: 'Ciclo activo', key: 'is_active' },
        { title: 'Estado', key: 'estado' },
        { title: 'Acciones', key: 'acciones' },
    ]
    : [
        { title: 'Ciclo', key: 'cicle_name' },
        { title: 'Inicio', key: 'start_date' },
        { title: 'Coordinador', key: 'coordinador' },
        { title: 'Materias', key: 'subjects_count' },
        { title: 'Acciones', key: 'acciones' },
    ]);

// buscar y filtrar
const search = ref('')
const activoFiltro = ref('todos');

const filtros = () => ({
    cicle_name: search.value?.trim() || undefined,
    // quien no gestiona solo ve el evento activo
    is_active: !esGestor ? true : (activoFiltro.value === 'todos' ? undefined : activoFiltro.value === 'activo')
})

watch(activoFiltro, async () => {
    await obtenerEventos(1, filtros())
})

// la busqueda se hace solo con Enter, con la lupa o al limpiar el campo
const buscar = async () => {
    await obtenerEventos(1, filtros())
}

const paginationMethod = async (page) => {
    await obtenerEventos(page, filtros())
}

// refrescar la tabla: recarga los datos y la key fuerza que se vuelva a dibujar
const tablaKey = ref(0)
const refrescarTabla = async (page) => {
    await obtenerEventos(page ?? pagination.value?.current_page ?? 1, filtros())
    tablaKey.value++
}

// ver evento
const dialogVer = ref(false);
const eventoSeleccionado = ref(null);

const verEvento = (item) => {
    eventoSeleccionado.value = item;
    dialogVer.value = true;
}

// crear y editar (mismo formulario)
const dialogForm = ref(false);
const esEdicion = ref(false);
const formEvento = ref(null);
const guardando = ref(false);
const form = ref({ id: null, cicle_name: '', coordinador_id: null, start_date: '' });

const abrirCrear = () => {
    if (!coordinadores.value.length) obtenerCoordinadores();
    esEdicion.value = false;
    form.value = { id: null, cicle_name: '', coordinador_id: null, start_date: '' };
    dialogForm.value = true;
}

const abrirEditar = (item) => {
    if (!coordinadores.value.length) obtenerCoordinadores();
    esEdicion.value = true;
    form.value = {
        id: item.id,
        cicle_name: item.cicle_name,
        coordinador_id: item.coordinador?.id ?? null,
        start_date: item.start_date,
    };
    dialogForm.value = true;
}

const guardar = async () => {
    const { valid } = await formEvento.value.validate();
    if (!valid) return;

    guardando.value = true;
    const datos = {
        cicle_name: form.value.cicle_name.trim(),
        coordinador_id: form.value.coordinador_id,
        start_date: form.value.start_date,
    };

    const ok = esEdicion.value
        ? await actualizarEvento(form.value.id, datos)
        : await crearEvento(datos);
    guardando.value = false;

    if (ok) dialogForm.value = false;
    // se refresca siempre: si hubo timeout, el servidor pudo guardar igual
    await refrescarTabla(esEdicion.value ? undefined : 1);
}

// activar o desactivar el ciclo
const alternarActivo = async (item) => {
    await alternarActivoEvento(item.id);
    await refrescarTabla();
}

// deshabilitar o habilitar el evento
const cambiarEstado = async (item) => {
    await cambiarEstadoEvento(item.id);
    await refrescarTabla();
}

onMounted(async () => {
    await obtenerEventos(1, filtros());
    // la lista de coordinadores solo hace falta para crear o editar
    if (esGestor) await obtenerCoordinadores();
})
</script>