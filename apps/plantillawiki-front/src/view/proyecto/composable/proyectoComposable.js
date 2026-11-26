import { useProyectoStore } from "../../../store/proyecto/useProyecto.js";
import {computed} from 'vue'

export const useProyectos = ()=>{
    const store = useProyectoStore();

    const proyectos = computed(()=>store.proyectos)
    const pagination = computed(()=>store.pagination)

    //separa los proyectos que están activos y los desactivados
    const proyectosActivos = computed(()=>
        proyectos.value.filter(proyecto => proyecto.estado === true)
    )

    const proyectosDesactivados = computed(()=>
        proyectos.value.filter(proyecto =>proyecto.estado === false)
    )

    const obtenerProyectos = async(page = 1, filtros ={})=>{
        try {
            const response = await store.getProyectos({page, ...filtros});
            console.log(response);
        } catch (error) {
            console.error(error);
        }
    }   

        const actualizarProyecto = async(id, data)=>{
            try {
                const status = await store.actualizarProyecto(id, data);
                return status === 200;
            } catch (error) {
                console.error(error);
                return false;
            }
        }

        const eliminarProyecto = async(id)=>{
            try {
                const status = await store.eliminarProyecto(id);
                return status === 200;
            } catch (error) {
                console.log(error);
                return false;
            }
        }

        const activarProyecto = async(id)=>{
            try {
                const status = await store.activarProyecto(id);
                return status === 200;
            } catch (error) {
                console.log(error);
                return false;
            }
        }

        const crearProyecto = async(data)=>{
            try {
                const respuesta = await store.crearProyecto(data);
                return respuesta;
            } catch (error) {
                console.error(error);
                return null;
            }
        }
        const subirArchivo = async (projectId, archivo) => {
            try {
                const status = await store.subirArchivo(projectId, archivo)
                return status === 201
            } catch (error) {
                console.error(error)
                return false
            }
        }
        const actualizarArchivo = async (id, archivo) => {
            try {
                const status = await store.actualizarArchivo(id, archivo)
                return status === 200
            } catch (error) {
                console.error(error)
                return false
            }
        }

                const visualizarArchivo = async (id) => {
            try {
                return await store.visualizarArchivo(id)
            } catch (error) {
                console.error(error)
                return null
            }
        }

        const descargarArchivo = async (id) => {
            try {
                return await store.descargarArchivo(id)
            } catch (error) {
                console.error(error)
                return null
            }
        }
        
        const eliminarArchivo = async (id) => {
            try {
                return await store.eliminarArchivo(id)
            } catch (error) {
                console.error(error)
                return null
            }
        }
    
    
    return {
        proyectos,
        pagination,
        proyectosActivos,
        proyectosDesactivados,
        obtenerProyectos,
        actualizarProyecto,
        eliminarProyecto,
        crearProyecto,
        activarProyecto,
        subirArchivo,
        actualizarArchivo,
        visualizarArchivo,
        descargarArchivo,
        eliminarArchivo
    }
}
