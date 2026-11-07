import api from '../../utils/axios'
const carreras = async(params = {})=> await api.get('carreras', {params})
const actualizarcarreras = async (id, data) => await api.put(`carreras/${id}`, data)
const eliminarcarreras = async (id) => await api.delete(`carreras/${id}`)
const crearcarreras = async (data) => await api.post(`carreras/crear`, data)


export {
    carreras,
    actualizarcarreras,
    eliminarcarreras,
    crearcarreras,
}