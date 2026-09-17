export const routerAuth = [
    {
        path: "/login",
        name: "Login",
        component: () => import('./Login/Login.vue'),
        meta: {
            layout: "guest",
            guestOnly: true
        },
    },
]