<template>
  <main class="login-page">
    <section class="login-brand-panel" aria-label="Universidad Luterana Salvadoreña">
      <div class="login-brand-panel__name">
        Universidad<br />
        Luterana<br />
        Salvadoreña
      </div>
      <span class="login-brand-panel__divider" aria-hidden="true" />
      <p class="login-brand-panel__motto">Conocimiento que transforma</p>
    </section>

    <section class="login-form-panel">
      <header class="login-topbar">
        
        <ThemeToggle />
        
        
      </header>

      <div class="login-form-wrap">
        <div class="login-mobile-brand">
          <div class="login-mobile-brand__mark" aria-hidden="true">
            <v-icon icon="mdi-school-outline" size="30" />
          </div>
          <span>Universidad Luterana Salvadoreña</span>
        </div>

        <div class="login-heading">
          <span class="login-heading__eyebrow">BIENVENIDO/A</span>
          <h1>Accede a tu cuenta</h1>
          <p>Ingresa tus datos para continuar</p>
        </div>

        <v-form ref="form" class="login-form" @submit.prevent="submit">
          <v-text-field
            v-model="formulario.email"
            label="Correo electrónico"
            type="email"
            autocomplete="username"
            variant="outlined"
            color="primary"
            prepend-inner-icon="mdi-email-outline"
            :rules="[rules.required, rules.email]"
            required
          />

          <v-text-field
            v-model="formulario.password"
            label="Contraseña"
            autocomplete="current-password"
            variant="outlined"
            color="primary"
            prepend-inner-icon="mdi-lock-outline"
            :type="showPassword ? 'text' : 'password'"
            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
            :rules="[rules.required]"
            required
            @click:append-inner="showPassword = !showPassword"
          />

          <div class="login-form__options">
            <v-checkbox
              v-model="rememberEmail"
              label="Recordarme"
              color="primary"
              density="compact"
              hide-details
            />
            <span class="login-form__help">
              ¿Olvidaste tu contraseña? Consulta con tu coordinador
            </span>
          </div>

          <v-btn
            type="submit"
            block
            size="large"
            color="primary"
            variant="flat"
            class="login-form__submit"
          >
            Iniciar sesión
          </v-btn>
        </v-form>

        <p class="login-form__signup">
          ¿No tienes cuenta?
          <span>Consulta con tu coordinador</span>
        </p>
      </div>

      <footer class="login-footer">
        © 2026 FESOL · Fundación Educativa para el Software Libre
      </footer>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { useLogin } from './composable';
import { useAlertStore } from '../../../store/useAlertStore';
import ThemeToggle from '../../../components/ThemeToggle.vue';

const { formulario, submit: login } = useLogin();
const alertStore = useAlertStore();
const form = ref(null);
const showPassword = ref(false);
const rememberEmail = ref(Boolean(localStorage.getItem('rememberedEmail')));

if (rememberEmail.value) {
  formulario.value.email = localStorage.getItem('rememberedEmail') || '';
}

const rules = {
  required: (value) => Boolean(value) || 'Este campo es obligatorio',
  email: (value) => /.+@.+\..+/.test(value) || 'Ingresa un correo válido',
};

const submit = async () => {
  const { valid } = await form.value.validate();
  if (!valid) {
    return;
  }

  if (rememberEmail.value) {
    localStorage.setItem('rememberedEmail', formulario.value.email);
  } else {
    localStorage.removeItem('rememberedEmail');
  }

  try {
    await login();
  } catch (error) {
    alertStore.showAlert(
      error.response?.data?.message || 'No se pudo iniciar sesión.',
      'error'
    );
  }
};
</script>

<style scoped>
.login-page {
  display: grid;
  min-height: 100vh;
  grid-template-columns: minmax(360px, 1fr) minmax(500px, 1fr);
  background: rgb(var(--v-theme-surface));
}

