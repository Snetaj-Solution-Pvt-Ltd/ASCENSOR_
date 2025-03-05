import { defineStore } from 'pinia';

export const useAuthStore = defineStore('useAuthStore', {
  state: () => ({
    user: localStorage.getItem("user") ? localStorage.getItem("user") : null,
    userList: null
  }),
  getters: {
    getUser(state) {
      return JSON.parse(state.user);
    },
    token(state) {
      const user = state.user
      return user && user.token ? user.token : localStorage.getItem("token");
    }
  },
  actions: {
    async setUser(data) {
      this.user = data ? data : localStorage.getItem("user");
      localStorage.setItem("user", JSON.stringify(data));
      localStorage.setItem("token", data.token);
    },
  },
});