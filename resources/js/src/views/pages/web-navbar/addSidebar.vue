<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetForm"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Navbar
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetForm">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <!-- Parent Navbar -->
          <validation-provider #default="{ errors }" name="Select Parent Navbar" vid="parent_id">
            <b-form-group label="Select Parent Navbar" description="Leave this option for parent navbar" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.parent_id" :options="parentOptions" :clearable="false"
              :filterable="false" @search="onSearch" :reduce="label => label.id" label="title" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Navbar Title -->
          <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
            <b-form-group label="Title">
              <b-form-input id="title" v-model="formData.title" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Navbar URL -->
          <validation-provider #default="{ errors }" name="URL" vid="url" rules="required">
            <b-form-group label="URL">
              <b-form-input id="url" v-model="formData.url" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Order -->
          <validation-provider #default="{ errors }" name="Order" vid="order" rules="required|numeric">
            <b-form-group label="Order">
              <b-form-input id="order" v-model="formData.order" :state="errors.length > 0 ? false : null" type="number"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Show in Menu -->
          <validation-provider #default="{ errors }" name="Show in Menu" vid="show" rules="required">
            <b-form-group label="Show in Menu">
              <b-form-checkbox v-model="formData.show" name="check-button" switch />

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
import { postData, getParentNavbarSearch } from '@/network/web-navbar'
import _ from 'lodash'

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
    }
  },
  data() {
    const parentOptions = []
    getParentNavbarSearch()
    .then(res => {
      this.parentOptions = res.data
    })
    .catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })
    return {
      isButtonLoading: false,
      required,
      numeric,
      formData: {show:true},
      parentOptions
    }
  },
  methods: {
    resetForm() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.formData['show'] = true
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
          this.$bvToast.toast(`${this.formData.name} has been added successfully`, {
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
    },
    onSearch(search, loading) {
      if (search.length) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    search: _.debounce((loading, search, vm) => {
      getParentNavbarSearch({ q: search })
      .then(res => {
        vm.parentOptions = res.data
        loading(false)
      })
      .catch(error => {
        vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        loading(false)
      })
    }, 300),
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
