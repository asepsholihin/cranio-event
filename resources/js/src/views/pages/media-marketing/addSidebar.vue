<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetForm"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Media Marketing
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

          <!-- Title -->
          <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
            <b-form-group label="Title">
              <b-form-input v-model="formData.title" name="title"
                :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Type -->
          <validation-provider #default="{ errors }" name="Type" vid="type" rules="required">
            <b-form-group label="Type">
              <v-select v-model="formData.type" :options="typeOptions"
                :clearable="false" :reduce="label => label.value"/>

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Category -->
          <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
            <b-form-group label="Category">
              <v-select v-model="formData.category" :options="categoryOptions"
                :clearable="false" :reduce="label => label.value"/>

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Image -->
          <div v-if="formData.type == 'banner'">
            <validation-provider #default="{ errors }" vid="image" name="Image" rules="required">
              <b-form-group description="Format : PNG, JPG">
                <label for="image">Image <feather-icon icon="AlertCircleIcon" id="tooltip-button-variant"/></label>
                <b-tooltip target="tooltip-button-variant" variant="info">Square photo 1080 x 1080px<br>Landscape photo 1080 x 566px<br>Portrait photo 1080 x 1350px</b-tooltip>
                <b-form-file accept="image/jpeg, image/png, image/webp" v-model="formData.file_image"
                  :state="errors.length > 0 ? false : null" placeholder="Choose a image or drop it here..." 
                  drop-placeholder="Drop image here..." id="image" />
                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div v-if="formData.type == 'flyer'">
            <validation-provider #default="{ errors }" vid="images" name="Images">
              <b-form-group label="Images">
                <b-button variant="primary" size="sm" @click="$refs.refInputEl.click()" class="mt-25">
                  <input accept="image/jpeg, image/png, image/webp" ref="refInputEl" type="file" class="d-none" multiple @change="onFileChange">
                  <span>Add Image</span>
                </b-button>
              </b-form-group>
            </validation-provider>

            <b-row class="mb-3" v-if="images.length > 0">
              <b-col sm="4" v-for="(item, key) in images" :key="key" class="position-relative mb-1">
                <img v-if="item.image" :src="item.image" class="img-fluid rounded" />
                <b-button variant="danger" size="sm" class="btn-delete" @click="deleteImage(key, item)">
                  <feather-icon icon="Trash2Icon" />
                </b-button>
              </b-col>
            </b-row>
          </div>

          <!-- Status -->
          <validation-provider #default="{ errors }" name="Status" vid="status" rules="required">
            <b-form-group label="Status">
              <b-form-checkbox v-model="formData.status" name="check-button" switch />

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
import { BRow, BCol, BSidebar, BForm, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner, BFormCheckbox, BTooltip } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/media-marketing'

export default {
  components: {
    BRow,
    BCol,
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
    BTooltip,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
    vSelect,
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
    const typeOptions = [
        { label: 'Banner', value: 'banner' },
        { label: 'Flyer', value: 'flyer' },
    ]
    const categoryOptions = [
        { label: 'Mitra', value: 'mitra' },
        { label: 'Campaign', value: 'campaign' },
    ]
    return {
      isButtonLoading: false,
      required,
      numeric,
      formData: { status: true },
      typeOptions,
      categoryOptions,
      images: [],
      imageFiles: []
    }
  },
  methods: {
    resetForm() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
    },
    onFileChange(e) {
      let selectedFiles = this.$refs.refInputEl.files;
      if (!selectedFiles.length) {
          return;
      }

      for (var i = 0; i < selectedFiles.length; i++) {
        const file = this.$refs.refInputEl.files[i]
        this.imageFiles.push(file)
        this.images.push({ stored: false, image: URL.createObjectURL(file)})
      }
      this.$refs.refInputEl.value = null;
    },
    deleteImage(index, item) {
      this.$refs.refInputEl.value = null;
      this.imageFiles.splice(index, 1)
      this.images.splice(index, 1)
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        for (const row of this.imageFiles) {
          vForm.append("file_image[]", row);
        }
        this.isButtonLoading = true
        postData(vForm).then(response => {
          this.$bvToast.toast(`Media Marketing has been added successfully`, {
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
.btn-delete {
    position: absolute;
    right: 0;
    top: 8px;
}
#add-new-sidebar {
  .vs__dropdown-menu {
    max-height: 200px !important;
  }
}
</style>
