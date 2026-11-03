import {defineStore} from 'pinia'
import {obtenerUsuarios} from '../../services/users/users.service'

export const useUserStore = defineStore('users',{
    state:()=>({
        users:[],
        paginacion:{}
    }),
    actions:{
        async getUsers(page,params){
            const response = await obtenerUsuarios(page,params)
            this.users = response.data.data
            this.paginacion = response.data.pagination
            return response.data.status
        }
    }
})