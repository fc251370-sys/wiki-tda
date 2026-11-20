import { useCategoriaStore } from '../../../../store/wiki/useCategoria.js'
import { computed } from 'vue'

export const useCategorias = () => {
  const store = useCategoriaStore()
  const categorias = computed(() => store.categorias)
  const pagination = computed(() => store.pagination)

  // Obtener listado con paginación y filtros
  const obtenerCategorias = async (page = 1, filtros = {}) => {
    try {
      const response = await store.getCategorias({ page, ...filtros })
      return response
    } catch (error) {
      console.error(error)
    }
  }

  // Actualizar categoría (nombre del store: actualizarCategoria)
  const actualizarCategorias = async (id, data) => {
    try {
      const status = await store.actualizarCategoria(id, data)
      return status === 200
    } catch (error) {
      console.error(error)
      return false
    }
  }

  // Eliminar / cambiar estado (nombre del store: eliminarCategoria)
  const eliminarCategorias = async (id) => {
    try {
      const status = await store.eliminarCategoria(id)
      return status === 200
    } catch (error) {
      console.error(error)
      return false
    }
  }

  // Crear categoría (nombre del store: crearCategoria)
  const crearCategorias = async (data) => {
    try {
      const status = await store.crearCategoria(data)
      return status === 201
    } catch (error) {
      console.error(error)
      return false
    }
  }

  return {
    categorias,
    pagination,
    obtenerCategorias,
    actualizarCategorias,
    eliminarCategorias,
    crearCategorias,
  }
}