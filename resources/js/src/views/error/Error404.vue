<template>
  <!-- Error page-->
  <div class="misc-wrapper">

    <div class="misc-inner p-2 p-sm-3">
      <div class="w-100 text-center">
        <h2 class="mb-1">
          Page Not Found 🕵🏻‍♀️
        </h2>
        <p class="mb-2">
          Oops! :) The requested URL was not found on this server.
        </p>

        <b-button
          variant="primary"
          class="mb-2 btn-sm-block"
          :to="{path:'/'}"
        >
          Back to home
        </b-button>
      </div>
    </div>
  </div>
<!-- / Error page-->
</template>

<script>
/* eslint-disable global-require */
import { BButton } from 'bootstrap-vue'

export default {
  components: {
    BButton,
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/page-misc.scss';
</style>
