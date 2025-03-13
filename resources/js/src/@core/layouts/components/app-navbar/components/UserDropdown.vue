<template>
  <b-nav-item-dropdown
    right
    toggle-class="d-flex align-items-center dropdown-user-link"
    class="dropdown-user"
  >
    <template #button-content>
      <div class="d-sm-flex user-nav">
        <p class="user-name font-weight-bolder mb-0">
          {{ userData.name }}
        </p>
      </div>
    </template>

    <b-dropdown-item
      :to="{ name: 'profile-page'}"
      link-class="d-flex align-items-center"
    >
      <feather-icon
        size="16"
        icon="UserIcon"
        class="mr-50"
      />
      <span>Profile</span>
    </b-dropdown-item>

    <b-dropdown-divider />
    <b-dropdown-item
      link-class="d-flex align-items-center"
      @click="logout"
    >
      <feather-icon
        size="16"
        icon="LogOutIcon"
        class="mr-50"
      />
      <span>Logout</span>
    </b-dropdown-item></b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BDropdownItem, BDropdownDivider, BAvatar,
} from 'bootstrap-vue'
import { removeUserData, getUserData } from '@/auth/utils'
import authService from '@/auth/service/'

export default {
  components: {
    BNavItemDropdown,
    BDropdownItem,
    BDropdownDivider,
    BAvatar,
  },
  setup() {
      const userData = getUserData()
      return {
          userData
      }
  },
  methods: {
    logout() {
        authService.logout()
        .then(response => {
            removeUserData()
            this.$router.push({ name: 'auth-login' })
        })
    },
  },
}
</script>
