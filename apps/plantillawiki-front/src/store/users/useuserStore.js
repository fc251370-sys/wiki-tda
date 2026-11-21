import { defineStore } from 'pinia'

import { users, actualizarusers, eliminarusers, crearusers } from '../../services/users/users.service'

export const useUserStore = defineStore('users', {
    state: () => ({
        users: [],
        pagination: {},
    }),
    actions: {
        async getUsers(params = {}) {
            try {
                const response = await users(params);

                this.users = response.data.data ?? [];
                this.pagination = response.data.pagination ?? {};
                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        // actualizar, cambiar estado y crear usuarios
        async actualizarUsers(id, data) {
            try {
                const response = await actualizarusers(id, data);

                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async eliminarUsers(id) {
            try {
                const response = await eliminarusers(id);

                return response.status;
            } catch (error) {
                console.log(error);
            }
        },

        async crearUsers(data) {
            try {
                const response = await crearusers(data);
                return response.status;
            } catch (error) {
                console.log(error);
                return null;
            }
        },
    }
})