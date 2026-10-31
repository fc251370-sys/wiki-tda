import {defineStore} from 'pinia'
//importamos
import { carreras, actualizarcarreras } from '../../services/carrera/carrera.service.js'


export const useCarreraStore = defineStore('carreras',{
    state:()=>({
        carreras:[],
        pagination:{},
    }),
    actions:{
        async getCarreras(){
            try {
                const response = await carreras();
                console.log(response);
                
                this.carreras = response.data.data;
                this.pagination = response.data.pagination;
                return response.status;
            } catch (error) {
                console.log(error);
                
            }
        },
        //eliminar y actualizar carreras
        async actualizarCarreras(id, data){
            try {
                const response = await actualizarcarreras(id, data);
                console.log(response);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },
    },
    
})