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
      <v-container class="fill-height">
        <tasks v-if="isLogged" />
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
import { mapActions, mapGetters } from "vuex";
import tasks from "../components/Tasks.vue";

export default {
  components: {
    tasks
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
  created() {
    if (!this.isLogged) {
      this.$router.push({ name: "login" });
    }
  }
};
</script>
