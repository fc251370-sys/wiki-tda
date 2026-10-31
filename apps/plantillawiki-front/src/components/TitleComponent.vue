<template>
    <v-row>
        <v-col  class="d-flex justify-center font-weight-black">
            <p class="text-primary">
                {{ title }}
            </p>
        </v-col>
    </v-row>
</template>
<script setup>
import {defineProps} from 'vue'
const props = defineProps({
    title:{
        type:String,
        default:'DashBoard'
    }
})
</script>
<style scoped>

</style>