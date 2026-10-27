<template>
    <div>
        
        <data-table-component :totalPage="pagination.last_page" :page="pagination.current_page" :headers="headers" :items="carreras" @viewAction="verCarrera"  @update:page="paginationMethod">
             <template  v-slot:item.estado="{ item }">
                <v-chip :color="item.estado ? 'success' : 'error'">
                    {{ item.estado ? 'Activo' : 'Inactivo' }}
                </v-chip>
                </template>
                <template v-slot:item.acciones="{ item }">
                    <v-row>
                    <v-col cols="12" col-md="4" col-sm="3" >
                        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verCarrera(item)"></v-btn>
                        <v-btn v-tooltip:bottom="'Editar'"class="ml-2" density="compact" icon="mdi-pencil" v-if="item.estado"></v-btn>
                        <v-btn v-tooltip:bottom="'Eliminar'" class="ml-2" density="compact" icon="mdi-trash-can-outline"></v-btn>
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
    </div>
</template>

<script setup>
import {ref,onMounted} from 'vue'
import DataTableComponent from '../../components/DataTableComponent.vue';
import {useCarreras} from './composable/carreraComposable'
const acciones = ref(true);
const dialogVerCarrera = ref(false);
const carrera = ref(null);
const {carreras, pagination,obtenerCarreras} = useCarreras();
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



onMounted(async()=>{
    await obtenerCarreras();
    console.log(pagination.value);
    
})

</script>