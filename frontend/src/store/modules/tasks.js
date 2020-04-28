import { tasks as api } from '../../api'

// initial state
const state = {
  items: {
    total: 0,
    rows: []
  },
  processing: false
}

// getters
const getters = {}

// actions
const actions = {
  async getTasks({ commit }, options) {
    try {
      const response = await api.getTasks(options)
      commit('setTasks', response.data)
    } catch (e) {
      commit('resetTasks')
    }
  },
  async createTask(context, { name, visibility }) {
    return api.createTask(name, visibility)
  },
  processLoading({ commit }) {
    commit('processLoading')
  },
  canAddTask({ rootGetters }, { visibility, authorId }) {
    return (
      visibility === 'public' ||
      (visibility === 'private' && authorId === rootGetters.userId)
    )
  },
  async cancelTask(context, id) {
    return api.cancelTask(id)
  },
  async wsNotificationsInit({ rootState, state, dispatch, commit }) {
    const socket = new WebSocket(process.env.VUE_APP_WS_URL)

    socket.onopen = function() {
      if (rootState.user) {
        socket.send(
          JSON.stringify({
            type: 'auth',
            token: rootState.user.access_token
          })
        )
      }
    }

    socket.onmessage = async function(event) {
      const data = JSON.parse(event.data)

      switch (data.event) {
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskCreated': {
          if (
            await dispatch('canAddTask', {
              visibility: data.visibility,
              authorId: data.author_id
            })
          ) {
            commit('processLoading')
            await dispatch('getTasks')
            commit('processLoaded')
          }
          break
        }
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskExecuted':
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskCompleted':
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskCanceled':
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskError': {
          commit('processLoading')
          await dispatch('getTasks')
          commit('processLoaded')
          break
        }
        case 'App\\TaskHandler\\Entity\\Task\\Event\\TaskProcessed': {
          const hasTask = state.items.rows.some(
            (item) => item.id === data.task_id
          )
          if (hasTask) {
            commit('updateTask', {
              taskId: data.task_id,
              processPercent: data.process_percent
            })
          } else {
            commit('processLoading')
            await dispatch('getTasks')
            commit('processLoaded')
          }
          break
        }
      }
    }
  }
}

// mutations
const mutations = {
  setTasks(state, items) {
    state.items = items
  },
  resetTasks(state) {
    state.items = {
      total: 0,
      rows: []
    }
  },
  updateTask(state, { taskId, processPercent }) {
    const item = state.items.rows.find((item) => item.id === taskId)
    item.process_percent = processPercent
  },
  processLoading(state) {
    state.processing = true
  },
  processLoaded(state) {
    state.processing = false
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
