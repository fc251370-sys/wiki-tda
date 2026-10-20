import { defineStore } from 'pinia';

export const useLoadingStore = defineStore('loading', {
  state: () => ({
    activeRequests: 0,
  }),
  getters: {
    isLoading: (state) => state.activeRequests > 0,
  },
  actions: {
    startLoading() {
      this.activeRequests += 1;
    },
    finishLoading() {
      this.activeRequests = Math.max(0, this.activeRequests - 1);
    },
  },
});
