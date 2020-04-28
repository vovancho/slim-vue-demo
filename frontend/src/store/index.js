import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import jwtDecode from 'jwt-decode'
import tasks from './modules/tasks'
import { base as api } from '../api'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    tasks
  },
  state: {
    currentEmail: null,
    user: JSON.parse(localStorage.getItem('user'))
  },
  getters: {
    isLogged: (state) => {
      return !!state.user
    },
    userId: (state) => {
      if (state.user) {
        const payload = jwtDecode(state.user.access_token)
        return payload.sub
      }
      return null
    },
    userEmail: (state) => {
      if (state.user) {
        const payload = jwtDecode(state.user.access_token)
        return payload.email
      }
      return null
    }
  },
  mutations: {
    changeCurrentEmail(state, email) {
      state.currentEmail = email
    },
    login(state, user) {
      state.user = user
    },
    logout(state) {
      state.user = null
    }
  },
  actions: {
    async login({ commit }, data) {
      commit('logout')
      try {
        const response = await api.login(data.email, data.password)
        const user = response.data
        localStorage.setItem('user', JSON.stringify(user))
        axios.defaults.headers.common.Authorization =
          'Bearer ' + user.access_token
        commit('login', user)

        return user
      } catch (error) {
        commit('logout')
        localStorage.removeItem('user')

        throw error
      }
    },
    logout({ commit }) {
      commit('logout')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common.Authorization
    },
    async refresh({ state, commit, dispatch }) {
      if (state.user) {
        delete axios.defaults.headers.common.Authorization

        try {
          const response = await api.refreshToken(state.user.refresh_token)
          const user = response.data
          localStorage.setItem('user', JSON.stringify(user))
          axios.defaults.headers.common.Authorization =
            'Bearer ' + user.access_token
          commit('login', user)

          return response
        } catch (error) {
          dispatch('logout')

          throw error
        }
      }
    },
    signup(context, { email, password }) {
      return api.signup(email, password)
    },
    signupConfirm(context, { email, token }) {
      return api.signupConfirm(email, token)
    }
  }
})
