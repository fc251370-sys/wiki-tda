import {defineStore} from 'pinia'
//importamos
import { categorias, actualizarCategoria, eliminarCategoria, crearCategoria } from '../../services/wiki/categoria.service.js'

export const useCategoriaStore = defineStore('categorias',{
    state:()=>({
        categorias:[],
        pagination:{},
    }),
    actions:{
        async getCategorias(params = {}){
            try {
                const response = await categorias(params);
                console.log(response);
                
                this.categorias = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
                
            }
        },
        //eliminar y actualizar carreras
        async actualizarCategoria(id, data){
            try {
                const response = await actualizarCategoria(id, data);
                console.log(response);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },

        async eliminarCategoria(id){
            try {
                const response = await eliminarCategoria(id);
                console.log(response);

                return response.status
            } catch(error){
                console.log(error);
            }
        },

        async crearCategoria(data){
            try {
                const response = await crearCategoria(data);
                return response.status
            } catch (error) {
                console.log(error);
                return null;
            }
        },
    }
})  
