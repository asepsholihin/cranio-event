<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetMainVisualData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Image Itinerary
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetMainVisualData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

           <!-- Category Type -->
           <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
            <b-form-group label="Category" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.category_id" :options="categories_product" @input="changeCatgeories" :clearable="false" :reduce="label => label.id" Label="name" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                </b-form-invalid-feedback>
                </b-form-group>
           </validation-provider>

           <!-- Subcategory -->
            <validation-provider #default="{ errors }" name="Subcategory" vid="sub_category" rules="required">
            <b-form-group label="Subcategory" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.subcategory_id" :options="subcategories" :clearable="false" :reduce="label => label.id" Label="name" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                </b-form-invalid-feedback>
            </b-form-group>
            </validation-provider>

          <!-- Package Duration -->
          <validation-provider #default="{ errors }" name="Package Duration" vid="package_duration"
            rules="required|numeric">
            <b-form-group label="Package Duration">
              <b-form-input id="package_duration" v-model="formData.package_duration"
                :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Image or File PDF -->
          <validation-provider #default="{ errors }" vid="image" name="Image" rules="required">
            <b-form-group label="Image" description="Recommended Size 780 x 1080 px (Format : PNG, JPG, WEBP, PDF) Max Size: 1.5MB">
              <b-form-file accept="image/jpeg, image/png, image/webp, application/pdf" v-model="formData.image"
                :state="errors.length > 0 ? false : null" placeholder="Choose a image or drop it here..."
                drop-placeholder="Drop image here..." />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Alt Image -->
          <validation-provider #default="{ errors }" name="Alt Image" vid="alt_image" rules="required">
            <b-form-group label="Alt Image">
              <b-form-input id="alt_image" v-model="formData.alt_image" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Title Image -->
          <validation-provider #default="{ errors }" name="Title Image" vid="title_image" rules="required">
            <b-form-group label="Title Image">
              <b-form-input id="title_image" v-model="formData.title_image" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Notes" vid="notes">
            <b-form-group label="Notes">
              <b-form-textarea id="notes" v-model="formData.notes" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Form Actions -->
          <div class="d-flex mt-2">
            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit"
              :disabled="isButtonLoading">
              <b-spinner small v-show="isButtonLoading" /> Add
            </b-button>
            <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary" @click="hide">
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
import { getListCategories, getListSubcategory } from '@/network/catalog'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
import { postData } from '@/network/web-itinerary'
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
    const categories_product = []
    getListCategories().then(res => {
        this.categories_product = res.data
    })
    .catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })
    return {
      isButtonLoading: false,
      categories_product,
      required,
      numeric,
      subcategories: [],
      formData: { status: true }
    }
  },
  methods: {
    changeCatgeories(){
        const category_id = this.formData.category_id
        this.formData.subcategory_id = ''
        getListSubcategory(category_id).then(response => {
            this.subcategories = response.data
        }).catch(error => {
            this.$bvToast.toast(error, {
            title: `Error`,
            variant: 'danger',
            toaster: 'b-toaster-top-center',
            solid: true,
            })
        })
    },
    resetMainVisualData() {
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
          this.$bvToast.toast(`Image Itinerary has been added successfully`, {
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
