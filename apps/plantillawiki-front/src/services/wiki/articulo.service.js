import api from '../../utils/axios'

const articulos = async(params = {})=> await api.get('articulos', {params})
const actualizarArticulo = async (id, data) => await api.put(`articulos/${id}`, data)
const eliminarArticulo = async (id) => await api.delete(`articulos/${id}`)
const crearArticulo = async (data) => await api.post(`articulos/crear`, data)


export {
    articulos,
    actualizarArticulo,
    eliminarArticulo,
    crearArticulo,
}