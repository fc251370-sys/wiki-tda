export const configurationRouter = [
     {
        path: "configuracion",
        name: "Configuracion",
        component: () => import('./index.vue'),
        meta: {
            roles: ['admin'],
        },
    },
]