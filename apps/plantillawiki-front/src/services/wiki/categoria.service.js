import api from '../../utils/axios'

const categorias = async(params = {})=> await api.get('categorias', {params})
const actualizarCategoria = async (id, data) => await api.put(`categorias/${id}`, data)
const eliminarCategoria = async (id) => await api.delete(`categorias/${id}`)
const crearCategoria = async (data) => await api.post(`categorias/crear`, data)


export {
    categorias,
    actualizarCategoria,
    eliminarCategoria,
    crearCategoria,
}