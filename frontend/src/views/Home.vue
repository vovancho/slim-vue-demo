<template>
  <v-app>
    <v-app-bar app color="indigo" dark hide-on-scroll>
      <v-toolbar-title>Demo API Task Handler Application</v-toolbar-title>
      <v-spacer />
      <v-toolbar-items>
        <div class="headline d-flex align-center px-4">{{ userEmail }}</div>
        <v-btn text @click="logoutClicked">Выйти</v-btn>
      </v-toolbar-items>
    </v-app-bar>

    <v-content>
      <v-container class="fill-height" fluid>
        <v-card
          class="mx-auto mt-5"
          max-width="344"
          outlined
          :loading="loading"
        >
          <v-card-text> Name: {{ name }}, Version: {{ version }}</v-card-text>
        </v-card>
      </v-container>
    </v-content>

    <v-footer padless>
      <v-card flat tile width="100%" class="indigo text-center">
        <v-card-text class="white--text">
          {{ new Date().getFullYear() }} — <strong>Vuetify</strong>
        </v-card-text>
      </v-card>
    </v-footer>
  </v-app>
</template>

<script>
import axios from "axios";
import { mapActions, mapGetters } from "vuex";

export default {
  data() {
    return {
      name: null,
      version: null,
      loading: true
    };
  },
  computed: {
    ...mapGetters(["isLogged", "userEmail"])
  },
  methods: {
    ...mapActions(["logout"]),
    async logoutClicked() {
      await this.logout();
      await this.$router.push({ name: "login" });
    }
  },
  async mounted() {
    if (this.$store.state.user) {
      await axios.get("/").then(response => {
        this.name = response.data.name;
        this.version = response.data.version;
        this.loading = false;
      });
    }
  },
  created() {
    if (!this.isLogged) {
      this.$router.push({ name: "login" });
    }
  }
};
</script>
