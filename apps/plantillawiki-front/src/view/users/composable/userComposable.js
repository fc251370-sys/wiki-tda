import { computed } from 'vue'
import { useUserStore } from '../../../store/users/useuserStore'

export const useUsers = () => {
    const store = useUserStore()
    const usuarios = computed(() => store.users)
    const paginacion = computed(() => store.pagination)

    const obtenerUsers = async (page = 1, filtros = {}) => {
        try {
            await store.getUsers({ page, perPage: 10, ...filtros });
        } catch (error) {
            console.error(error);
        }
    }

    // actualizar, cambiar estado y crear usuarios
    const actualizarUsers = async (id, data) => {
        try {
            const status = await store.actualizarUsers(id, data);
            return status === 200;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    const eliminarUsers = async (id) => {
        try {
            const status = await store.eliminarUsers(id);
            return status === 200;
        } catch (error) {
            console.log(error);
            return false;
        }
    }

    const crearUsers = async (data) => {
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