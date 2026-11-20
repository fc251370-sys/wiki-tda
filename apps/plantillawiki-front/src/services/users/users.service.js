import api from '../../utils/axios'

const obtenerUsuarios = async(page,params)=>{
    const response = await api.get('auth/users',{
        params:{
            page,
            perPage: 10,
            ...params
        }
    })
    return response
}

export {
    obtenerUsuarios
}