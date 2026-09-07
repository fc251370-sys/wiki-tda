<template>
    <v-app-bar>
      <v-app-bar-nav-icon @click="toggleDrawer">
        <v-icon icon="mdi-reorder-horizontal"></v-icon>
      </v-app-bar-nav-icon>
      <v-toolbar-title>{{ nameButton }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn text to="/admin">Dashboard</v-btn>
      <v-btn text @click="cerrarSesion()">Logout</v-btn>
    </v-app-bar>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { logout } from '../services/auth/login.service';
import { useRouter } from 'vue-router';
const router = useRouter()

const props = defineProps({
    drawer: Boolean,
    nameButton:{
      default:'Dashboard',
      type:String
    }
})
const emit = defineEmits(['update:drawer'])
const toggleDrawer = () => emit('update:drawer', !props.drawer)

const cerrarSesion = async () => {
  const response = await logout()
  if (response.status) {
    localStorage.clear()
    router.push('/')
  }
}

</script>
