<script>
export default {
  name: 'App'
}
</script>

<template>
  <v-fade-transition hide-on-leave>
    <router-view />
  </v-fade-transition>
</template>

<style lang="stylus">
html
  overflow-y auto !important
</style>
