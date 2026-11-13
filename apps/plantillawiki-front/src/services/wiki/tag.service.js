import api from '../../utils/axios'

const tags = async(params = {})=> await api.get('tags', {params})
const actualizarTag = async (id, data) => await api.put(`tags/${id}`, data)
const eliminarTag = async (id) => await api.delete(`tags/${id}`)
const crearTag = async (data) => await api.post(`tags/crear`, data)


export {
    tags,
    actualizarTag,
    eliminarTag,
    crearTag,
}