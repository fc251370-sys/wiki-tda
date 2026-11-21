import api from '../../utils/axios'

// Las rutas de usuarios están dentro del prefijo "auth" en el backend: /api/auth/users
const users = async (params = {}) => await api.get('auth/users', { params })
const actualizarusers = async (id, data) => await api.put(`auth/users/${id}`, data)
const eliminarusers = async (id) => await api.delete(`auth/users/${id}`)
const crearusers = async (data) => await api.post('auth/users/crear', data)

// Lo usa el módulo de eventos para cargar los coordinadores.
// No cambiar la firma (page, params) ni lo que devuelve.
const obtenerUsuarios = async (page, params) => {
    const response = await api.get('auth/users', {
        params: {
            page,
            perPage: 10,
            ...params
        }
    })
    return response
}

export {
    users,
    actualizarusers,
    eliminarusers,
    crearusers,
    obtenerUsuarios,
}