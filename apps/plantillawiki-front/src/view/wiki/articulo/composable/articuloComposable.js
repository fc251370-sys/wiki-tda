import { useArticuloStore } from '../../../../store/wiki/useArticulo.js'
import { computed } from 'vue'

export const useArticulos = () => {
    const store = useArticuloStore()
    const articulos = computed(() => store.articulos)
    const pagination = computed(() => store.pagination)

    const obtenerArticulos = async (page = 1, filtros = {}) => {
        try {
            await store.getArticulos({ page, ...filtros })
        } catch (error) {
            console.error(error)
        }
    }

    const actualizarArticulos = async (id, data) => {
        try {
            const status = await store.actualizarArticulos(id, data)
            return status === 200
        } catch (error) {
            console.error(error)
            return false
        }
    }

    const eliminarArticulos = async (id) => {
        try {
            const status = await store.eliminarArticulos(id)
            return status === 200
        } catch (error) {
            console.error(error)
            return false
        }
    }

    const crearArticulos = async (data) => {
        try {
            const status = await store.crearArticulos(data)
            return status === 201
        } catch (error) {
            console.error(error)
            return false
        }
    }

    return {
        articulos,
        pagination,
        obtenerArticulos,
        actualizarArticulos,
        eliminarArticulos,
        crearArticulos,
    }
}