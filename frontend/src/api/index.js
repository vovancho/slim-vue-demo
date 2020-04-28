import axios from 'axios'

const base = {
  login(username, password) {
    return axios.post('/v1/oauth/auth', {
      grant_type: 'password',
      username,
      password,
      client_id: 'app',
      client_secret: '',
      access_type: 'offline'
    })
  },
  refreshToken(refreshToken) {
    return axios.post('/v1/oauth/auth', {
      grant_type: 'refresh_token',
      refresh_token: refreshToken,
      client_id: 'app',
      client_secret: ''
    })
  },
  async signup(email, password) {
    return axios.post('/v1/auth/signup', { email, password })
  },
  signupConfirm(email, token) {
    return axios.post('/v1/auth/signup/confirm', { email, token })
  }
}

const tasks = {
  getTasks(options) {
    return axios.get('/v1/tasks', { params: options })
  },
  createTask(name, visibility) {
    return axios.post('/v1/tasks/create', { name, visibility })
  },
  cancelTask(id) {
    return axios.delete(`/v1/tasks/${id}/cancel`)
  }
}

export { base, tasks }
