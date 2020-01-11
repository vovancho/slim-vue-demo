<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-6" :disabled="loading">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Авторизация</v-toolbar-title>
                <v-spacer />
                <v-icon>mdi-login</v-icon>
              </v-toolbar>
              <v-card-text>
                <v-alert
                  :value="!!error"
                  icon="mdi-alert-circle"
                  prominent
                  dense
                  transition="scroll-x-reverse-transition"
                  border="left"
                  type="error"
                  >{{ error }}
                </v-alert>

                <v-form>
                  <ValidationObserver ref="login">
                    <ValidationProvider
                      :name="form.email.label"
                      :rules="form.email.rules"
                      v-slot="{ errors, valid }"
                    >
                      <v-text-field
                        v-model="form.email.value"
                        :label="form.email.label"
                        name="login"
                        prepend-icon="mdi-account"
                        type="text"
                        :error-messages="errors"
                        required
                        :success="valid"
                      />
                    </ValidationProvider>

                    <ValidationProvider
                      :name="form.password.label"
                      :rules="form.password.rules"
                      v-slot="{ errors, valid }"
                    >
                      <v-text-field
                        v-model="form.password.value"
                        id="password"
                        :label="form.password.label"
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error-messages="errors"
                        required
                        :success="valid"
                      />
                    </ValidationProvider>
                  </ValidationObserver>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn @click="registerView" text color="indigo" small>
                  <v-icon>mdi-arrow-left</v-icon>
                  Регистрация
                </v-btn>
                <v-spacer />
                <v-btn
                  @click="loginForm"
                  color="primary"
                  large
                  :loading="loading"
                  >Войти
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import form from "../mixins/form";
import { mapActions, mapGetters } from "vuex";

export default {
  mixins: [form],
  data() {
    return {
      form: {
        email: {
          label: "E-Mail",
          value: this.$store.state.currentEmail,
          rules: { required: true, email: true },
          error: null
        },
        password: {
          label: "Пароль",
          value: "",
          rules: { required: true, min: 6 },
          error: null
        }
      },
      error: null,
      loading: false
    };
  },
  computed: {
    ...mapGetters(["isLogged"])
  },
  methods: {
    ...mapActions(["login"]),
    registerView() {
      this.$router.push({ name: "signup" });
    },
    async loginForm() {
      this.error = null;

      const valid = await this.$refs.login.validate();

      if (valid) {
        try {
          this.loading = true;
          let preparedForm = this.prepareForm(this.form);

          await this.login(preparedForm);
          this.loading = false;
          await this.$router.push({ name: "home" });
        } catch (error) {
          if (error.response) {
            this.error = error.response.data.error || error.response.data.message;
          } else {
            console.log(error.message);
          }
          this.loading = false;
        }
      }
    }
  },
  created() {
    if (this.isLogged) {
      this.$router.push({ name: "home" });
    }
  }
};
</script>
