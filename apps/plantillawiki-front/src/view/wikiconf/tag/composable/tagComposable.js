import {useTagStore} from '../../../../store/wiki/useTag.js'
import {computed} from 'vue'
export const useTags = ()=>{
    const store = useTagStore();
    const tags = computed(()=>store.tags)
    const pagination = computed(()=>store.pagination)

    const obtenerTags = async(page = 1, filtros = {})=>{
        try {
        const response = await store.getTags({page, ...filtros});
            console.log(response);
            
        } catch (error) {
            console.error(error);
        }
    }

    //actualizar y eliminar carreras
    const actualizarTags = async(id, data)=>{
        try {
            const status = await store.actualizarTags(id, data);
            return status === 200;
        } catch (error){
            console.error(error);
            return false;
        }
    }

    const eliminarTags = async(id)=>{
        try {
            const status = await store.eliminarTags(id);
            return status === 200;
        } catch (error){
            console.log(error);
            return false;
        }
    }

    //crear carreras
    const crearTags = async(data)=>{
        try {
            const status = await store.crearTags(data);
            return status === 201;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    return {
        tags,
        pagination, 
        obtenerTags,
        actualizarTags,
        eliminarTags,
        crearTags,
    }
}