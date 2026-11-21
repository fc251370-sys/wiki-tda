import { defineStore } from 'pinia'
import {
    eventos,
    crearEvento,
    actualizarEvento,
    cambiarEstadoEvento,
    alternarActivoEvento,
    materias,
    crearMateria,
    actualizarMateria,
    cambiarEstadoMateria,
} from '../../services/evento/evento.service.js'
import { obtenerUsuarios } from '../../services/users/users.service.js'
import { carreras as obtenerCarreras } from '../../services/carrera/carrera.service.js'

export const useEventoStore = defineStore('eventos', {
    state: () => ({
        eventos: [],
        pagination: {},
        coordinadores: [],
        materias: [],
        paginationMaterias: {},
        carreras: [],
        docentes: [],
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

        // materias del evento
        async getMaterias(eventId, params = {}) {
            try {
                if ((params.page ?? 1) === 1) this.materias = [];
                const response = await materias(eventId, params);
                this.materias = response.data.data ?? [];
                this.paginationMaterias = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        // carreras habilitadas para el selector (recorre las paginas)
        async getCarrerasLista() {
            try {
                let page = 1;
                let last = 1;
                let todas = [];
                do {
                    const response = await obtenerCarreras({ page, estado: true });
                    todas = todas.concat(response.data.data ?? []);
                    last = response.data.pagination?.last_page ?? 1;
                    page++;
                } while (page <= last && page <= 20);
                this.carreras = todas;
            } catch (error) {
                console.log(error);
            }
        },

        // usuarios con rol teacher para el selector (hasta 100)
        async getDocentes() {
            try {
                const response = await obtenerUsuarios(1, { perPage: 100, role: 'teacher' });
                this.docentes = response.data.data ?? [];
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async crearMateria(eventId, data) {
            try {
                const response = await crearMateria(eventId, data);
                return response.status;
            } catch (error) {
                console.log(error);
                return null;
            }
        },

        async actualizarMateria(eventId, id, data) {
            try {
                const response = await actualizarMateria(eventId, id, data);
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async cambiarEstadoMateria(eventId, id) {
            try {
                const response = await cambiarEstadoMateria(eventId, id);
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