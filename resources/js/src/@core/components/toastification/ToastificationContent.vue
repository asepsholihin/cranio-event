<template>
  <div class="toastification">
    <div class="d-flex align-items-start">
      <b-avatar
        :variant="variant"
        size="1.8rem"
        class="mr-75 flex-shrink-0"
      >
        <feather-icon
          :icon="icon"
          size="15"
        />
      </b-avatar>
      <div class="d-flex flex-grow-1">
        <div>
          <h5
            v-if="title"
            class="mb-0 font-weight-bolder toastification-title"
            :class="`text-${variant}`"
            v-text="title"
          />
          <small
            v-if="text"
            :variant="variant"
            class="d-inline-block text-body"
            v-text="text"
          />
        </div>
        <span
          class="cursor-pointer toastification-close-icon ml-auto "
          @click="$emit('close-toast')"
        >
          <feather-icon
            v-if="!hideClose"
            icon="XIcon"
            class="text-body"
          />
        </span>
      </div>
    </div>
    <div v-if="button" class="mt-1 text-right">
      <b-link class="link" @click.stop="openLink">Details</b-link>
    </div>
  </div>
</template>

<script>
import { BAvatar, BLink } from 'bootstrap-vue'

export default {
  components: {
    BAvatar, BLink
  },
  props: {
    variant: {
      type: String,
      default: 'primary',
    },
    icon: {
      type: String,
      default: null,
    },
    title: {
      type: String,
      default: null,
    },
    text: {
      type: String,
      default: null,
    },
    hideClose: {
      type: Boolean,
      default: false,
    },
    button: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    openLink() {
      this.$emit('close-toast')
      this.$emit("click")
    },
  },
}
</script>

<style lang="scss" scoped>
.toastification-close-icon,
.toastification-title {
  line-height: 26px;
}

.toastification-title {
  color: inherit;
}
.link {
  font-size: 0.875em;
  text-decoration: underline;
}
</style>
