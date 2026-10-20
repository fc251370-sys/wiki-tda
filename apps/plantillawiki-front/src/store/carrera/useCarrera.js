import {defineStore} from 'pinia'
import {carreras} from '../../services/carrera/carrera.service.js'

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
        }
    }
})