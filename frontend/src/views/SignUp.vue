<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-6" :disabled="loading">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Регистрация</v-toolbar-title>
                <v-spacer/>
                <v-icon>mdi-account-plus</v-icon>
              </v-toolbar>
              <v-card-text>
                <v-alert :value="!!error" icon="mdi-alert-circle" prominent dense transition="scroll-x-reverse-transition" border="left" type="error">{{ error }}</v-alert>

                <v-form>
                  <ValidationProvider name="E-Mail" rules="required|email" v-slot="{ errors, valid }">
                    <v-text-field :disabled="isConfirming" v-model="form.email" :error-messages="errors.concat(mErrors.email || [])" label="E-Mail" name="login"
                                  prepend-icon="mdi-at"
                                  type="text" :readonly="authReadonly" @focus="removeReadonly" required :success="valid" v-bind="$attrs"/>
                  </ValidationProvider>

                  <ValidationProvider name="Пароль" rules="required|min:6" v-slot="{ errors, valid }">
                    <v-text-field :disabled="isConfirming" v-model="form.password" :error-messages="errors.concat(mErrors.password || [])" label="Пароль" name="password"
                                  prepend-icon="mdi-lock" type="password" :readonly="authReadonly" @focus="removeReadonly" required :success="valid"/>
                  </ValidationProvider>

                  <v-divider class="pb-5" v-if="isConfirming"></v-divider>

                  <ValidationProvider name="Код подтверждения" rules="required|min:6" v-slot="{ errors, valid }">
                    <v-text-field v-if="isConfirming" v-model="confirmCode" :error-messages="errors.concat(mErrors.token || [])" label="Код подтверждения" name="confirm"
                                  v-mask="'######'" append-icon="mdi-numeric" type="text" persistent-hint :hint="codeHint" outlined dense color="primary" :success="valid"/>
                  </ValidationProvider>
                </v-form>

              </v-card-text>
              <v-card-actions>
                <v-btn @click="loginView" text small color="indigo">
                  <v-icon>mdi-arrow-left</v-icon>
                  Авторизация
                </v-btn>
                <v-spacer/>
                <v-btn v-if="!isConfirming" @click="signup" color="primary" large :loading="loading">Зарегистрироваться</v-btn>
                <v-btn v-else @click="confirm" color="primary" large :loading="loading">Подтвердить</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  import axios from "axios";
  import {ValidationProvider} from "vee-validate";

  export default {
    components: {
      ValidationProvider
    },
    data() {
      return {
        form: {
          email: '',
          password: '',
        },
        confirmCode: '',
        codeHint: 'Проверьте электронную почту, и введите код подтверждения, указанный в письме',
        error: null,
        mErrors: [],
        isConfirming: false,
        authReadonly: true, // prevent from storing password
        loading: false,
      }
    },
    methods: {
      loginView() {
        this.$router.push({name: 'login'});
      },
      confirm() {
        this.error = null;
        this.mErrors = [];
        this.loading = true;
        axios
          .post('/auth/signup/confirm', {
            email: this.form.email,
            token: this.confirmCode
          })
          .then(() => {
            this.$store.commit('changeCurrentEmail', this.form.email);
            this.$router.push({name: 'login'});
            this.loading = false;
          })
          .catch(error => {
            if (error.response) {
              if (error.response.data.error) {
                this.error = error.response.data.error;
              } else if (error.response.data.errors) {
                this.mErrors = error.response.data.errors;
              }
            } else {
              console.log(error.message);
            }
            this.loading = false;
          });
      },
      signup() {
        this.error = null;
        this.mErrors = [];
        this.loading = true;

        axios
          .post('/auth/signup', this.form)
          .then(() => {
            this.$store.commit('changeCurrentEmail', this.form.email);
            this.isConfirming = true;
            this.loading = false;
          })
          .catch(error => {
            if (error.response) {
              if (error.response.data.error) {
                this.error = error.response.data.error;
              } else if (error.response.data.errors) {
                this.mErrors = error.response.data.errors;
              }
            } else {
              console.log(error.message);
            }
            this.loading = false;
          });
      },
      removeReadonly() {
        this.authReadonly = false;
      }
    }
  }
</script>
