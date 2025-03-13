<template>
  <b-card>
    <p>
      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetItemData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <!-- Category -->
          <validation-provider #default="{ errors }" name="Select Category" vid="faq_category_id" rules="required">
            <b-form-group label="Select Category" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.faq_category_id" :options="categoryOptions" :clearable="false"
                :filterable="false" @search="onSearchCategory" :reduce="label => label.id" label="name" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Title -->
          <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
            <b-form-group label="Title">
              <b-form-input id="title" v-model="formData.title" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Content -->
          <validation-provider #default="{ errors }" name="Content" vid="content">
            <b-form-group label="Content">
              <ckeditor :editor="editor" v-model="formData.content" :config="editorConfig"></ckeditor>
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

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
            <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary"
              @click="hide">
              Cancel
            </b-button>
          </div>

        </b-form>
      </validation-observer>
    </p>
  </b-card>
</template>

<script>
import {
  BCard, BForm, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner, BFormCheckbox
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/faq-content'
import { getCategoriesSearch } from '@/network/faq-category'
import _ from 'lodash'
import Cleave from 'vue-cleave-component'
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from "ckeditor5-build-classic-image";

export default {
  components: {
    BCard,
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
    Cleave,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
    ckeditor: CKEditor.component
  },
  directives: {
    Ripple,
  },
  data() {
    const categoryOptions = [];
    getCategoriesSearch()
      .then(res => {
        this.categoryOptions = res.data

      })
      .catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })

      })

    var fonSizes = [
      9, 10, 11, 12, 13,
      'default',
      17, 18, 19, 20, 24, 26, 28, 30, 36, 40, 50, 60, 70, 80
    ]

    return {
      isButtonLoading: false,
      required,
      numeric,
      formData: { status: true },
      categoryOptions,
      unitOptions: [],
      editor: ClassicEditor,
      editorConfig: {
        contentsCss: require('@/assets/images/logo/logo.png'),
        fontFamily: {
          options: [
            'default',
            'Arial',
            'Courier New',
            'Georgia',
            'Lucida Sans Unicode',
            'Tahoma',
            'Times New Roman',
            'Trebuchet MS',
            'Verdana',
            'Gotham',
            'Montserrat, Helvetica, Arial, serif',
            'Naskh Regular',
          ],
          supportAllValues: true
        },
        fontSize: {
          options: fonSizes,
          supportAllValues: true
        },
        heading: {
          options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
          ]
        },
        toolbar: {
          items: [
            'heading',
            '|',
            "fontFamily",
            "fontSize",
            "lineHeight",
            "fontColor",
            "bold",
            "italic",
            "underline",
            "alignment",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "indent",
            "outdent",
            "|",
            "link",
            "imageUpload",
            "insertTable", "tableColumn", "tableRow", "mergeTableCells",
            "mediaEmbed",
            "blockQuote",
            "|",
            "removeFormat"
          ]
        }
      },
      optionClave: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
    }
  },
  methods: {
    resetItemData() {
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
          this.$router.push({ name: 'faq-content' })
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
    onSearchCategory(searchCategory, loading) {
      if (searchCategory.length) {
        loading(true);
        this.searchCategory(loading, searchCategory, this);
      }
    },
    searchCategory: _.debounce((loading, searchCategory, vm) => {
      getCategoriesSearch({ q: searchCategory })
        .then(res => {
          vm.categoryOptions = res.data
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
