<template>
  <v-fade-transition hide-on-leave>
    <router-view />
  </v-fade-transition>
</template>

<script>
export default {
  name: "App"
};
</script>

<style lang="stylus">
  html
    overflow-y auto !important
</style>
