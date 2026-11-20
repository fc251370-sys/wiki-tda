import api from '../../utils/axios'

const users = async(params = {})=> await api.get('users', {params})
const actualizarusers = async (id, data) => await api.put(`users/${id}`, data)
const eliminarusers = async (id) => await api.delete(`users/${id}`)
const crearusers = async (data) => await api.post(`users/crear`, data)


export {
    users,
    actualizarusers,
    eliminarusers,
    crearusers,
}