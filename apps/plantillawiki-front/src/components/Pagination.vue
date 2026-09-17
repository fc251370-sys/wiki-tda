<template>
    <v-pagination :length="totalPage" v-model="paginas" @update:modelValue="onPageChange"></v-pagination>
</template>
<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    page:{
        type:Number,
        default:1
    },
    totalPage:{
        type:Number,
        default:1
    }
})

const emit = defineEmits(['update:page']);

const paginas = ref(props.page);

watch(() => props.page, (newPage) => {
    paginas.value = newPage;
});

const onPageChange = (newPage) => {
    emit('update:page', newPage);
};
</script>
