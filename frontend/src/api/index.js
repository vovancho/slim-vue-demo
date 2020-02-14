import axios from "axios";

const base = {
  login(username, password) {
    return axios.post("/oauth/auth", {
      grant_type: "password",
      username,
      password,
      client_id: "app",
      client_secret: "",
      access_type: "offline"
    });
  },
  refreshToken(refreshToken) {
    return axios.post("/oauth/auth", {
      grant_type: "refresh_token",
      refresh_token: refreshToken,
      client_id: "app",
      client_secret: ""
    });
  },
  signup(email, password) {
    return axios.post("/auth/signup", { email, password });
  },
  signupConfirm(email, token) {
    return axios.post("/auth/signup/confirm", { email, token });
  }
};

const tasks = {
  getTasks(options) {
    return axios.get("/tasks", { params: options });
  },
  createTask(name, type) {
    return axios.post("/tasks/create", { name, type });
  },
  cancelTask(id) {
    return axios.delete(`/tasks/${id}/cancel`);
  }
};

export { base, tasks };
