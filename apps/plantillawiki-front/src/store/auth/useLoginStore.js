import {defineStore} from 'pinia';
import {login, logout as requestLogout} from '../../services/auth/login.service.js';
import {jwtDecode} from 'jwt-decode';
import { clearAuthSession } from '../../utils/authSession.js';

export const useLoginStore = defineStore('login', {
  state: () => ({
    formulario: {
      email: '',
      password: ''
    }
  }),
  actions: {
    async submit() {
      const response = await login(this.formulario);
      const data = response?.data;

      if (!data?.token) {
        throw new Error('La respuesta de inicio de sesión no contiene un token.');
      }

      const decodedToken = jwtDecode(data.token);
      localStorage.setItem('roles', JSON.stringify(decodedToken.roles ?? []));
      localStorage.setItem('permissions', JSON.stringify(decodedToken.permissions ?? []));
      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));
      return response.status;
    },

    async logout() {
      try {
        return await requestLogout();
      } finally {
        clearAuthSession();
      }
    },
  }
})