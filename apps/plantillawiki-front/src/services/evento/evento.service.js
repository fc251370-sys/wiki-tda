import api from '../../utils/axios'

const eventos = async(params = {})=> await api.get('eventos', {params})
const crearEvento = async(data) => await api.post('eventos/crear', data)
const actualizarEvento = async (id, data) => await api.put(`eventos/${id}`, data)
const cambiarEstadoEvento = async (id) => await api.delete(`eventos/${id}`)
const alternarActivoEvento = async (id) => await api.patch(`eventos/${id}/activar`)


export {
    eventos,
    crearEvento,
    actualizarEvento,
    cambiarEstadoEvento,
    alternarActivoEvento
}