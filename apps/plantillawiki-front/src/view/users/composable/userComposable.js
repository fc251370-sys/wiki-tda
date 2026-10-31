import {ref, computed} from 'vue'
import { useUserStore } from '../../../store/users/useuserStore'
export const useUsers =  ()=>{
    const headers = ref([
        {
            title:'Nombre',
            key:'nombre',
        },
        {
            title:'Correo electronico',
            key:'email',
        },
        {
            title:'Carnet',
            key:'carnet',
        },
        {
            title:'Acciones',
            key:'acciones'
        }
    ])
   
    const store = useUserStore()
    const usuarios = computed(()=>store.users)
    const paginacion = computed(()=>store.paginacion)

    const getUsuarios = async(page=1, params={})=>{
        return store.getUsers(page,params)
    }
    return {
        headers,
        usuarios,
        paginacion,
        getUsuarios
    }
}