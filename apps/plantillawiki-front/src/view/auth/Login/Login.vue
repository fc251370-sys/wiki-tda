<template>
  <main class="login-page">
    <section class="login-brand-panel" aria-label="Fundación Educativa para el Software Libre">
      <div class="login-brand">
        <div class="login-brand__mark" aria-hidden="true">
          <v-icon icon="mdi-school-outline" size="42" />
        </div>
        <div>
          <div class="login-brand__name">FESOL</div>
          <p class="login-brand__description">
            Fundación Educativa<br />
            para el Software Libre
          </p>
        </div>
      </div>

      <div class="login-illustration" aria-hidden="true">
        <div class="login-illustration__orbit login-illustration__orbit--one" />
        <div class="login-illustration__orbit login-illustration__orbit--two" />
        <div class="login-illustration__code">
          <v-icon icon="mdi-code-tags" size="30" />
          <span />
          <span />
        </div>
        <div class="login-illustration__dashboard">
          <div class="login-illustration__dashboard-header">
            <span />
            <span />
            <span />
          </div>
          <div class="login-illustration__dashboard-content">
            <div class="login-illustration__chart">
              <span />
              <span />
              <span />
              <span />
              <span />
            </div>
            <div class="login-illustration__stats">
              <v-icon icon="mdi-chart-donut" size="62" />
              <span />
              <span />
              <span />
            </div>
          </div>
        </div>
        <div class="login-illustration__people">
          <v-icon icon="mdi-account-group-outline" size="116" />
        </div>
        <p class="login-brand-panel__motto">
          Conocimiento que conecta,<br />
          tecnología que transforma.
        </p>
      </div>
    </section>

    <section class="login-form-panel">
      <header class="login-topbar">
        <v-chip
          variant="outlined"
          color="secondary"
          append-icon="mdi-chevron-down"
          class="login-topbar__organization"
        >
          FESOL 2026
        </v-chip>
        <ThemeToggle />
        <v-btn
          icon="mdi-bell-outline"
          variant="text"
          color="secondary"
          aria-label="Notificaciones"
          disabled
        />
        <v-avatar color="secondary" size="38" class="login-topbar__avatar">
          <v-icon icon="mdi-account" color="white" />
        </v-avatar>
      </header>

      <div class="login-form-wrap">
        <div class="login-mobile-brand">
          <div class="login-brand__mark" aria-hidden="true">
            <v-icon icon="mdi-school-outline" size="30" />
          </div>
          <span>FESOL</span>
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

.login-brand-panel {
  position: relative;
  display: flex;
  min-height: 100vh;
  flex-direction: column;
  overflow: hidden;
  padding: clamp(40px, 8vw, 110px);
  background:
    radial-gradient(ellipse at 52% 65%, rgb(31 45 127 / 55%), transparent 44%),
    linear-gradient(145deg, #0b2947 0%, #0d1239 100%);
  color: white;
}

.login-brand {
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 20px;
}

.login-brand__mark {
  display: grid;
  width: 82px;
  height: 82px;
  flex: 0 0 82px;
  place-items: center;
  border: 2px solid rgb(255 255 255 / 80%);
  border-radius: 50%;
  color: white;
}

.login-brand__name {
  font-size: clamp(2.6rem, 5vw, 4rem);
  font-weight: 700;
  letter-spacing: 0.02em;
  line-height: 1;
}

.login-brand__description {
  margin-top: 8px;
  font-size: 1.05rem;
  line-height: 1.35;
}

.login-illustration {
  position: relative;
  display: grid;
  min-height: 450px;
  flex: 1;
  place-items: center;
}

.login-illustration__dashboard {
  z-index: 1;
  width: min(78%, 440px);
  overflow: hidden;
  border: 9px solid #274c83;
  border-radius: 18px;
  background: #f4f7fc;
  box-shadow: 0 24px 50px rgb(0 0 0 / 30%);
  transform: translateY(12px);
}

.login-illustration__dashboard-header {
  display: flex;
  gap: 7px;
  padding: 12px;
  background: #e6edf8;
}

.login-illustration__dashboard-header span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #6f90bf;
}

.login-illustration__dashboard-content {
  display: grid;
  min-height: 190px;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 18px;
  padding: 24px 20px;
}

.login-illustration__chart {
  display: flex;
  height: 150px;
  align-items: end;
  gap: 10px;
  border-bottom: 2px solid #d0d9e8;
}

.login-illustration__chart span {
  flex: 1;
  border-radius: 5px 5px 0 0;
  background: linear-gradient(#5984c2, #315a9b);
}

.login-illustration__chart span:nth-child(1) { height: 36%; }
.login-illustration__chart span:nth-child(2) { height: 55%; }
.login-illustration__chart span:nth-child(3) { height: 43%; }
.login-illustration__chart span:nth-child(4) { height: 78%; }
.login-illustration__chart span:nth-child(5) { height: 100%; }

.login-illustration__stats {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #315a9b;
}

.login-illustration__stats span {
  width: 74%;
  height: 6px;
  border-radius: 8px;
  background: #d7dfec;
}

.login-illustration__code {
  position: absolute;
  top: 20%;
  left: 12%;
  display: grid;
  width: 72px;
  height: 72px;
  place-items: center;
  border: 1px solid rgb(255 255 255 / 18%);
  border-radius: 18px;
  background: rgb(30 65 108 / 85%);
  color: #80b6ff;
  transform: rotate(-8deg);
}

.login-illustration__code span {
  position: absolute;
  right: -80px;
  width: 50px;
  height: 4px;
  border-radius: 4px;
  background: rgb(128 182 255 / 45%);
}

.login-illustration__code span:last-child {
  right: -64px;
  margin-top: 14px;
  width: 34px;
}

.login-illustration__orbit {
  position: absolute;
  border: 1px solid rgb(127 176 232 / 24%);
  border-radius: 50%;
}

.login-illustration__orbit--one {
  width: 390px;
  height: 150px;
  transform: rotate(-24deg);
}

.login-illustration__orbit--two {
  width: 430px;
  height: 190px;
  transform: rotate(26deg);
}

.login-illustration__people {
  position: absolute;
  z-index: 2;
  right: 6%;
  bottom: 8%;
  display: grid;
  width: 130px;
  height: 130px;
  place-items: center;
  border-radius: 50%;
  background: #234b7f;
  color: #c8d9f0;
  box-shadow: 0 14px 30px rgb(0 0 0 / 24%);
}

.login-brand-panel__motto {
  z-index: 1;
  color: rgb(255 255 255 / 75%);
  font-size: 1rem;
  line-height: 1.6;
  text-align: center;
}

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
  color: rgb(var(--v-theme-on-surface-variant));
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

  .login-brand__mark {
    width: 64px;
    height: 64px;
    flex-basis: 64px;
  }

  .login-brand__description {
    font-size: 0.9rem;
  }

  .login-illustration__dashboard {
    width: 90%;
  }

  .login-illustration__people {
    right: 0;
    width: 96px;
    height: 96px;
  }

  .login-illustration__people :deep(.v-icon) {
    font-size: 84px !important;
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
    font-size: 1.3rem;
    font-weight: 700;
  }

  .login-mobile-brand .login-brand__mark {
    width: 48px;
    height: 48px;
    flex-basis: 48px;
    border-color: rgb(var(--v-theme-primary));
    color: rgb(var(--v-theme-primary));
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
