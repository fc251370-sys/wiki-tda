import {defineStore} from 'pinia'
//importamos
import { tags, actualizarTag, eliminarTag, crearTag } from '../../services/wiki/tag.service.js'

export const useTagStore = defineStore('tags',{
    state:()=>({
        tags:[],
        pagination:{},
    }),
    actions:{
        async getTags(params = {}){
            try {
                const response = await tags(params);
                console.log(response);
                
                this.tags = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
                
            }
        },
        //eliminar y actualizar carreras
        async actualizarTags(id, data){
            try {
                const response = await actualizarTag(id, data);
                console.log(response);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },

        async eliminarTags(id){
            try {
                const response = await eliminarTag(id);
                console.log(response);

                return response.status
            } catch(error){
                console.log(error);
            }
        },

        async crearTags(data){
            try {
                const response = await crearTag(data);
                return response.status
            } catch (error) {
                console.log(error);
                return null;
            }
        },
    }
})  
