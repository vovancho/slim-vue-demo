<template>
  <v-app>
    <v-app-bar app color="indigo" dark hide-on-scroll>
      <v-toolbar-title>Application</v-toolbar-title>
    </v-app-bar>

    <v-content>
      <v-container class="fill-height" fluid>
        <v-card class="mx-auto mt-5" max-width="344" outlined :loading="loading">
          <v-card-text> Name: {{ name }}, Version: {{ version }}</v-card-text>
        </v-card>
      </v-container>
    </v-content>

    <v-footer color="indigo" app>
      <span class="white--text">&copy; 2020</span>
    </v-footer>
  </v-app>
</template>

<script>
  import axios from "axios";

  export default {
    data() {
      return {
        name: null,
        version: null,
        loading: true
      }
    },
    mounted() {
      axios.get('/').then(response => {

        this.name = response.data.name;
        this.version = response.data.version;
        this.loading = false;
      });
    },
  };
</script>
