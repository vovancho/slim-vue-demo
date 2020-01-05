import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
import jwt_decode from "jwt-decode";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    currentEmail: null,
    user: JSON.parse(localStorage.getItem("user"))
  },
  getters: {
    isLogged: state => {
      return !!state.user;
    },
    userEmail: state => {
      if (state.user) {
        let payload = jwt_decode(state.user.access_token);
        return payload.email;
      }
      return null;
    }
  },
  mutations: {
    changeCurrentEmail(state, email) {
      state.currentEmail = email;
    },
    login(state, user) {
      state.user = user;
    },
    logout(state) {
      state.user = null;
    }
  },
  actions: {
    async login({ commit }, data) {
      commit("logout");
      try {
        let response = await axios.post("/oauth/auth", {
          grant_type: "password",
          username: data.email,
          password: data.password,
          client_id: "app",
          client_secret: "",
          access_type: "offline"
        });

        const user = response.data;
        localStorage.setItem("user", JSON.stringify(user));
        axios.defaults.headers.common["Authorization"] =
          "Bearer " + user.access_token;
        commit("login", user);

        return user;
      } catch (error) {
        commit("logout");
        localStorage.removeItem("user");

        throw error;
      }
    },
    logout({ commit }) {
      commit("logout");
      localStorage.removeItem("user");
      delete axios.defaults.headers.common["Authorization"];
    },
    async refresh({ state, commit, dispatch }) {
      if (state.user) {
        delete axios.defaults.headers.common["Authorization"];

        try {
          let response = await axios.post("/oauth/auth", {
            grant_type: "refresh_token",
            refresh_token: state.user.refresh_token,
            client_id: "app",
            client_secret: ""
          });

          const user = response.data;
          localStorage.setItem("user", JSON.stringify(user));
          axios.defaults.headers.common["Authorization"] =
            "Bearer " + user.access_token;
          commit("login", user);

          return response;
        } catch (error) {
          dispatch("logout");

          throw error;
        }
      }
    }
  },
  modules: {}
});
