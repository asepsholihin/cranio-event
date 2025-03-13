<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetTestimonialData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Testimonials
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetTestimonialData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
            <b-form-group label="Category" :state="errors.length > 0 ? false : null">
              <v-select id="category" multiple v-model="formData.category" :options="categoryOptions"
                  :clearable="true" :reduce="label => label.value" />

              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Sub Category" vid="subcategory">
            <b-form-group label="Sub Category" :state="errors.length > 0 ? false : null">
              <v-select id="subcategory" multiple v-model="formData.subcategory" :options="subCategoryOptions"
                  :clearable="true" :reduce="label => label.name" label="name" />

              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Customer Name -->
          <validation-provider #default="{ errors }" name="Customer Name" vid="customer_name" rules="required">
            <b-form-group label="Customer Name">
              <b-form-input v-model="formData.customer_name" name="customer_name" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Job or Position -->
          <validation-provider #default="{ errors }" name="Job or Position" vid="customer_job" rules="required">
            <b-form-group label="Job or Position">
              <b-form-input v-model="formData.customer_job" name="customer_job" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Trip -->
          <validation-provider #default="{ errors }" name="Keberangkatan" vid="trip">
            <b-form-group label="Keberangkatan">
              <b-form-input v-model="formData.trip" name="trip" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Package" vid="package">
            <b-form-group label="Package" :state="errors.length > 0 ? false : null">
              <v-select id="package" multiple v-model="formData.package" :options="packageOptions"
                  :clearable="true" :reduce="label => label.name" label="name" />

              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Testimony -->
          <validation-provider #default="{ errors }" name="Testimony" vid="testimony" rules="required">
            <b-form-group label="Testimony">
              <b-form-textarea id="testimony" v-model="formData.testimony" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Customer Photo -->
          <validation-provider #default="{ errors }" vid="image" name="Customer Photo" rules="required">
            <b-form-group label="Customer Photo" description="Recommended Size 300 x 300 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
              <b-form-file accept="image/jpeg, image/png, image/webp" v-model="formData.image"
                :state="errors.length > 0 ? false : null" placeholder="Choose a image or drop it here..."
                drop-placeholder="Drop image here..." />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Status -->
          <validation-provider #default="{ errors }" name="Status" vid="publish_status" rules="required">
            <b-form-group label="Status">
              <b-form-checkbox v-model="formData.publish_status" name="check-button" switch />

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
import { postData } from '@/network/testimonial'
import flatPickr from 'vue-flatpickr-component'
import { getPackageSearch } from '@/network/package'
import { getSubCategorySearch } from "@/network/catalog"

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
  },
  data() {
    const categoryOptions = [
        { label: 'Umroh', value: 'umroh' },
        { label: 'Haji', value: 'haji' },
        { label: 'Ruang Participant', value: 'ruangparticipant' },
        { label: 'Badal Umroh', value: 'badalhaji' },
        { label: 'Tabungan Umroh', value: 'tabunganumroh' },
    ]

    const packageOptions = []
    getPackageSearch()
    .then(response => {
        this.packageOptions = response.data;
    }).catch(error => {
        this.$bvToast.toast(error, {
          title: `Error`,
          variant: 'danger',
          toaster: 'b-toaster-top-center',
          solid: true,
        })
    })

    const subCategoryOptions = []
    getSubCategorySearch()
    .then(response => {
        this.subCategoryOptions = response.data;
    }).catch(error => {
        this.$bvToast.toast(error, {
          title: `Error`,
          variant: 'danger',
          toaster: 'b-toaster-top-center',
          solid: true,
        })
    })

    return {
      categoryOptions,
      isButtonLoading: false,
      required,
      numeric,
      formData: {publish_status:true},
      packageOptions,
      subCategoryOptions
    }
  },
  methods: {
    resetTestimonialData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.formData.publish_status = true
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
            this.$bvToast.toast(error, {
              title: `Error`,
              variant: 'danger',
              toaster: 'b-toaster-top-center',
              solid: true,
            })
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
