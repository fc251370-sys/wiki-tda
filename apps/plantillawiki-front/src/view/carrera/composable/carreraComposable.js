import {useCarreraStore} from '../../../store/carrera/useCarrera.js'
import {computed} from 'vue'
export const useCarreras = ()=>{
    const store = useCarreraStore();
    const carreras = computed(()=>store.carreras)
    const pagination = computed(()=>store.pagination)

    const obtenerCarreras = async(page = 1, filtros = {})=>{
        try {
        const response = await store.getCarreras({page, ...filtros});
            console.log(response);
            
        } catch (error) {
            console.error(error);
        }
    }

    //actualizar y eliminar carreras
    const actualizarCarreras = async(id, data)=>{
        try {
            const status = await store.actualizarCarreras(id, data);
            return status === 200;
        } catch (error){
            console.error(error);
            return false;
        }
    }

    const eliminarCarreras = async(id)=>{
        try {
            const status = await store.eliminarCarreras(id);
            return status === 200;
        } catch (error){
            console.log(error);
            return false;
        }
    }

    //crear carreras
    const crearCarreras = async(data)=>{
        try {
            const status = await store.crearCarreras(data);
            return status === 201;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    return {
        carreras,
        pagination, 
        obtenerCarreras,
        actualizarCarreras,
        eliminarCarreras,
        crearCarreras,
    }
}