<template>
  <div class="navbar-container d-flex content align-items-center">

    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link
          class="nav-link"
          @click="toggleVerticalMenuActive"
        >
          <feather-icon
            icon="MenuIcon"
            size="21"
          />
        </b-link>
      </li>
    </ul>

    <b-navbar-nav class="nav align-items-center ml-auto">
      <dark-Toggler class="d-none d-lg-block" />
      <notification-dropdown @fetch-notifications="fetchNotifications" :notifications="notifications" :count-notifications="countNotifications" />
      <user-dropdown />
    </b-navbar-nav>
  </div>
</template>

<script>
import {
  BLink, BNavbarNav,
} from 'bootstrap-vue'
import DarkToggler from './components/DarkToggler.vue'
import UserDropdown from './components/UserDropdown.vue'
import NotificationDropdown from './components/NotificationDropdown.vue'
import { getNotifications } from '@/network/notification'

export default {
  components: {
    BLink,

    // Navbar Components
    BNavbarNav,
    DarkToggler,
    UserDropdown,
    NotificationDropdown,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return { notifications: [], countNotifications: 0 }
  },
  created() {
    this.fetchNotifications()
  },
  methods: {
    fetchNotifications() {
      getNotifications({params: {}}).then(response => {
        this.notifications = response.data
        this.countNotifications = response.data.length
      })
    }
  }
}
</script>
