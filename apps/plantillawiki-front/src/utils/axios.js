// src/services/api.js

import axios from "axios";
import { useAlertStore } from "../store/useAlertStore";
import { useLoadingStore } from "../store/useLoadingStore";
import { clearAuthSession } from "./authSession";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "/api",
  timeout: 15000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

let refreshPromise;
let sessionExpiredRedirecting = false;

const isAuthEndpoint = (url = "") =>
  /(?:^|\/)auth\/(?:login|refresh)(?:[?#]|$)/.test(url);
const isHandledAuthRequest = (url = "") =>
  /(?:^|\/)auth\/(?:login|logout)(?:[?#]|$)/.test(url);

async function refreshAccessToken() {
  const response = await api.post("auth/refresh");
  const token = response.data?.data?.token ?? response.data?.token;

  if (!token) {
    throw new Error("La respuesta de refresh no contiene un token.");
  }

  localStorage.setItem("token", token);
  sessionExpiredRedirecting = false;
  return token;
}

function handleExpiredSession() {
  clearAuthSession();

  if (!sessionExpiredRedirecting) {
    sessionExpiredRedirecting = true;
    useAlertStore().showAlert(
      "Tu sesión expiró. Inicia sesión nuevamente.",
      "warning"
    );

    if (window.location.pathname !== "/login") {
      window.location.assign("/login");
    }
  }
}

/**
 * Interceptor de solicitudes
 * Agrega el token JWT automáticamente
 */
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    config._globalLoaderTracked = true;
    useLoadingStore().startLoading();

    return config;
  },
  (error) => Promise.reject(error)
);

/**
 * Interceptor de respuestas
 */
api.interceptors.response.use(
  (response) => {
    if (response.config._globalLoaderTracked) {
      response.config._globalLoaderTracked = false;
      useLoadingStore().finishLoading();
    }

    return response;
  },

  async (error) => {
    if (error.config?._globalLoaderTracked) {
      error.config._globalLoaderTracked = false;
      useLoadingStore().finishLoading();
    }

    const alertStore = useAlertStore();

    const requestUrl = error.config?.url ?? "";
    if (!error.response) {
      if (isHandledAuthRequest(requestUrl)) {
        return Promise.reject(error);
      }

      console.error("Error de conexión con el servidor.");
      alertStore.showAlert("Error de conexión con el servidor.", "error");
      return Promise.reject(error);
    }

    if (error.response.status === 401) {
      if (isAuthEndpoint(requestUrl)) {
        return Promise.reject(error);
      }

      if (isHandledAuthRequest(requestUrl)) {
        return Promise.reject(error);
      }

      if (error.config && !error.config._retry && localStorage.getItem("token")) {
        error.config._retry = true;

        try {
          refreshPromise ??= refreshAccessToken();
          const token = await refreshPromise;
          error.config.headers.Authorization = `Bearer ${token}`;
          return api(error.config);
        } catch (refreshError) {
          if (refreshError.response?.status === 401) {
            handleExpiredSession();
          }
          return Promise.reject(refreshError);
        } finally {
          refreshPromise = undefined;
        }
      }

      if (localStorage.getItem("token")) {
        handleExpiredSession();
      } else {
        alertStore.showAlert("Debes iniciar sesión para continuar.", "warning");
      }

      return Promise.reject(error);
    }

    switch (error.response.status) {
      case 400:
        console.error("Solicitud incorrecta.");
        alertStore.showAlert("Solicitud incorrecta.", "error");
        break;

      case 403:
        console.error("No tienes permisos.");
        alertStore.showAlert("No tienes permisos para realizar esta acción.", "error");
        break;

      case 404:
        console.error("Recurso no encontrado.");
        alertStore.showAlert("Recurso no encontrado.", "error");
        break;

      case 422:
        console.error("Error de validación.");
        alertStore.showAlert("Error de validación. Revisa los datos enviados.", "warning");
        break;

      case 500:
        console.error("Error interno del servidor.");
        alertStore.showAlert("Error interno del servidor.", "error");
        break;

      default:
        console.error("Ha ocurrido un error.");
        alertStore.showAlert("Ha ocurrido un error inesperado.", "error");
    }

    return Promise.reject(error);
  }
);

export default api;