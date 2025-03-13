<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData" @shown="resetUserData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">Add User Platform</h5>
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

          <!-- Name -->
          <validation-provider #default="{ errors }" name="Full Name" vid="name" rules="required">
            <b-form-group label="Full Name">
              <b-form-input v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Email -->
          <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
            <b-form-group label="Email" label-for="email">
              <b-form-input name="user-platform-password" v-model="formData.email" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Password -->
          <validation-provider #default="{ errors }" name="Password" vid="password" rules="required|min:7">
            <b-form-group label="Password">
                <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                  <b-form-input  v-model="formData.password" :state="errors.length > 0 ? false : null"
                    :type="passwordFieldTypeNew" autocomplete="nope"/>
                  <b-input-group-append is-text>
                    <feather-icon :icon="passwordToggleIconNew" class="cursor-pointer" @click="togglePasswordNew" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
            </b-form-group>
          </validation-provider>
          
          <!-- Department -->
          <validation-provider #default="{ errors }" name="Department" vid="departments" rules="required">
            <b-form-group label="Department" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.departments" :options="departmentOptions" :clearable="false"
                :reduce="label => label.id" label="name" multiple />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

            <!-- Access Status -->
          <validation-provider #default="{ errors }" name="Access Status" vid="access_status" rules="required">
            <b-form-group label="Access Status" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.access_status" :options="[{ label: 'Active', value: 1 }, { label: 'Disabled', value: 2 }]" :clearable="false" :reduce="label => label.value" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                </b-form-invalid-feedback>
                </b-form-group>
           </validation-provider>

           <validation-provider #default="{ errors }" name="Role" vid="role_id" rules="required">
                <b-form-group label="Role" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.role_id" :options="roleOptions" :clearable="false"
                    :reduce="label => label.value" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                </b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>

            <b-form-group label="Permissions">
                <b-form-checkbox-group v-model="formData.permission_checked" :options="permissionList" switches stacked />
            </b-form-group>

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
import { BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/user-platform'
import flatPickr from 'vue-flatpickr-component'
import { getOfficeSearch } from '@/network/master-office'

export default {
  components: {
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
    permissionList: {
      required: true
    },
    departmentOptions: {
      type: Array,
      required: true,
    },
  },
  data() {
    const officeOptions =[]
    getOfficeSearch({ q: '' }).then(res => {
        this.officeOptions = res.data
    })
    .catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })
    return {
      password: '',
      passwordFieldTypeNew: 'password',
      isButtonLoading: false,
      required,
      min,
      numeric,
      email,
      formData: {},
      officeOptions
    }
  },
  setup() {
    const roleOptions = [
      { label: 'Admin', value: 1 },
      { label: 'Staff', value: 2 },
      { label: 'Manager', value: 3 },
      { label: 'Directors', value: 4 }
    ]
    return {
      roleOptions
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
      this.formData = {}
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
