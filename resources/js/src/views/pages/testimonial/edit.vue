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

                    <b-media class="mb-2">
                        <template #aside>
                        <b-avatar :src="formData.customer_photo" :text="avatarText('NA')" size="90px" rounded />
                        </template>
                        <div class="d-flex flex-wrap">
                        <b-button v-if="hasPermission('testimonial-add-or-edit')" variant="primary" @click="$refs.refInputEl.click()">
                            <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                            <span class="d-none d-sm-inline">Change</span>
                            <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                        </b-button>
                        </div>
                        <div class="mt-1 text-muted">Recommended Size 300 x 300 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                    </b-media>

                    <b-row>
                        <!-- Category -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
                              <b-form-group label="Category" :state="errors.length > 0 ? false : null">
                                <v-select id="category" multiple v-model="formData.category" :options="categoryOptions"
                                    :clearable="true" :reduce="label => label.value" />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- SubCategory -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Sub Category" vid="subcategory">
                              <b-form-group label="Sub Category" :state="errors.length > 0 ? false : null">
                                <v-select id="subcategory" multiple v-model="formData.subcategory" :options="subCategoryOptions"
                                    :clearable="true" :reduce="label => label.name" label="name" />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Trip -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Keberangkatan" vid="trip">
                              <b-form-group label="Keberangkatan" :state="errors.length > 0 ? false : null">
                                <b-form-input v-model="formData.trip" name="trip" :state="errors.length > 0 ? false : null" trim />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- Package -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Package" vid="package">
                              <b-form-group label="Package" :state="errors.length > 0 ? false : null">
                                <v-select id="package" multiple v-model="formData.package" :options="packageOptions"
                                    :clearable="true" :reduce="label => label.name" label="name" />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Customer Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Customer Name" vid="customer_name" rules="required">
                              <b-form-group label="Customer Name" :state="errors.length > 0 ? false : null">
                                <b-form-input v-model="formData.customer_name" name="customer_name" :state="errors.length > 0 ? false : null" trim />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- Job or Position -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Job or Position" vid="customer_job" rules="required">
                              <b-form-group label="Job or Position" :state="errors.length > 0 ? false : null">
                                <b-form-input v-model="formData.customer_job" name="customer_job" :state="errors.length > 0 ? false : null" trim />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Testimony -->
                        <b-col cols="12" md="8">
                            <validation-provider #default="{ errors }" name="Testimony" vid="testimony" rules="required">
                              <b-form-group label="Testimony">
                                <b-form-textarea id="testimony" rows="15" v-model="formData.testimony" :state="errors.length > 0 ? false : null"
                                trim />

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
                            <validation-provider #default="{ errors }" name="Publish Status" vid="publish_status" rules="required">
                              <b-form-group label="Publish Status">
                                <b-form-checkbox v-model="formData.publish_status" name="check-button" switch />

                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>

                    </b-row>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            type="submit" v-if="hasPermission('testimonial-add-or-edit')" :disabled="isButtonLoading">
                            <b-spinner v-show="isButtonLoading" small></b-spinner> Save Changes
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia, BSpinner } from 'bootstrap-vue'
import { getDetail } from '@/network/testimonial'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData } from '@/network/testimonial'
import { hasPermission } from '@/auth/utils'
import { getPackageSearch } from '@/network/package'
import { getSubCategorySearch } from "@/network/catalog"

export default {
    components: {
        BCard,
        BLink,
        BFormInvalidFeedback,
        BButton,
        vSelect,
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
        BMedia,
        BSpinner,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        const categoryOptions = [
            { label: 'Umroh', value: 'umroh' },
            { label: 'Haji', value: 'haji' },
            { label: 'Ruang Participant', value: 'ruangparticipant' },
            { label: 'Badal Umroh', value: 'badalhaji' },
            { label: 'Tabungan Umroh', value: 'tabunganumroh' },
        ]

        return {hasPermission, avatarText, categoryOptions}
    },
    methods: {
        inputImageRenderer() {
          this.formData.image = this.$refs.refInputEl.files[0]
          const file = this.$refs.refInputEl.files[0]
          const reader = new FileReader()

          reader.addEventListener(
            'load',
            () => {
              this.formData.customer_photo = reader.result
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
                    if (key == 'customer_photo')
                        continue
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                this.isButtonLoading = true
                postData(vForm).then(response => {
                    this.$bvToast.toast('Testimonial has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                    this.isButtonLoading = false
                })
                .catch(error => {
                    this.isButtonLoading = false
                    this.$bvToast.toast(error, {
                        title: `Error`,
                        variant: 'danger',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                })
            })
        }
    },
    data() {
        const formData = {}
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['publish_status'] = (response.data['publish_status'] == 1) ? true : false
            if(response.data['category']) this.formData['category'] = response.data['category'].split(',')
            if(response.data['subcategory']) this.formData['subcategory'] = response.data['subcategory'].split(',')
            if(response.data['package']) this.formData['package'] = response.data['package'].split(',')
        }).catch(error => {
            this.$bvToast.toast(error, {
              title: `Error`,
              variant: 'danger',
              toaster: 'b-toaster-top-center',
              solid: true,
            })
        })

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
            formData, required, numeric, packageOptions, subCategoryOptions, isButtonLoading: false
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
