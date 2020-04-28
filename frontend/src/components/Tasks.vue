<script>
import { createNamespacedHelpers, mapGetters as mapRootGetters } from 'vuex'
import TasksNewItem from './TasksNewItem.vue'

const { mapState, mapActions } = createNamespacedHelpers('tasks')
const WAIT = 'wait'
const EXECUTE = 'execute'
const COMPLETE = 'complete'
const ERROR = 'error'
const CANCEL = 'cancel'

export default {
  components: {
    TasksNewItem
  },
  data() {
    return {
      grid: {
        loading: false,
        headers: [
          { text: 'Пользователь', value: 'user_email' },
          { text: 'Дата', value: 'pushed_at' },
          { text: 'Задача', value: 'name' },
          { text: 'Статус', value: 'status' },
          { text: '', value: 'data-table-expand' }
        ],
        options: {},
        rowsPerPage: [
          10,
          20,
          30,
          { text: '$vuetify.dataIterator.rowsPerPageAll', value: -1 }
        ]
      }
    }
  },
  computed: {
    ...mapRootGetters(['userId']),
    ...mapState(['items', 'processing'])
  },
  async mounted() {
    this.$watch(
      'grid.options',
      async () => {
        this.getTasksByGrid()
      },
      { deep: true }
    )
    this.wsNotificationsInit()

    this.getTasksByGrid()
  },
  methods: {
    ...mapActions(['getTasks', 'cancelTask', 'wsNotificationsInit']),
    statusText(status) {
      switch (status) {
        case WAIT:
          return 'В очереди'
        case EXECUTE:
          return 'В процессе'
        case COMPLETE:
          return 'Выполнено'
        case ERROR:
          return 'Ошибка'
        case CANCEL:
          return 'Отменена'
      }
    },
    statusChip(status) {
      switch (status) {
        case WAIT:
          return 'cyan'
        case EXECUTE:
          return 'teal'
        case COMPLETE:
          return 'light-green'
        case ERROR:
          return 'deep-orange'
        case CANCEL:
          return 'blue-grey'
      }
    },
    statusIcon(status) {
      switch (status) {
        case WAIT:
          return 'mdi-clock'
        case EXECUTE:
          return 'mdi-progress-clock'
        case COMPLETE:
          return 'mdi-check-circle'
        case ERROR:
          return 'mdi-alert-circle'
        case CANCEL:
          return 'mdi-cancel'
      }
    },
    statusQueuePosition(status, position) {
      if (status === WAIT) {
        return ' (' + position + ')'
      }
      return ''
    },
    isExecuting(status) {
      return status === EXECUTE
    },
    canCancel(status, userId) {
      return [WAIT, EXECUTE].includes(status) && userId === this.userId
    },
    async cancelTaskClick(id) {
      const taskName = this.items.rows.find((item) => item.id === id).name
      const res = await this.$dialog.confirm({
        text: `вы уверены что хотите отменить задачу "${taskName}"?`,
        title: 'Подтверждение'
      })

      if (res) {
        try {
          this.grid.loading = true
          await this.cancelTask(id)
          this.grid.loading = false
        } catch (error) {
          if (error.response) {
            const errorObj = error.response.data
            if (errorObj && errorObj.message) {
              this.$dialog.error({
                text: errorObj.message,
                title: 'Ошибка'
              })
            }
          } else {
            console.log(error.message)
          }
          this.grid.loading = false
        }
      }
    },
    async getTasksByGrid() {
      this.grid.loading = true
      await this.getTasks(this.grid.options)
      this.grid.loading = false
    }
  }
}
</script>

<template>
  <v-layout child-flex>
    <v-card>
      <v-card-text>
        <v-data-table
          :headers="grid.headers"
          :items="items.rows"
          :options.sync="grid.options"
          :server-items-length="items.total"
          :loading="grid.loading || processing"
          class="elevation-1"
          show-expand
        >
          <template #top>
            <div class="pa-2">
              <tasks-new-item />
            </div>
          </template>

          <template #item.user_email="{ item }">
            <p
              :class="
                item.author_id === userId
                  ? 'primary--text font-weight-bold'
                  : ''
              "
            >
              {{ item.author_email }}
            </p>
          </template>

          <template #item.name="{ item }">
            {{ item.name }}
            <v-icon v-if="item.visibility === 'private'">
              mdi-lock
            </v-icon>
          </template>

          <template #item.status="{ item }">
            <v-chip :color="statusChip(item.status)" text-color="white">
              <v-avatar v-if="isExecuting(item.status)" class="mr-1">
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

          <template #item.data-table-expand="{ item, isExpanded, expand }">
            <v-layout class="align-center">
              <v-tooltip v-if="canCancel(item.status, item.author_id)" left>
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
                    <v-icon dark>
                      mdi-cancel
                    </v-icon>
                  </v-btn>
                </template>
                <span>Отменить выполнение задачи</span>
              </v-tooltip>
              <v-btn
                v-if="item.status === 'error' && !isExpanded"
                color="error"
                fab
                x-small
                @click="expand(true)"
              >
                <v-icon>mdi-alert-circle-outline</v-icon>
              </v-btn>
              <v-btn
                v-if="item.status === 'error' && isExpanded"
                color="primary"
                fab
                x-small
                @click="expand(false)"
              >
                <v-icon>mdi-arrow-collapse-up</v-icon>
              </v-btn>
            </v-layout>
          </template>

          <template #expanded-item="{ headers, item }">
            <td :colspan="headers.length">
              <v-card v-if="item.error_message" class="my-3 red lighten-5" tile>
                <v-card-title class="error-message-trace red--text darken-4">
                  {{ item.error_message }}
                </v-card-title>
                <v-divider v-if="item.error_trace" />
                <v-card-text
                  v-if="item.error_trace"
                  class="error-message-trace brown--text darken-4"
                >
                  {{ item.error_trace }}
                </v-card-text>
              </v-card>
            </td>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-layout>
</template>

<style lang="stylus">
.error-message-trace
  white-space pre-line
</style>
