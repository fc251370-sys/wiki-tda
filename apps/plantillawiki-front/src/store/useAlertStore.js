import { defineStore } from 'pinia';

export const useAlertStore = defineStore('alert', {
  state: () => ({
    show: false,
    message: '',
    type: 'info', // 'success', 'error', 'warning', 'info'
    timeout: 3000
  }),
  actions: {
    showAlert(message, type = 'info', timeout = 3000) {
      this.message = message;
      this.type = type;
      this.timeout = timeout;
      this.show = true;
    },
    hideAlert() {
      this.show = false;
    }
  }
});
