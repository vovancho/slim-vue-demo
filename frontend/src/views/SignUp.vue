<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-6" :disabled="loading">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Регистрация</v-toolbar-title>
                <v-spacer />
                <v-icon>mdi-account-plus</v-icon>
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

                <v-text-field label="hidden" style="display:none" /> <!-- Disable Password Manager of Google Chrome -->

                <ValidationObserver ref="signup">
                  <ValidationProvider
                    :name="signUpForm.email.label"
                    :rules="signUpForm.email.rules"
                    v-slot="{ errors, valid }"
                  >
                    <v-text-field
                      :disabled="isConfirming"
                      v-model="signUpForm.email.value"
                      :error-messages="
                        mergeErrors(errors, signUpForm.email.error)
                      "
                      :label="signUpForm.email.label"
                      name="login"
                      prepend-icon="mdi-at"
                      type="text"
                      required
                      :success="valid"
                      v-bind="$attrs"
                      autocomplete="new-password"
                      ref="email"
                      @keyup.enter="signupClicked"
                    />
                  </ValidationProvider>

                  <ValidationProvider
                    :name="signUpForm.password.label"
                    :rules="signUpForm.password.rules"
                    v-slot="{ errors, valid }"
                  >
                    <v-text-field
                      :disabled="isConfirming"
                      v-model="signUpForm.password.value"
                      :error-messages="
                        mergeErrors(errors, signUpForm.password.error)
                      "
                      :label="signUpForm.password.label"
                      name="password"
                      prepend-icon="mdi-lock"
                      type="password"
                      required
                      :success="valid"
                      autocomplete="new-password"
                      @keyup.enter="signupClicked"
                    />
                  </ValidationProvider>
                </ValidationObserver>

                <v-divider class="pb-5" v-if="isConfirming"></v-divider>

                <ValidationObserver ref="confirm">
                  <ValidationProvider
                    :name="confirmForm.token.label"
                    :rules="confirmForm.token.rules"
                    v-slot="{ errors, valid }"
                  >
                    <v-text-field
                      v-if="isConfirming"
                      v-model="confirmForm.token.value"
                      :error-messages="
                        mergeErrors(errors, confirmForm.token.error)
                      "
                      :label="confirmForm.token.label"
                      name="confirm"
                      v-mask="'######'"
                      append-icon="mdi-numeric"
                      type="text"
                      persistent-hint
                      :hint="confirmForm.token.hint"
                      outlined
                      dense
                      color="primary"
                      :success="valid"
                      ref="token"
                      class="confirm-input"
                      @keyup.enter="confirmClicked"
                    >
                      <template v-slot:append-outer>
                        <v-btn large text @click="cancelConfirm">Отмена</v-btn>
                      </template>
                    </v-text-field>
                  </ValidationProvider>
                </ValidationObserver>
              </v-card-text>
              <v-card-actions>
                <v-btn @click="loginView" text small color="indigo">
                  <v-icon>mdi-arrow-left</v-icon>
                  Авторизация
                </v-btn>
                <v-spacer />
                <v-btn
                  v-if="!isConfirming"
                  @click="signupClicked"
                  color="primary"
                  large
                  :loading="loading"
                  >Зарегистрироваться
                </v-btn>
                <v-btn
                  v-else
                  @click="confirmClicked"
                  color="primary"
                  large
                  :loading="loading"
                  >Подтвердить
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
import Vue from "vue";
import form from "../mixins/form";
import { mapGetters, mapActions } from "vuex";

export default {
  mixins: [form],
  data() {
    return {
      signUpForm: {
        email: {
          label: "E-Mail",
          value: "",
          rules: { required: true /*, email: true*/ },
          error: null
        },
        password: {
          label: "Пароль",
          value: "",
          rules: { required: true, min: 6 },
          error: null
        }
      },
      confirmForm: {
        email: {
          label: "E-Mail",
          value: "",
          rules: { required: true, email: true },
          error: null
        },
        token: {
          label: "Код подтверждения",
          value: "",
          rules: { required: true, min: 6 },
          error: null,
          hint:
            "Проверьте электронную почту, и введите код подтверждения, указанный в письме"
        }
      },
      error: null,
      isConfirming: false,
      loading: false
    };
  },
  computed: {
    ...mapGetters(["isLogged"])
  },
  methods: {
    ...mapActions(["signup", "signupConfirm"]),
    loginView() {
      this.$router.push({ name: "login" });
    },
    async confirmClicked() {
      this.error = null;

      const valid = await this.$refs.confirm.validate();

      if (valid) {
        try {
          this.loading = true;
          let preparedForm = this.prepareForm(this.confirmForm);

          await this.signupConfirm(preparedForm);
          await this.$router.push({ name: "login" });
          this.loading = false;
        } catch (error) {
          if (error.response) {
            let errorObj = error.response.data.error;
            if (errorObj) {
              if (errorObj.description) {
                this.error = errorObj.description;
              }
              if (errorObj.formErrors) {
                this.confirmForm = this.assignErrors(
                  this.confirmForm,
                  errorObj.formErrors
                );
              }
            }
          } else {
            console.log(error.message);
          }
          this.loading = false;
        }
      }
    },
    async signupClicked() {
      this.error = null;
      const valid = await this.$refs.signup.validate();

      if (valid) {
        try {
          this.loading = true;
          let preparedForm = this.prepareForm(this.signUpForm);

          await this.signup(preparedForm);
          this.$store.commit("changeCurrentEmail", this.signUpForm.email.value);
          this.confirming();
          this.loading = false;
        } catch (error) {
          if (error.response) {
            let errorObj = error.response.data.error;
            if (errorObj) {
              if (errorObj.description) {
                this.error = errorObj.description;
              }
              if (errorObj.formErrors) {
                this.signUpForm = this.assignErrors(
                  this.signUpForm,
                  errorObj.formErrors
                );
              }
            }
          } else {
            console.log(error.message);
          }
          this.loading = false;
        }
      }
    },
    cancelConfirm() {
      this.isConfirming = false;
      this.confirmForm.token.value = "";
      this.$refs.confirm.reset();
    },
    confirming() {
      this.confirmForm.email.value = this.signUpForm.email.value;
      this.isConfirming = true;
      Vue.nextTick(() => {
        this.$refs.token.focus();
      });
    }
  },
  created() {
    if (this.isLogged) {
      this.$router.push({ name: "home" });
    }
  },
  mounted() {
    this.$refs.email.focus();
  }
};
</script>

<style lang="stylus">
.confirm-input
  .v-input__append-outer
    margin 0 !important
</style>
