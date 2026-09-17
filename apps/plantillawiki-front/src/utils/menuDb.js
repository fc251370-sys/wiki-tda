export const menuItems = [
    {
        title: 'Dashboard',
        icon: 'mdi-view-dashboard',
        to: '/admin',
        roles:['admin','user','viewer']
    },
    {
        title: 'Seguridad',
        icon: 'mdi-shield-lock',
        roles:['admin','viewer'],
        children: [
            {
                title: 'Usuarios',
                to: '/admin/users',
                roles: [],
                permisos: []
            },
            {
                title: 'Roles',
                to: '/admin/roles',
                roles: [],
                permisos: ['create_rol','view_rol','edit_rol']
            },
           
        ]
    },
    {
        title: 'Configuración',
        icon: 'mdi-cog',
        to: '/admin/configuracion',
         roles:['admin','user','viewer']
    }
]