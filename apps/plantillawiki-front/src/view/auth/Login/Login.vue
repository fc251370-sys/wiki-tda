<template>
  <div>
     <v-card
        class="mx-auto pt-4"
        width="500"
      >
        <template v-slot:title>
          <span class="font-weight-black">Iniciar sesión</span>
        </template>

        <v-card-text class="">
          <v-form
              @submit.prevent="submit"
            >
              <v-text-field
                v-model="formulario.email"
                
                label="Email"
                variant="solo-filled"
                class="mb-3"
              ></v-text-field>

              <v-text-field
                v-model="formulario.password"
                
                label="Password"
                type="password"
                variant="solo-filled"
              ></v-text-field>

              <v-btn
                type="submit"
                block
                color="tertiary"
              >
                Login
              </v-btn>
            </v-form>
        </v-card-text>
      </v-card>
  </div>
</template>

<script setup>
import { useLogin } from './composable';

const { formulario, submit } = useLogin();

</script>
