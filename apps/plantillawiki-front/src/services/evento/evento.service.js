import api from '../../utils/axios'

// eventos
const eventos = async (params = {}) => await api.get('eventos', { params })
const crearEvento = async (data) => await api.post('eventos/crear', data)
const actualizarEvento = async (id, data) => await api.put(`eventos/${id}`, data)
// el DELETE del backend no borra: alterna el campo "estado"
const cambiarEstadoEvento = async (id) => await api.delete(`eventos/${id}`)
// alterna is_active (solo un evento puede estar activo a la vez)
const alternarActivoEvento = async (id) => await api.patch(`eventos/${id}/activar`)

// materias de un evento (rutas anidadas: eventos/{eventId}/materias)
const materias = async (eventId, params = {}) => await api.get(`eventos/${eventId}/materias`, { params })
const crearMateria = async (eventId, data) => await api.post(`eventos/${eventId}/materias/crear`, data)
const actualizarMateria = async (eventId, id, data) => await api.put(`eventos/${eventId}/materias/${id}`, data)
// el DELETE del backend no borra: alterna el campo "estado"
const cambiarEstadoMateria = async (eventId, id) => await api.delete(`eventos/${eventId}/materias/${id}`)

export {
    eventos,
    crearEvento,
    actualizarEvento,
    cambiarEstadoEvento,
    alternarActivoEvento,
    materias,
    crearMateria,
    actualizarMateria,
    cambiarEstadoMateria,
}