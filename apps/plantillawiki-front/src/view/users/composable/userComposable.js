import {ref} from 'vue'
export const useUsers =  ()=>{
    const headers = ref([
        {
            title:'Nombre',
            key:'name',
        },
        {
            title:'Correo electronico',
            key:'email',
        },
        {
            title:'Carnet',
            key:'carnet_or_code',
        },
        {
            title:'Acciones',
            key:'acciones'
        }
    ])
    const items = ref([
        {
            name:'Irwin',
            email:'irwin@yopmail.com',
            carnet_or_code:'AA00000',
            acciones:''
        }
    ])

    return {
        headers,
        items
    }
}