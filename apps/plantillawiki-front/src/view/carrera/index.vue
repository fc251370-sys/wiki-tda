<template>
    <div>
        <!-- buscar carrera seguir despues-->
        <title-component :title="'Listado de carreras'"></title-component>
        <v-row class="d-flex justify-end">
            <v-col cols="12" md="4" >
                <v-text-field v-model="search" variant="outlined" clearable density="compact" rounded-xl append-inner-icon="mdi-magnify" @input="obtenerCarreras"></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-btn v-tooltip:bottom="'Nueva Carrera'" color="primary" icon="mdi-plus" @click="abrirCrear" ></v-btn>
            </v-col>
        </v-row>
        
        <data-table-component :totalPage="pagination?.last_page" :page="pagination?.current_page" :headers="headers" :items="carreras" @viewAction="verCarrera"  @update:page="paginationMethod">
            <template  v-slot:item.estado="{ item }">
                <v-chip :color="item.estado ? 'success' : 'error'">
                    {{ item.estado ? 'Activo' : 'Inactivo' }}
                </v-chip>
                </template>
                <template v-slot:item.acciones="{ item }">
                    <v-row>
                    <v-col cols="12" col-md="4" col-sm="3" >
                        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verCarrera(item)"></v-btn>

                        <!-- botones nuevos -->

                        <v-btn v-tooltip:bottom="'Editar'" class="ml-2" density="compact" icon="mdi-pencil" v-if="item.estado" @click="ActualizarCarrera(item)"></v-btn>
                        
                        <!-- ELIMINAR PENDIENTE -->
                        <v-btn v-tooltip:bottom="item.estado ? 'Desactivar' : 'Activar'" class="ml-2" density="compact" :icon="item.estado ? 'mdi mdi-cancel' : 'mdi mdi-check-circle'" :color="item.estado ? 'error' : 'success'" @click="cambiarEstado(item)"></v-btn>
    
                    </v-col>
                    </v-row>
                </template>
        </data-table-component>
        <v-dialog
    v-model="dialogVerCarrera"
    
        persistent
    >
    <v-card
    class="mx-auto my-8"
    elevation="4"
    width="500"
    rounded="lg"
>
    <v-card-item >
    <v-card-title>
        {{ carrera.name }}
    </v-card-title>

    </v-card-item>

    <v-card-text>
        {{ carrera.code }}    
    </v-card-text>
    <v-card-actions>
        <v-row>
            <v-col cols="12"  class="text-center">
                <v-btn rounded="lg" variant="outlined" color="error" text @click="dialogVerCarrera = false">Cerrar</v-btn>

            </v-col>
        </v-row>
    </v-card-actions>
    </v-card>
    </v-dialog>
    
    <!-- actualizar card nueva -->

    <v-dialog v-model="dialogActualizarCarrera" max-width="500" persistent>
    <v-card>
        <v-card-title>Editar carrera</v-card-title>
        <v-card-text>
            <v-form ref="formEditar">
                <v-text-field
                    v-model="form.name"
                    label="Nombre"
                    :rules="[v => !!v?.trim() || 'El nombre es obligatorio']"
                />
                <v-text-field v-model="form.code" label="Código" />
            </v-form>
        </v-card-text>
        <v-card-actions>
            <v-btn @click="dialogActualizarCarrera = false">Cancelar</v-btn>
            <v-btn color="primary" :loading="guardando" @click="guardarEdicion">Guardar</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

<!-- crear card nueva -->
    <v-dialog v-model="dialogCrearCarrera" max-width="500" persistent>
    <v-card>
        <v-card-title>Crear carrera</v-card-title> 
        <v-card-text>
            <v-form ref="formNueva">
                <v-text-field
                    v-model="nuevaCarrera.name"
                    label="Nombre"
                    :rules="[v => !!v?.trim() || 'El nombre es obligatorio', v => v?.trim().length>= 3 || 'Minimo 3 caracteres']"
                />
                <v-text-field v-model="nuevaCarrera.code" label="Código" />
            </v-form> 
        </v-card-text>
        <v-card-actions>
            <v-btn @click="dialogCrearCarrera = false">Cancelar</v-btn>
            <v-btn color="primary" :loading="creando" @click="guardarNueva">Guardar</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

    </div>
</template>

<script setup>
import {ref,onMounted,watch,onBeforeUnmount} from 'vue'
import DataTableComponent from '../../components/DataTableComponent.vue';
import {useCarreras} from './composable/carreraComposable'
import TitleComponent from '../../components/TitleComponent.vue';
import { eliminarcarreras } from '../../services/carrera/carrera.service.js';
const acciones = ref(true);

// crear carrers
const dialogCrearCarrera = ref(false);
const nuevaCarrera = ref({name:'', code:''});
const formNueva = ref(null);
const creando = ref(false);

const dialogVerCarrera = ref(false);
//agregado
const dialogActualizarCarrera = ref(false);

//agregado
const form = ref({id:null, name:'', code:''});
const formEditar = ref(null);
const guardando = ref(false)
//agregado para buscar carreras
const search = ref('')
let searchTimeout
const filtros = () => ({name: search.value?.trim() || undefined})

const paginationMethod = async(page) => {
    await obtenerCarreras(page, filtros())
}

watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        obtenerCarreras(1, filtros())
    }, 300)
})

onBeforeUnmount(() => clearTimeout(searchTimeout))

//agragdo
const carrera = ref(null);
const {carreras, pagination,obtenerCarreras, actualizarCarreras, eliminarCarreras, crearCarreras} = useCarreras();
const headers = ref([
        { title: 'Name', key: 'name' },
        { title: 'Code', key: 'code' },
        { title: 'Estado', key: 'estado' },
    ]);
acciones.value ? headers.value.push({ title: 'Acciones', key: 'acciones' }) : null;


const verCarrera = (item) =>{
    dialogVerCarrera.value = true;
    carrera.value = item;
}
//actualizar carrera
const ActualizarCarrera = (item) =>{
    //agregado
    form.value = { id: item.id, name: item.name, code: item.code}
    dialogActualizarCarrera.value = true;
    carrera.value = item;
}

//guardar edicion en el form
const guardarEdicion = async() =>{
    const { valid } = await formEditar.value.validate();
    if (!valid) return;

    guardando.value = true;
    
    const ok = await actualizarCarreras(form.value.id, {
        name: form.value.name,
        code: form.value.code
    });
    guardando.value = false;

    if (ok) {
        dialogActualizarCarrera.value = false;
        await obtenerCarreras();
    }
}
//elimnar carrera cambiar estado activo incactivo
const cambiarEstado = async(item) =>{
    const ok = await eliminarCarreras(item.id,);
    if (ok){
        await obtenerCarreras();
    }
}

//crear carreras
const abrirCrear = () =>{
    nuevaCarrera.value = {name:'', code:''};
    dialogCrearCarrera.value = true;
}

const guardarNueva = async() =>{
    const { valid } = await formNueva.value.validate();
    if (!valid) return;

    creando.value = true;
    const ok = await crearCarreras({
        name: nuevaCarrera.value.name,
        code: nuevaCarrera.value.code
    });
    creando.value = false;

    if(ok) {
        dialogCrearCarrera.value =false;
        await obtenerCarreras()
    }
}

//buscar carreras seguir despues
const buscarcarreras= (e)=>{
    console.log(e.target.value);
    
}

onMounted(async()=>{
    await obtenerCarreras();
    console.log(pagination.value);
})

</script>