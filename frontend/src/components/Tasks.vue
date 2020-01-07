<template>
  <v-layout child-flex>
    <v-card>
      <v-card-text>
        <v-data-table
          :headers="grid.headers"
          :items="items.rows"
          :options.sync="grid.options"
          :server-items-length="items.total"
          :loading="grid.loading"
          class="elevation-1"
        >
          <template #item.status="{ item }">
            <v-chip :color="statusChip(item.status)" text-color="white">
              <v-avatar v-if="isExecuting(item.status)">
                <v-progress-circular
                  class="caption"
                  color="teal lighten-5"
                  :rotate="-90"
                  :size="25"
                  :width="2"
                  :value="item.process_percent"
                >
                  {{ item.process_percent }}
                </v-progress-circular>
              </v-avatar>
              <v-avatar v-else class="mr-1">
                <v-icon>{{ statusIcon(item.status) }}</v-icon>
              </v-avatar>
              {{ statusText(item.status)
              }}{{ statusQueuePosition(item.status, item.position) }}
            </v-chip>
          </template>

          <template #item.actions="{ item }">
            <v-tooltip left v-if="canCancel(item.status, item.user_id)">
              <template #activator="{ on }">
                <v-btn
                  text
                  icon
                  dark
                  small
                  color="red"
                  v-on="on"
                  @click="cancelTaskClick(item.id)"
                >
                  <v-icon dark>mdi-cancel</v-icon>
                </v-btn>
              </template>
              <span>Отменить выполнение задачи</span>
            </v-tooltip>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-layout>
</template>

<script>
import { createNamespacedHelpers } from "vuex";
import { mapGetters as mapRootGetters } from "vuex";
const { mapState, mapActions } = createNamespacedHelpers("tasks");
const WAIT = "wait";
const EXECUTE = "execute";
const COMPLETE = "complete";
const ERROR = "error";
const CANCEL = "cancel";

export default {
  data() {
    return {
      grid: {
        loading: false,
        headers: [
          {
            text: "ID",
            align: "left",
            value: "id"
          },
          { text: "Дата", value: "pushed_at" },
          { text: "Задача", value: "name" },
          { text: "Статус", value: "status" },
          { text: "", value: "actions", sortable: false }
        ],
        options: {},
        rowsPerPage: [
          10,
          20,
          30,
          { text: "$vuetify.dataIterator.rowsPerPageAll", value: -1 }
        ]
      }
    };
  },
  computed: {
    ...mapRootGetters(["userId"]),
    ...mapState(["items"])
  },
  async mounted() {
    this.$watch(
      "grid.options",
      async () => {
        this.getTasksByGrid();
      },
      { deep: true }
    );

    this.getTasksByGrid();
  },
  methods: {
    ...mapActions(["getTasks"]),
    statusText(status) {
      switch (status) {
        case WAIT:
          return "В очереди";
        case EXECUTE:
          return "В процессе";
        case COMPLETE:
          return "Выполнено";
        case ERROR:
          return "Ошибка";
        case CANCEL:
          return "Отменена";
      }
    },
    statusChip(status) {
      switch (status) {
        case WAIT:
          return "cyan";
        case EXECUTE:
          return "teal";
        case COMPLETE:
          return "light-green";
        case ERROR:
          return "deep-orange";
        case CANCEL:
          return "blue-grey";
      }
    },
    statusIcon(status) {
      switch (status) {
        case WAIT:
          return "mdi-clock";
        case EXECUTE:
          return "mdi-progress-clock";
        case COMPLETE:
          return "mdi-check-circle";
        case ERROR:
          return "mdi-alert-circle";
        case CANCEL:
          return "mdi-cancel";
      }
    },
    statusQueuePosition(status, position) {
      if (status === WAIT) {
        return " (" + position + ")";
      }
      return "";
    },
    isExecuting(status) {
      return status === EXECUTE;
    },
    canCancel(status, userId) {
      return [WAIT, EXECUTE].includes(status) && userId === this.userId;
    },
    async cancelTaskClick(id) {
      console.log(id);
    },
    async getTasksByGrid() {
      this.grid.loading = true;
      await this.getTasks(this.grid.options);
      this.grid.loading = false;
    }
  }
};
</script>
