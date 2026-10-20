import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'

export default createVuetify({
  dark: false,
  icons: {
    defaultSet: 'mdi',
  },
  colors: {
    primary: '#0D1239',
    secondary: '#1F2D7F',
    accent: '#82B1FF',
    error: '#FF5252',
    info: '#2196F3',
    success: '#4CAF50',
    warning: '#FFC107', 

  }
})
