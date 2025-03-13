<template>
  <b-sidebar id="import-sidebar" :visible="isImportSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData"
    @change="(val) => $emit('update:is-import-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Import Konfirmasi Keberangkatan
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <div class="m-2">
        <a href="" @click.prevent="exportDeparture()">Download Template Import Keberangkatan</a>
      </div>

      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetUserData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body" v-html="errors[0]"> 
              </div>
            </b-alert>
          </validation-provider>

          <!-- File -->
          <validation-provider #default="{ errors }" vid="file" name="File Import" rules="required">
            <b-form-group label="File">
              <b-form-file accept=".csv, .xlsx" v-model="formData.file"
                :state="errors.length > 0 ? false : null" placeholder="Choose a file or drop it here..."
                drop-placeholder="Drop file here..." />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Form Actions -->
          <div class="d-flex mt-2">
            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit" :disabled="isButtonLoading">
              <b-spinner small v-show="isButtonLoading" /> Import
            </b-button>
            <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary"
              @click="hide">
              Cancel
            </b-button>
          </div>

        </b-form>
      </validation-observer>
    </template>
  </b-sidebar>
</template>

<script>
import { BSidebar, BForm, BFormGroup, BFormInput, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { importDeparture, exportDeparture } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'

export default {
  components: {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BFormFile,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    vSelect,
    flatPickr,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isImportSidebarActive',
    event: 'update:is-import-sidebar-active',
  },
  props: {
    eventAttendance: {
      type: Object,
      required: true,
    },
    isImportSidebarActive: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      isButtonLoading: false,
      required,
      numeric,
      email,
      formData: {},
    }
  },
  methods: {
    resetUserData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.isButtonLoading = false
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        this.isButtonLoading = true
        importDeparture(vForm).then(response => {
          this.$bvToast.toast(`Departure Confirmation has been imported successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$emit('refetch-data')
          this.$emit('update:is-import-sidebar-active', false)
          this.isButtonLoading = false
        })
        .catch(error => {
            if (error.response.data.errors) {
              this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
              this.$refs.refObsForm.setErrors(error.response.data)
            }
            this.formData = {}
            this.isButtonLoading = false
          })
      })
    },
    exportDeparture() {
        exportDeparture({eventId:this.eventAttendance.id}).then(response => {
            window.location = response.data.downloadLink
        }).catch(error => {
            this.isSubmitModal = false
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })
    },
  },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

#add-new-sidebar {
  .vs__dropdown-menu {
    max-height: 200px !important;
  }
}
</style>
