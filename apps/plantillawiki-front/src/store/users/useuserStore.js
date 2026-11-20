import {defineStore} from 'pinia'

import { users, actualizarusers, eliminarusers,crearusers } from '../../services/users/users.service'


export const useUserStore = defineStore('users',{
    state:()=>({
        users:[],
        pagination:{},
    }),
    actions:{
        async getUsers(params = {}){
            try {
                const response = await users(params);
                console.log(response);
                
                this.users = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
                
            }
        },
        //eliminar y actualizar carreras
        async actualizarusers(id, data){
            try {
                const response = await actualizarusers(id, data);
                console.log(response);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },

        async eliminarusers(id){
            try {
                const response = await eliminarusers(id);
                console.log(response);

                return response.status
            } catch(error){
                console.log(error);
            }
        },

        async crearusers(data){
            try {
                const response = await crearusers(data);
                return response.status
            } catch (error) {
                console.log(error);
                return null;
            }
        },
    }
})  