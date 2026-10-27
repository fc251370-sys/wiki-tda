<template>
    <v-row>
        <v-col color="primary" class="d-flex justify-center font-weight-black">
            <p >
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
p{
    color: #0D1239;
}
</style>