import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import vuetify from "./plugins/vuetify";
import VuetifyDialog from "vuetify-dialog";
import axios from "axios";
import VueTheMask from "vue-the-mask";
import "./vee-validate/index";

Vue.config.productionTip = false;

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

const user = JSON.parse(localStorage.getItem("user"));
if (user) {
  axios.defaults.headers.common["Authorization"] =
    "Bearer " + user.access_token;
}

axios.interceptors.response.use(null, async error => {
  if (!error.response || error.response.status !== 401) {
    return error;
  }
  const request = error.config;
  if (request.data) {
    let data = JSON.parse(request.data);
    if (data && data.grant_type) {
      return error;
    }
  }

  try {
    await store.dispatch("refresh")
  } catch (e) {
    await router.push({ name: "login" });
    return error;
  }

  request.headers["Authorization"] = "Bearer " + store.state.user.access_token;
  return axios(request);
});

Vue.use(VuetifyDialog, {
  context: {
    vuetify
  },
  confirm: {
    actions: {
      false: "Отмена",
      true: {
        text: "Применить",
        color: "primary"
      }
    }
  },
  error: {
    icon: false,
    actions: {
      false: "Закрыть"
    }
  }
});

Vue.use(VueTheMask);

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount("#app");
