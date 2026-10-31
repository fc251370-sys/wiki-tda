import api from "../../utils/axios.js";

const login = async (form) => {
    const response = await api.post('auth/login', form);
    return response.data;
}

const logout = async () => api.post('auth/logout')
export {
    login,
    logout
}
