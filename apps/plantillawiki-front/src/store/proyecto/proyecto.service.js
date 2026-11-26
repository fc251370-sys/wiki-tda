import api  from "../../utils/axios"

const proyectos = async(params = {}) => await api.get('proyectos', {params})
const crearProyecto = async(data)=>await api.post('proyectos/crear', data)
const actualizarProyecto =async(id, data)=>await api.put(`proyectos/${id}`,data)
const eliminarProyecto = async(id)=>await api.delete(`proyectos/${id}`)
const activarProyecto = async (id) => await api.put(`proyectos/${id}/activar`)
const eliminarArchivo = async(id) => await api.delete(`proyectos/archivos/${id}`)

const subirArchivo = async (projectId, archivo) => {
    const formData = new FormData()
    formData.append('file', archivo)

    return await api.post(
        `proyectos/${projectId}/archivos`,
        formData,
        {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }
    )
}

const actualizarArchivo = async (id, archivo) => {
    const formData = new FormData()
    formData.append('file', archivo)
    formData.append('_method', 'PUT')

    console.log('Formulario enviado:', [...formData.entries()])

    return await api.post(
        `proyectos/archivos/${id}`,
        formData,
        {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }
    )
}

const visualizarArchivo = async (id) => {
    return await api.get(`proyectos/archivos/${id}`, {
        responseType: 'blob'
    })
}

const descargarArchivo = async (id) => {
    return await api.get(`proyectos/archivos/${id}/descargar`, {
        responseType: 'blob'
    })
}

export {
    proyectos,
    crearProyecto,
    actualizarProyecto,
    eliminarProyecto,
    activarProyecto,
    subirArchivo,
    actualizarArchivo,
    visualizarArchivo,
    descargarArchivo,
    eliminarArchivo,
}