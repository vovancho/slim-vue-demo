<script>
import form from '../mixins/form'
import { mapActions, mapGetters } from 'vuex'

export default {
  mixins: [form],
  data() {
    return {
      form: {
        email: {
          label: 'E-Mail',
          value: this.$store.state.currentEmail,
          rules: { required: true, email: true },
          error: null
        },
        password: {
          label: 'Пароль',
          value: '',
          rules: { required: true, min: 6 },
          error: null
        }
      },
      error: null,
      loading: false
    }
  },
  computed: {
    ...mapGetters(['isLogged'])
  },
  created() {
    if (this.isLogged) {
      this.$router.push({ name: 'home' })
    }
  },
  mounted() {
    this.$refs.email.focus()
  },
  methods: {
    ...mapActions(['login']),
    registerView() {
      this.$router.push({ name: 'signup' })
    },
    async loginForm() {
      this.error = null

      const valid = await this.$refs.login.validate()

      if (valid) {
        try {
          this.loading = true
          const preparedForm = this.prepareForm(this.form)

          await this.login(preparedForm)
          this.loading = false
          await this.$router.push({ name: 'home' })
        } catch (error) {
          if (error.response) {
            const errorObj = error.response.data
            if (errorObj && errorObj.message) {
              this.error = errorObj.message
            }
          } else {
            console.log(error.message)
          }
          this.loading = false
        }
      }
    }
  }
}
</script>

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
                >
                  {{ error }}
                </v-alert>

                <v-form>
                  <v-text-field label="hidden" style="display:none" />
                  <!-- Disable Password Manager of Google Chrome -->

                  <ValidationObserver ref="login">
                    <ValidationProvider
                      v-slot="{ errors, valid }"
                      :name="form.email.label"
                      :rules="form.email.rules"
                    >
                      <v-text-field
                        ref="email"
                        v-model="form.email.value"
                        :label="form.email.label"
                        name="login"
                        prepend-icon="mdi-account"
                        type="text"
                        :error-messages="errors"
                        required
                        :success="valid"
                        autocomplete="new-password"
                        @keyup.enter="loginForm"
                      />
                    </ValidationProvider>

                    <ValidationProvider
                      v-slot="{ errors, valid }"
                      :name="form.password.label"
                      :rules="form.password.rules"
                    >
                      <v-text-field
                        v-model="form.password.value"
                        :label="form.password.label"
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error-messages="errors"
                        required
                        :success="valid"
                        autocomplete="new-password"
                        @keyup.enter="loginForm"
                      />
                    </ValidationProvider>
                  </ValidationObserver>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="indigo" small @click="registerView">
                  <v-icon>mdi-arrow-left</v-icon>
                  Регистрация
                </v-btn>
                <v-spacer />
                <v-btn
                  color="primary"
                  large
                  :loading="loading"
                  @click="loginForm"
                >
                  Войти
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>
