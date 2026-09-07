import api from "../../utils/axios.js";

const login = async (form) => {
    try{
        const response = await api.post('auth/login', form);
        return response?.data;
    }catch(error){
        console.error(error);
    }
}

const logout = async () => await api.post('auth/logout')
export {
    login,
    logout
}
