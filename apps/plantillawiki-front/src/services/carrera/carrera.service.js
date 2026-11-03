import api from '../../utils/axios'
const carreras = async()=> await api.get('carreras')
const actualizarcarreras = async (id, data) => await api.put(`carreras/${id}`, data)
const eliminarcarreras = async (id) => await api.delete(`carreras/${id}`)

export {
    carreras,
    actualizarcarreras,
    eliminarcarreras,
}