<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetFormData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Vendor
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetFormData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <!-- Name -->
          <validation-provider #default="{ errors }" name="Name" vid="name" rules="required">
            <b-form-group label="Name">
              <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Alamat -->
          <validation-provider #default="{ errors }" name="Alamat" vid="address">
            <b-form-group label="Address">
              <b-form-textarea id="address" v-model="formData.address" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- PIC Name -->
          <validation-provider #default="{ errors }" name="PIC Name" vid="address">
            <b-form-group label="PIC Name">
              <b-form-input id="pic" v-model="formData.pic" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Phone -->
          <validation-provider #default="{ errors }" name="Phone" vid="phone" rules="numeric">
            <b-form-group label="Phone">
              <b-form-input id="phone" v-model="formData.phone" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Email -->
          <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
            <b-form-group label="Email">
              <b-form-input id="email" v-model="formData.email" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Speciality -->
          <validation-provider #default="{ errors }" name="Speciality" vid="speciality">
            <b-form-group label="Speciality">
              <b-form-input id="speciality" v-model="formData.speciality" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Equipments -->
          <validation-provider #default="{ errors }" name="Equipments" vid="equipments">
            <b-form-group label="Equipments">
              <v-select id="equipments" multiple v-model="formData.equipment_ids" :options="equipmentList"
                :clearable="true" :reduce="label => label.id" label="name" />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Description -->
          <validation-provider #default="{ errors }" name="Description" vid="description">
            <b-form-group label="Description">
              <b-form-textarea id="description" v-model="formData.description" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Batas Waktu Retur -->
          <validation-provider #default="{ errors }" name="Batas Waktu Retur (Hari)" vid="guarantee_time" rules="numeric">
            <b-form-group label="Batas Waktu Retur (Hari)">
              <b-form-input id="guarantee_time" v-model="formData.guarantee_time" :state="errors.length > 0 ? false : null"
              trim />

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
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/vendor'
import { getEquipmentSearch } from '@/network/equipment'

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
  mounted() {
    this.getEquipments()
  },
  data() {
    return {
      isButtonLoading: false,
      required, numeric, email,
      equipmentList: [],
      formData: {}
    }
  },
  methods: {
    getEquipments() {
      getEquipmentSearch().then(response => {
          this.equipmentList = response.data
      }).catch(error => {
        this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })
    },
    resetFormData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.formData['status'] = true
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

#add-new-sidebar {
  .vs__dropdown-menu {
    max-height: 200px !important;
  }
}
</style>
