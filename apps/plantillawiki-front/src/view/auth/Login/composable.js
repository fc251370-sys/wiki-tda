import { useLoginStore } from '../../../store/auth/useLoginStore.js';
import { computed } from 'vue';
import { useRouter } from 'vue-router';

export const useLogin = () => {
    const router = useRouter();
    const store = useLoginStore();
    
    const formulario = computed(() => store.formulario);

    const submit = async () => {
        const response = await store.submit();
        if (response == 200) {
            router.push({ name: 'Dashboard' });
        }
    }

    return{
        formulario,
        submit
    }
}