import {defineStore} from 'pinia'
//importamos
import { articulos, actualizarArticulo, eliminarArticulo, crearArticulo } from '../../services/wiki/articulo.service.js'

export const useArticuloStore = defineStore('articulos',{
    state:()=>({
        articulos:[],
        pagination:{},
    }),
    actions:{
        async getArticulos(params = {}){
            try {
                const response = await articulos(params);
                console.log(response);
                
                this.articulos = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
                
            }
        },
        //eliminar y actualizar carreras
        async actualizarArticulos(id, data){
            try {
                const response = await actualizarArticulo(id, data);
                console.log(response);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },

        async eliminarArticulos(id){
            try {
                const response = await eliminarArticulo(id);
                console.log(response);

                return response.status
            } catch(error){
                console.log(error);
            }
        },

        async crearArticulos(data){
            try {
                const response = await crearArticulo(data);
                return response.status
            } catch (error) {
                console.log(error);
                return null;
            }
        },
    }
})  
