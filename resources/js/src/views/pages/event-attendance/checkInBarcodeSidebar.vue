<template>
  <b-sidebar id="checkin-barcode-sidebar" :visible="isBarcodeSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData" @shown="resetUserData"
    @change="(val) => $emit('update:is-barcode-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">Check In Barcode</h5>
        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />
      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetUserData" autocomplete="nope">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <!-- Check In -->
          <validation-provider #default="{ errors }" name="Barcode" vid="barcode">
            <b-form-group label="Scan Barcode">
              <b-form-input v-model="formData.barcode" autofocus name="barcode" ref="barcodeField" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

        <b-alert variant="success" show v-if="attendee.checkIn" >
            <div class="alert-body">
                {{ attendee.item.name }} <br/>
                {{formatDateTime(attendee.checkInAt)}}
              </div>
        </b-alert>

          <!-- Form Actions -->
          <div class="d-flex mt-2">
            <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary"
              @click="hide">
              Close
            </b-button>
          </div>

        </b-form>
      </validation-observer>
    </template>

  </b-sidebar>
</template>

<script>
import { BSidebar, BAvatar, BMedia, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postAttendee } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'
import { avatarText, formatDateTime } from '@core/utils/filter'
import _ from 'lodash';

export default {
  components: {
    BMedia,
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckboxGroup,
    vSelect,
    flatPickr,
    BAvatar,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isBarcodeSidebarActive',
    event: 'update:is-barcode-sidebar-active',
  },
  props: {
    eventAttendance: {
      type: Object,
      required: true,
    },
    isBarcodeSidebarActive: {
      type: Boolean,
      required: true,
    }
  },
  data() {
    return {
      password: '',
      passwordFieldTypeNew: 'password',
      required,
      min,
      numeric,
      email,
      avatarText,
      formatDateTime,
      formData: {},
      attendee:{'checkIn':false}
    }
  },
  computed: {
    passwordToggleIconNew() {
      return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  methods: {
    togglePasswordNew() {
      this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
    },
    resetUserData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.$refs.barcodeField.focus()
      this.attendee = {'checkIn':false}
    },
    onSubmit() {
      this.attendee = {'checkIn':false, 'item': {}, 'checkInAt': ''}
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        vForm.append('event_id', this.eventAttendance.id)
        vForm.append('act', 'checkinBarcode')
        postAttendee(vForm).then(response => {
          this.$emit('refetch-data')
          this.formData.barcode = ''
          this.attendee['checkIn'] =true
          this.attendee['item'] =response.data.attendee
          this.attendee['checkInAt'] =response.data.checkInAt
          resetUserData()
        })
        .catch(error => {
            this.$refs.refObsForm.setErrors(error.response.data)
            this.formData.barcode = ''
            this.attendee.checkIn = false
          })
      })
    }
  },

}
</script>
