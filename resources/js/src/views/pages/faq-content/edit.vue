<template>
  <b-card>
    <p class="pt-1">
      <!-- Form -->
      <b-form @submit.prevent="onSubmit">
        <!-- BODY -->
        <validation-observer ref="refObsForm">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <!-- Category -->
            <b-col cols="12" md="4">
              <validation-provider #default="{ errors }" name="Select Category" vid="faq_category_id" rules="required">
                <b-form-group label="Select Category" :state="errors.length > 0 ? false : null">
                  <v-select v-model="formData.faq_category_id" :options="categoryOptions" :clearable="false"
                    :filterable="false" @search="onSearchCategory" :reduce="label => label.id" label="name" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

            <!-- Title -->
            <b-col cols="12" md="4">
              <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                <b-form-group label="Title">
                  <b-form-input id="title" v-model="formData.title" :state="errors.length > 0 ? false : null" trim />

                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>
          <b-row>
            <!-- Content -->
            <b-col cols="12" md="8">
              <validation-provider #default="{ errors }" name="Content" vid="content">
                <b-form-group label="Content">
                  <ckeditor :editor="editor" v-model="formData.content" :config="editorConfig"></ckeditor>
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>
          <b-row>
            <!-- Status -->
            <b-col cols="12" md="4">
              <validation-provider #default="{ errors }" name="Status" vid="status" rules="required">
                <b-form-group label="Status">
                  <b-form-checkbox v-model="formData.status" name="check-button" switch />

                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>

          <!-- Form Actions -->
          <div class="d-flex mt-2">
            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit"
              v-if="hasPermission('faq-add-or-edit')">
              Save Changes
            </b-button>
            <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button"
              variant="outline-secondary">
              Back
            </b-button>
          </div>

        </validation-observer>
      </b-form>
    </p>

  </b-card>
</template>

<script>
import { BCard, BLink, BMedia, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea } from 'bootstrap-vue'
import vSelect from 'vue-select'
import { getDetail, postData } from '@/network/faq-content'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import { avatarText } from '@core/utils/filter'
import Ripple from 'vue-ripple-directive'
import { hasPermission } from '@/auth/utils'
import { getCategoriesSearch } from '@/network/faq-category'
import _ from 'lodash'
import Cleave from 'vue-cleave-component'
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from "ckeditor5-build-classic-image";

export default {
  components: {
    BCard,
    BLink,
    BMedia,
    BFormInvalidFeedback,
    BButton,
    BAvatar,
    BAlert,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormFile,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
    BFormTextarea,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
    vSelect,
    Cleave,
    ckeditor: CKEditor.component,
  },
  directives: {
    Ripple,
  },
  setup() {
    return { hasPermission, avatarText }
  },
  methods: {
    inputImageRenderer() {
      this.formData.image = this.$refs.refInputEl.files[0]
      const file = this.$refs.refInputEl.files[0]
      const reader = new FileReader()

      reader.addEventListener(
        'load',
        () => {
          this.formData.photo = reader.result
        },
        false,
      )

      if (file) {
        reader.readAsDataURL(file)
      }
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          if (key == 'photo') continue
          if (this.formData[key] != null) vForm.append(key, this.formData[key])
        }
        postData(vForm).then(response => {
          this.$bvToast.toast('Content has been changed successfully', {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$router.push({ name: 'faq-content' })
        })
          .catch(error => {
            if (error.response.data.errors) {
              this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
              this.$refs.refObsForm.setErrors(error.response.data)
            }
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
  data() {
    const categoryOptions = []
    getCategoriesSearch({ q: '' })
      .then(res => {
        this.categoryOptions = res.data
      })
      .catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })

    const unitOptions = []

    const formData = {}
    const id = parseInt(this.$route.params.id) || 0
    if (id == 0) this.$router.back()
    getDetail(id).then(response => {
      this.formData = response.data
      this.formData['status'] = (response.data['status'] == 1) ? true : false
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    var fonSizes = [
      9, 10, 11, 12, 13,
      'default',
      17, 18, 19, 20, 24, 26, 28, 30, 36, 40, 50, 60, 70, 80
    ]

    return {
      formData, required, numeric, categoryOptions, unitOptions,
      optionClave: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
      editor: ClassicEditor,
      editorConfig: {
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
    }
  }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
