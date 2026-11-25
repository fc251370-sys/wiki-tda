import {defineStore} from 'pinia'

import {
    proyectos,
    actualizarProyecto,
    eliminarProyecto,
    activarProyecto,
    crearProyecto,
    subirArchivo,
    actualizarArchivo,
    visualizarArchivo,
    descargarArchivo,
    eliminarArchivo,
} from './proyecto.service.js'

export const useProyectoStore = defineStore('proyectos',{
    state:()=>({
        proyectos:[],
        pagination:{},
    }),
    actions:{
        async getProyectos(params = {}){
            try{
                const response = await proyectos(params);

                this.proyectos = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};

                return response.status;
            } catch (error){
                console.log(error);
            }
        },

        async actualizarProyecto(id, data){
            try{
                const response = await actualizarProyecto(id, data);

                return response.status;
            } catch (error){
                console.log(error);
            }
        },
        async eliminarProyecto(id){
            try{
                const response = await eliminarProyecto(id);

                return response.status;
            } catch(error){
                console.log(error);
            }
        },

        async activarProyecto(id){
            try {
                const response = await activarProyecto(id);

                return response.status;
            } catch (error) {
                console.log(error);
            }
        }, 

        async crearProyecto(data){
            try {
                const response = await crearProyecto(data);

                return response.data;
            } catch (error) {
                console.log(error);
                return null;
            }
        },
        async subirArchivo(projectId, archivo) {
            try {
                const response = await subirArchivo(projectId, archivo)
                return response.status
            } catch (error) {
                console.log(error)
                return null
            }
        },
        
        async actualizarArchivo(id, archivo) {
            try {
                const response = await actualizarArchivo(id, archivo)
                console.log('Datos recibidos:', response.data)
                return response.status
            } catch (error) {
                console.log('Error completo:', error)
                console.log('Respuesta de Laravel:', error.response?.data)
                return null
            }
        },
        async visualizarArchivo(id) {
            try {
                const response = await visualizarArchivo(id)
                return response.data
            } catch (error) {
                console.log(error)
                return null
            }
        },

        async descargarArchivo(id) {
            try {
                const response = await descargarArchivo(id)
                return response
            } catch (error) {
                console.log(error)
                return null
            }
        },

        async eliminarArchivo(id) {
            try {
                const response = await eliminarArchivo(id)
                return response.status
            } catch (error) {
                console.log(error)
                return null
            }
        },
     }

})