/* ---------- Panel izquierdo ---------- */
.login-brand-panel {
  display: flex;
  min-height: 100vh;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: clamp(40px, 8vw, 110px);
  background:
    radial-gradient(ellipse at 52% 65%, rgb(31 45 127 / 55%), transparent 44%),
    linear-gradient(145deg, #0b2947 0%, #0d1239 100%);
  color: white;
  text-align: center;
}

.login-brand-panel__name {
  font-size: clamp(2.2rem, 4.2vw, 3.6rem);
  font-weight: 700;
  letter-spacing: -0.01em;
  line-height: 1.12;
}

.login-brand-panel__divider {
  width: 64px;
  height: 3px;
  margin: 28px 0 22px;
  border-radius: 3px;
  background: rgb(255 255 255 / 55%);
}

.login-brand-panel__motto {
  color: rgb(255 255 255 / 78%);
  font-size: clamp(1rem, 1.5vw, 1.25rem);
  letter-spacing: 0.02em;
}

/* ---------- Panel derecho (igual que el original) ---------- */
.login-form-panel {
  display: flex;
  min-height: 100vh;
  flex-direction: column;
  padding: 20px clamp(28px, 6vw, 88px) 24px;
  background: rgb(var(--v-theme-surface));
}

.login-topbar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 14px;
}

.login-topbar__organization {
  font-weight: 500;
}

.login-topbar__avatar {
  border: 2px solid white;
  box-shadow: 0 1px 5px rgb(13 18 57 / 18%);
}

.login-form-wrap {
  width: min(100%, 430px);
  margin: auto;
  padding-block: 48px;
}

.login-heading {
  margin-bottom: 34px;
}

.login-heading__eyebrow {
  color: rgb(var(--v-theme-secondary));
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.14em;
}

.login-heading h1 {
  margin-top: 12px;
  color: rgb(var(--v-theme-on-surface));
  font-size: clamp(2rem, 3vw, 2.5rem);
  font-weight: 700;
  letter-spacing: -0.035em;
}

.login-heading p {
  margin-top: 8px;
  color: black;
  font-size: 0.98rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.login-form__options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin: 0 0 12px;
}

.login-form__help {
  max-width: 190px;
  color: rgb(var(--v-theme-secondary));
  font-size: 0.82rem;
  line-height: 1.4;
  text-align: right;
}

.login-form__submit {
  min-height: 50px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 0;
}

.login-form__signup {
  margin-top: 28px;
  color: rgb(var(--v-theme-on-surface-variant));
  font-size: 0.9rem;
  text-align: center;
}

.login-form__signup span {
  color: rgb(var(--v-theme-secondary));
  font-weight: 600;
}

.login-footer {
  color: rgb(var(--v-theme-on-surface-variant));
  font-size: 0.74rem;
  text-align: center;
}

/* Marca que aparece solo en móvil, cuando se oculta el panel izquierdo */
.login-mobile-brand {
  display: none;
}

@media (max-width: 900px) {
  .login-page {
    grid-template-columns: minmax(290px, 0.8fr) minmax(420px, 1.2fr);
  }

  .login-brand-panel {
    padding: 40px;
  }
}

@media (max-width: 680px) {
  .login-page {
    display: block;
  }

  .login-brand-panel {
    display: none;
  }

  .login-form-panel {
    min-height: 100vh;
    padding: 16px 24px 20px;
  }

  .login-mobile-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 44px;
    color: rgb(var(--v-theme-primary));
    font-size: 1.05rem;
    font-weight: 700;
    line-height: 1.2;
  }

  .login-mobile-brand__mark {
    display: grid;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    place-items: center;
    border: 2px solid rgb(var(--v-theme-primary));
    border-radius: 50%;
  }

  .login-topbar__organization {
    margin-right: auto;
  }

  .login-form-wrap {
    padding-block: 32px;
  }
}

@media (max-width: 400px) {
  .login-form-panel {
    padding-inline: 18px;
  }

  .login-form__options {
    align-items: flex-start;
  }

  .login-form__help {
    max-width: 150px;
    padding-top: 8px;
    font-size: 0.75rem;
  }
}
</style>