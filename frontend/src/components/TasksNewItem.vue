<template>
  <v-dialog persistent v-model="show" max-width="600px">
    <template v-slot:activator="{ on: dialog }">
      <v-tooltip right>
        <template v-slot:activator="{ on: tooltip }">
          <v-fab-transition>
            <v-btn
              color="success"
              dark
              fab
              left
              top
              v-on="{ ...dialog, ...tooltip }"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>Добавить новую задачу</span>
      </v-tooltip>
    </template>
    <v-card :disabled="loading">
      <v-card-title>
        <span class="headline">Добавить задачу</span>
      </v-card-title>

      <v-divider />

      <v-card-text>
        <v-container grid-list-md>
          <v-layout wrap>
            <v-flex xs12>
              <v-row>
                <v-col cols="12">
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

                  <ValidationObserver ref="tasks-new">
                    <ValidationProvider
                      :name="form.name.label"
                      :rules="form.name.rules"
                      v-slot="{ errors, valid }"
                    >
                      <v-text-field
                        v-model="form.name.value"
                        :label="form.name.label"
                        name="name"
                        prepend-icon="mdi-account"
                        type="text"
                        :error-messages="mergeErrors(errors, form.name.error)"
                        required
                        ref="name"
                      />
                    </ValidationProvider>

                    <ValidationProvider
                      :name="form.type.label"
                      :rules="form.type.rules"
                      v-slot="{ errors }"
                    >
                      <v-select
                        v-model="form.type.value"
                        :label="form.type.label"
                        :items="types"
                        name="type"
                        prepend-icon="mdi-lock"
                        :error-messages="mergeErrors(errors, form.type.error)"
                        required
                      ></v-select>
                    </ValidationProvider>
                  </ValidationObserver>
                </v-col>
              </v-row>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-text>

      <v-divider />

      <v-card-actions>
        <v-spacer />
        <v-btn color="blue darken-1" text @click="show = false">Закрыть</v-btn>
        <v-btn
          color="blue darken-1"
          text
          :loading="loading"
          @click="createTaskClick"
          >Добавить</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import Vue from "vue";
import form from "../mixins/form";
import { createNamespacedHelpers } from "vuex";

const { mapActions } = createNamespacedHelpers("tasks");

export default {
  mixins: [form],
  data() {
    return {
      types: [
        { value: "public", text: "Общая" },
        { value: "private", text: "Приватная" }
      ],
      form: {
        name: {
          label: "Наименование",
          value: "",
          rules: { required: true, max: 255 },
          error: null
        },
        type: {
          label: "Тип задачи",
          value: "",
          rules: { required: true, oneOf: ["public", "private"] },
          error: null
        }
      },
      show: false,
      loading: false,
      error: null
    };
  },
  methods: {
    ...mapActions(["createTask"]),
    async createTaskClick() {
      this.error = null;

      const valid = await this.$refs["tasks-new"].validate();

      if (valid) {
        try {
          this.loading = true;
          let preparedForm = this.prepareForm(this.form);

          await this.createTask(preparedForm);
          this.loading = false;
          this.show = false;
        } catch (error) {
          if (error.response) {
            let errorObj = error.response.data.error;
            if (errorObj) {
              if (errorObj.description) {
                this.error = errorObj.description;
              }
              if (errorObj.formErrors) {
                this.form = this.assignErrors(this.form, errorObj.formErrors);
              }
            }
          } else {
            console.log(error.message);
          }
          this.loading = false;
        }
      }
    },
    resetForm() {
      this.form.name.value = "";
      this.form.type.value = "public";
    },
    focusNameInput() {
      this.$nextTick(() => {
        this.$refs.name.focus();
      });
    }
  },
  watch: {
    show: function(val) {
      if (val) {
        Vue.nextTick(() => {
          this.resetForm();
          this.$refs["tasks-new"].reset();
          this.focusNameInput();
        });
      }
    }
  }
};
</script>
