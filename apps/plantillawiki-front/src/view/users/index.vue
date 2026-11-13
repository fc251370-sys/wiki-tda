<template>
    <div>
        <title-component :title="'Listado de usuarios'"></title-component>
        <v-row class="d-flex justify-end">
            <v-col cols="12" md="4" >
                <v-text-field v-model="search" variant="outlined" clearable density="compact" placeholder="Buscar" rounded-xl append-inner-icon="mdi-magnify"></v-text-field>
            </v-col>
            
        </v-row>
        <data-table-component :headers="headers" :items="usuarios" :totalPage="paginacion.lastPage" :page="paginacion.currentPage"   @update:page="changePage">
            
            <template v-slot:item.carnet="{item}">
                <p >{{ item.carnet?item.carnet:'-' }}</p>
            </template>
            <template v-slot:item.acciones="{ item }">
                    <v-row>
                    <v-col cols="12" col-md="4" col-sm="3" >
                        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verUsuario(item)"></v-btn>
                        <v-btn v-tooltip:bottom="'Editar'"class="ml-2" density="compact" icon="mdi-pencil" ></v-btn>
                        <v-btn v-tooltip:bottom="'Eliminar'" class="ml-2" density="compact" icon="mdi-trash-can-outline"></v-btn>
                    </v-col>
                    </v-row>
                </template>
        </data-table-component>
    </div>
</template>
<script setup>
import {onBeforeUnmount, onMounted, ref, watch} from 'vue'
import TitleComponent from '../../components/TitleComponent.vue';
import DataTableComponent from '../../components/DataTableComponent.vue';
import { useUsers } from './composable/userComposable.js';
const {headers, usuarios, paginacion, getUsuarios} = useUsers()
const search = ref('')
let searchTimeout
//console.log(headers);
const verUsuario=(item)=>{
   // console.log(item);
    
}
const changePage = async(page)=>{
   await getUsuarios(page, { search: search.value.trim() })
    
}

watch(search, (value) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        getUsuarios(1, { search: value.trim() })
    }, 300)
})

onMounted(async()=>{
    await getUsuarios()
})

onBeforeUnmount(() => clearTimeout(searchTimeout))
</script>