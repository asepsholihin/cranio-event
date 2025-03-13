<template>
  <b-nav-item-dropdown
    class="dropdown-notification mr-25"
    menu-class="dropdown-menu-media"
    right
  >
    <template #button-content>
      <feather-icon
        :badge="countNotifications"
        badge-classes="bg-danger"
        class="text-body"
        icon="BellIcon"
        size="21"
      />
    </template>

    <!-- Header -->
    <li class="dropdown-menu-header">
      <div class="dropdown-header d-flex">
        <h4 class="notification-title mb-0 mr-auto">
          Notifications
        </h4>
        <b-badge
          pill
          variant="light-primary"
        >
          {{countNotifications}} New
        </b-badge>
      </div>

      
    </li>

    <!-- Notifications -->
    <li class="media-list overflow-auto">
      <!-- Account Notification -->
      <b-link
        v-for="notification in notifications"
        :key="notification.id"
      >
        <b-media :class="notification.is_read ? '' : 'bg-light-primary'" @click="openNotification(notification.id)">
          <template #aside>
            <b-avatar
              size="32"
              :src="notification.name"
              :text="avatarText(notification.name)"
            />
          </template>
          <p class="media-heading">
            <span class="font-weight-bolder">
              {{ notification.notification_type_name }}
            </span>
          </p>
          <small class="notification-text">{{ notification.message }}</small>
        </b-media>
      </b-link>
    </li>

    <!-- Cart Footer -->
    <li class="dropdown-menu-footer" v-if="notifications.length > 0"><b-button
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
      block @click="readAllNotification()"
    >Read all notifications</b-button>
    </li>
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BBadge, BMedia, BLink, BAvatar, BButton, BFormCheckbox,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { avatarText } from '@core/utils/filter'
import { readNotification, readAllNotification } from '@/network/notification'

export default {
  components: {
    BNavItemDropdown,
    BBadge,
    BMedia,
    BLink,
    BAvatar,
    BButton,
    BFormCheckbox,
    avatarText
  },
  directives: {
    Ripple,
  },
  setup() {
    return {avatarText}
  },
  props: {
    countNotifications: {
      type: Number,
      default: () => 0
    },
    notifications: {
      type: Array,
    },
  },
  methods: {
    openNotification(notificationId) {
      const vForm = {}
      vForm['id'] = notificationId
      readNotification(vForm).then(response => {
        this.$router.push({ 
          name: response.data['redirect'], 
          query: {
            order_no: response.data['order_no'],
            umroh_trip_id: response.data['umroh_trip_id'],
            package_umroh_trip_id: response.data['package_umroh_trip_id'],
            booking_id: response.data['booking_id']
          } 
        })
        this.$emit('fetch-notifications')
      })
      .catch(error => {
        this.$bvToast.toast(error, {
          title: `Error`,
          variant: 'danger',
          toaster: 'b-toaster-top-center',
          solid: true,
        })
      })
    },
    readAllNotification() {
      const vForm = {}
      readAllNotification(vForm).then(response => {
        this.$emit('fetch-notifications')
      })
      .catch(error => {
        this.$bvToast.toast(error, {
          title: `Error`,
          variant: 'danger',
          toaster: 'b-toaster-top-center',
          solid: true,
        })
      })
    },
  }
}
</script>

<style>

</style>
