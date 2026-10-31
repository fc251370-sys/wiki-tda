import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'

export default createVuetify({
  icons: {
    defaultSet: 'mdi',
  },
  theme: {
    defaultTheme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#0D1239',
          secondary: '#1F2D7F',
          accent: '#82B1FF',
          tertiary: '#82B1FF',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FFC107',
        },
      },
      dark: {
        dark: true,
        colors: {
          primary: '#9AA8FF',
          secondary: '#B3BEFF',
          accent: '#82B1FF',
          tertiary: '#82B1FF',
          error: '#FF8A80',
          info: '#64B5F6',
          success: '#81C784',
          warning: '#FFD54F',
          background: '#121318',
          surface: '#1C1D24',
        },
      },
    },
  },
})
