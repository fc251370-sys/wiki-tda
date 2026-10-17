export const menuItems = [
    {
        title: 'Dashboard',
        icon: 'mdi-view-dashboard',
        to: '/admin',
        roles:['admin','user','viewer']
    },
    {
        title: 'Carrera',
        icon: 'mdi-view-dashboard',
        to: '/carrera',
        roles:['admin','user','viewer']
    },
    {
        title: 'Evento',
        icon: 'mdi-view-dashboard',
        to: '/evento',
        roles:['admin','user','viewer']
    },
    {
        title: 'Proyecto',
        icon: 'mdi-view-dashboard',
        to: '/proyecto',
        roles:['admin','user','viewer']
    },
    {
        title: 'Wiki',
        icon: 'mdi-view-dashboard',
        to: '/wiki',
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