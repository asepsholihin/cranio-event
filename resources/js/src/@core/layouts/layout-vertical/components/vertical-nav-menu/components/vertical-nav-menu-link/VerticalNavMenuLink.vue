<template>
  <li
    v-if="isMenuRender(item.permission || true)"
    class="nav-item"
    :class="{
      'active': isActive,
      'disabled': item.disabled
    }"
  >
    <b-link
      v-bind="linkProps"
      class="d-flex align-items-center"
    >
      <feather-icon :icon="item.icon || 'CircleIcon'" />
      <span class="menu-title text-truncate">{{ item.title }}</span>
      <b-badge
        v-if="item.tagCreditReceipt && $store.state.app.badgeCreditReceipt > 0"
        pill
        :variant="item.tagVariant || 'primary'"
        class="mr-1 ml-auto"
      >
      {{ $store.state.app.badgeCreditReceipt }}
      </b-badge>
      <b-badge
        v-if="item.tagOrderChangeRequest && $store.state.app.badgeOrderChangeRequest > 0"
        pill
        :variant="item.tagVariant || 'primary'"
        class="mr-1 ml-auto"
      >
      {{ $store.state.app.badgeOrderChangeRequest }}
      </b-badge>
      <b-badge
        v-if="item.tagPaymentApproval && $store.state.app.badgePaymentApproval > 0"
        pill
        :variant="item.tagVariant || 'primary'"
        class="mr-1 ml-auto"
      >
      {{ $store.state.app.badgePaymentApproval }}
      </b-badge>
    </b-link>
  </li>
</template>

<script>
import { BLink, BBadge } from 'bootstrap-vue'
import useVerticalNavMenuLink from './useVerticalNavMenuLink'
import mixinVerticalNavMenuLink from './mixinVerticalNavMenuLink'
import { hasPermission } from '@/auth/utils'

export default {
  components: {
    BLink,
    BBadge,
  },
  mixins: [mixinVerticalNavMenuLink],
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { isActive, linkProps, updateIsActive } = useVerticalNavMenuLink(props.item)
    const isMenuRender = permission => {
        if (permission == true)
            return true
        return hasPermission(permission)
    }

    return {
      isActive,
      linkProps,
      updateIsActive,
      isMenuRender,
    }
  },

}
</script>
