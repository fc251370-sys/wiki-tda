import { defineStore } from "pinia"
import { eventos, crearEvento, actualizarEvento, cambiarEstadoEvento, alternarActivoEvento } from "../../services/evento/evento.service.js"
import { obtenerUsuarios } from '../../services/users/users.service.js'

export const useEventoStore = defineStore('eventos', {
    state: () => ({
        eventos: [],
        pagination: {},
        coordinadores: [],
    }),
    actions: {
        async getEventos(params = {}) {
            try {
                const response = await eventos(params);
                this.eventos = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        // usuarios con rol coordinador para el selector (hasta 100)
        async getCoordinadores() {
            try {
                const response = await obtenerUsuarios(1, { perPage: 100, role: 'coordinador' });
                this.coordinadores = response.data.data ?? [];
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async crearEvento(data) {
            try {
                const response = await crearEvento(data);
                return response.status;
            } catch (error) {
                console.log(error);
                return null;
            }
        },

        async actualizarEvento(id, data) {
            try {
                const response = await actualizarEvento(id, data);
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async cambiarEstadoEvento(id) {
            try {
                const response = await cambiarEstadoEvento(id);
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async alternarActivoEvento(id) {
            try {
                const response = await alternarActivoEvento(id);
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },
    }
})