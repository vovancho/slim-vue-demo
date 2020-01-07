import { tasks as api } from "../../api";

// initial state
const state = {
  items: {
    total: 0,
    rows: []
  }
};

// getters
const getters = {};

// actions
const actions = {
  async getTasks({ commit }, options) {
    let response = await api.getTasks(options);
    commit("setTasks", response.data);
  }
};

// mutations
const mutations = {
  setTasks(state, items) {
    state.items = items;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
