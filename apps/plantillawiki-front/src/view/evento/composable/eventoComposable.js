import { useEventoStore } from "../../../store/evento/useEvento.js"
import { computed } from "vue"

export const useEventos = () => {
    const store = useEventoStore();
    const eventos = computed(() => store.eventos)
    const pagination = computed(() => store.pagination)
    const coordinadores = computed(() => store.coordinadores)

    const obtenerEventos = async (page = 1, filtros = {}) => {
        try {
            await store.getEventos({ page, ...filtros });
        } catch (error) {
            console.error(error);
        }
    }

    const obtenerCoordinadores = async () => {
        try {
            await store.getCoordinadores();
        } catch (error) {
            console.error(error);
        }
    }

    const crearEvento = async (data) => {
        try {
            const status = await store.crearEvento(data);
            return status === 201;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    const actualizarEvento = async (id, data) => {
        try {
            const status = await store.actualizarEvento(id, data);
            return status === 200;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    // deshabilitar / habilitar (alterna "estado")
    const cambiarEstadoEvento = async (id) => {
        try {
            const status = await store.cambiarEstadoEvento(id);
            return status === 200;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    // activar / desactivar el ciclo (alterna "is_active")
    const alternarActivoEvento = async (id) => {
        try {
            const status = await store.alternarActivoEvento(id);
            return status === 200;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    return {
        eventos,
        pagination,
        coordinadores,
        obtenerEventos,
        obtenerCoordinadores,
        crearEvento,
        actualizarEvento,
        cambiarEstadoEvento,
        alternarActivoEvento,
    }
}