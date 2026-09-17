<template>
    <v-navigation-drawer app :model-value="drawer" @update:model-value="$emit('update:drawer', $event)" :permanent="permanent">
     <SidebarMenu />
    </v-navigation-drawer>
</template>
<script setup>
import SidebarMenu from './SidebarMenu.vue';
    defineProps({
        drawer: {
            type: Boolean,
            default: false
        },
        permanent: {
            type: Boolean,
            default: false
        }
    })
    
    defineEmits(['update:drawer'])
</script>
<style scoped>

</style>