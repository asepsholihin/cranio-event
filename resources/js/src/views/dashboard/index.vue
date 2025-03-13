<template>
  <section id="dashboard-ecommerce">
    <b-row class="match-height">
      <b-col>
        <statistics :data="dashboardData.statistics" />
        <bookings :data="dashboardData.bookings" />
      </b-col>
    </b-row>
  </section>
</template>

<script>
import { BRow, BCol } from 'bootstrap-vue'
import Statistics from './Statistics.vue'
import Bookings from './Bookings.vue'
import { getData } from '@/network/dashboard'
export default {
  components: {
    BRow,
    BCol,
    Bookings,
    Statistics,
  },
  data() {
    return {
      dashboardData:{}
    }
  },
  created() {
    getData().then(response => {
            this.dashboardData = response.data
      })
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/dashboard-ecommerce.scss';
</style>
