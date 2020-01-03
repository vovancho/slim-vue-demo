<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-6" :disabled="loading">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Авторизация</v-toolbar-title>
                <v-spacer/>
                <v-icon>mdi-login</v-icon>
              </v-toolbar>
              <v-card-text>
                <v-alert :value="!!error" icon="mdi-alert-circle" prominent dense transition="scroll-x-reverse-transition" border="left" type="error">{{ error }}</v-alert>

                <v-form>
                  <ValidationProvider name="E-Mail" rules="required|email" v-slot="{ errors, valid }">
                    <v-text-field v-model="form.email" label="E-Mail" name="login" prepend-icon="mdi-account" type="text" :error-messages="errors" required :success="valid"/>
                  </ValidationProvider>

                  <ValidationProvider name="Пароль" rules="required|min:6" v-slot="{ errors, valid }">
                    <v-text-field v-model="form.password" id="password" label="Пароль" name="password" prepend-icon="mdi-lock" type="password" :error-messages="errors" required
                                  :success="valid"/>
                  </ValidationProvider>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn @click="registerView" text color="indigo" small>
                  <v-icon>mdi-arrow-left</v-icon>
                  Регистрация
                </v-btn>
                <v-spacer/>
                <v-btn @click="login" color="primary" large :loading="loading">Войти</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  import {ValidationProvider} from "vee-validate";

  export default {
    components: {
      ValidationProvider
    },
    data() {
      return {
        form: {
          email: this.$store.state.currentEmail,
          password: null,
        },
        error: null,
        loading: false,
      }
    },
    methods: {
      registerView() {
        this.$router.push({name: 'signup'});
      },
      login() {
        this.error = null;
        this.loading = true;
        this.$store.dispatch('login', {
          username: this.form.email,
          password: this.form.password,
        })
          .then(() => {
            this.loading = false;
            this.$router.push({name: 'home'});
          })
          .catch(error => {
            if (error.response) {
              this.error = error.response.data.error;
            } else {
              console.log(error.message);
            }
            this.loading = false;
          });
      }
    }
  };
</script>
