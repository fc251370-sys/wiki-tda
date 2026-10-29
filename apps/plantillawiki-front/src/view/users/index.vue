<template>
    <div>
        <title-component :title="'Listado de usuarios'"></title-component>
        <v-row class="d-flex justify-end">
            <v-col cols="12" md="4" >
                <v-text-field variant="outlined" clearable density="compact" rounded-xl append-inner-icon="mdi-magnify" @input="buscarusuario"></v-text-field>
            </v-col>
            
        </v-row>
        <data-table-component :headers="headers" :items="items">
            <template v-slot:item.acciones="{ item }">
                    <v-row>
                    <v-col cols="12" col-md="4" col-sm="3" >
                        <v-btn v-tooltip:bottom="'Ver'" density="compact" icon="mdi-eye" @click="verUsuario(item)"></v-btn>
                        <v-btn v-tooltip:bottom="'Editar'"class="ml-2" density="compact" icon="mdi-pencil" v-if="item.estado"></v-btn>
                        <v-btn v-tooltip:bottom="'Eliminar'" class="ml-2" density="compact" icon="mdi-trash-can-outline"></v-btn>
                    </v-col>
                    </v-row>
                </template>
        </data-table-component>
    </div>
</template>
<script setup>
import TitleComponent from '../../components/TitleComponent.vue';
import DataTableComponent from '../../components/DataTableComponent.vue';
import { useUsers } from './composable/userComposable.js';
const {headers, items} = useUsers()
console.log(headers);
const verUsuario=(item)=>{
    console.log(item);
    
}
const buscarusuario= (e)=>{
    console.log(e.target.value);
    
}
</script>