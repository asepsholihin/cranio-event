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
                        <b-avatar :src="formData.image_url" :text="avatarText('NA')" size="90px" rounded />
                        </template>
                        <div class="d-flex flex-wrap">
                        <b-button v-if="hasPermission('images-slider-add-or-edit')" variant="primary" @click="$refs.refInputEl.click()">
                            <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                            <span class="d-none d-sm-inline">Change</span>
                            <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                        </b-button>
                        </div>
                        <div class="mt-1 text-muted">Recommended Size 1076 x 529 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                    </b-media>
                    
                    <b-row>
                        <!-- Alt Image -->
                        <b-col cols="12">
                            <validation-provider #default="{ errors }" name="Alt Image" vid="alt_image" rules="required">
                                <b-form-group label="Alt Image">
                                <b-form-input id="alt_image" v-model="formData.alt_image" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- Title Image -->
                        <b-col cols="12">
                            <validation-provider #default="{ errors }" name="Title Image" vid="title_image" rules="required">
                                <b-form-group label="Title Image">
                                <b-form-input id="title_image" v-model="formData.title_image" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- Order -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Order" vid="order" rules="required|numeric">
                                <b-form-group label="Order">
                                <b-form-input id="order" v-model="formData.order" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- URL -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="URL" vid="url" rules="required">
                                <b-form-group label="URL">
                                <b-form-input id="url" v-model="formData.url" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Title -->
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                                <b-form-group label="Title">
                                <b-form-input id="title" v-model="formData.title" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Description -->
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Description" vid="description" rules="required">
                                <b-form-group label="Description">
                                <b-form-textarea id="description" v-model="formData.description" :state="errors.length > 0 ? false : null"
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
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            type="submit" v-if="hasPermission('images-slider-add-or-edit')">
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia } from 'bootstrap-vue'
import { getDetail } from '@/network/web-program'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData } from '@/network/web-program'
import { hasPermission } from '@/auth/utils'

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

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        return {hasPermission, avatarText}
    },
    methods: {
        inputImageRenderer() {
          this.formData.image = this.$refs.refInputEl.files[0]
          const file = this.$refs.refInputEl.files[0]
          const reader = new FileReader()

          reader.addEventListener(
            'load',
            () => {
              this.formData.image_url = reader.result
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
                    if (key == 'image_url')
                        continue
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                postData(vForm).then(response => {
                    this.$bvToast.toast('Program has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                })
                    .catch(error => {
                        if (error.response.data.errors) {
                            this.$refs.refObsForm.setErrors(error.response.data.errors)
                        } else {
                            this.$refs.refObsForm.setErrors(error.response.data)
                        }
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
            this.formData['status'] = (response.data['status'] == 1) ? true : false
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
