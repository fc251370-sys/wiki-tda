import api from '../../utils/axios'
const carreras = async()=> await api.get('carreras')

export {
    carreras
}