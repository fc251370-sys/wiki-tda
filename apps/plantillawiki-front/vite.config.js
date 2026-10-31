import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    plugins: [
      vue(),
      vuetify({ autoImport: true }),
      tailwindcss()
    ],
    server: {
      host: true,
      port: parseInt(process.env.PORT || "3006", 10),
      watch: {
        usePolling: true
      },
      proxy: {
        '/api': {
          target: env.API_PROXY_TARGET || 'http://127.0.0.1:8006',
          changeOrigin: true
        }
      }
    }
  }
})
