<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetSaleData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Sales
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetSaleData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Sales Name" vid="user_id" rules="required">
              <b-form-group :state="errors.length > 0 ? false : null">
                  <label>
                      Sales Name
                  </label>
                  <v-select v-model="formData.user_id" :options="userOptions" :clearable="false"
                      :reduce="user => user.id" label="name" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                      {{ errors[0] }}
                  </b-form-invalid-feedback>
              </b-form-group>
          </validation-provider>

          <!-- Whatsapp Number -->
          <validation-provider #default="{ errors }" name="Whatsapp Number" vid="whatsapp_number" rules="required|numeric">
            <b-form-group label="Whatsapp Number">
              <b-form-input id="whatsapp_number" v-model="formData.whatsapp_number" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Order Number -->
          <validation-provider #default="{ errors }" name="Order Number" vid="order_number" rules="required|numeric">
            <b-form-group label="Order Number">
              <b-form-input id="order_number" v-model="formData.order_number" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Whatsapp API -->
          <validation-provider #default="{ errors }" name="Whatsapp API" vid="whatsapp_api">
            <b-form-group label="Whatsapp API">
              <b-form-textarea id="whatsapp_api" v-model="formData.whatsapp_api" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Status -->
          <validation-provider #default="{ errors }" name="Status" vid="status">
            <b-form-group label="Status">
              <b-form-checkbox v-model="formData.status" name="check-button" switch />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>


          <validation-provider #default="{ errors }" name="Show in Footer" vid="show_in_footer">
            <b-form-group label="Show in Footer">
              <b-form-checkbox v-model="formData.show_in_footer" name="check-button" switch />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>


          <validation-provider #default="{ errors }" name="Status" vid="whatsapp_service">
            <b-form-group label="Whatsapp Service">
              <b-form-checkbox v-model="formData.whatsapp_service" name="check-button" switch />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Form Actions -->
          <div class="d-flex mt-2">
            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit" :disabled="isButtonLoading">
              <b-spinner small v-show="isButtonLoading" /> Add
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
import { BSidebar, BForm, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner, BFormCheckbox } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/web-sales'
import { getUserSearch } from '@/network/user-platform'

export default {
  components: {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BFormCheckbox,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    vSelect,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isAddSidebarActive',
    event: 'update:is-add-sidebar-active',
  },
  props: {
    isAddSidebarActive: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    const userOptions = []
    getUserSearch({}).then(response => {
        this.userOptions = response.data;
    }).catch(error => {
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, timer: 3500, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
    })
    return {
      userOptions,
      isButtonLoading: false,
      required,
      numeric,
      formData: {is_reminder:true,status:true}
    }
  },
  methods: {
    resetSaleData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        this.isButtonLoading = true
        postData(vForm).then(response => {
          this.$bvToast.toast(`${this.formData.title} has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$emit('refetch-data')
          this.$emit('update:is-add-sidebar-active', false)
          this.isButtonLoading = false
        })
        .catch(error => {
            if (error.response.data.errors) {
              this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
              this.$refs.refObsForm.setErrors(error.response.data)
            }
            this.isButtonLoading = false
          })
      })
    }
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
