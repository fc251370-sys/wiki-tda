import {ref, computed} from 'vue'
import { useUserStore } from '../../../store/users/useuserStore' 
export const useUsers =  ()=>{
    const store = useUserStore()
    const usuarios = computed(()=>store.users)
    const paginacion = computed(()=>store.paginacion)

    const obtenerUsers = async(page = 1, filtros = {})=>{
        try {
        const response = await store.getUsers({page, ...filtros});
            console.log(response);
            
        } catch (error) {
            console.error(error);
        }
    }

    //actualizar y eliminar carreras
    const actualizarUsers = async(id, data)=>{
        try {
            const status = await store.actualizarUsers(id, data);
            return status === 200;
        } catch (error){
            console.error(error);
            return false;
        }
    }

    const eliminarUsers = async(id)=>{
        try {
            const status = await store.eliminarUsers(id);
            return status === 200;
        } catch (error){
            console.log(error);
            return false;
        }
    }

    //crear carreras
    const crearUsers = async(data)=>{
        try {
            const status = await store.crearUsers(data);
            return status === 201;
        } catch (error) {
            console.error(error);
            return false;
        }
    }
    
    return {
        usuarios,
        paginacion,
        obtenerUsers,
        actualizarUsers,
        eliminarUsers,
        crearUsers,
    }
}