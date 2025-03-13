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
                        <b-col cols="12">
                            <!-- Image OR FILE PDF-->
                            <validation-provider #default="{ errors }" vid="image" name="Image or File PDF" rules="required">
                                <b-form-group label="Image Or File PDF" description="Recommended Size 780 x 1080 px (Format : PNG, JPG, WEBP, PDF) Max Size: 1.5MB">
                                <b-form-file accept="image/jpeg, image/png, image/webp, application/pdf" v-model="formData.image"
                                    :state="errors.length > 0 ? false : null" placeholder="Choose a image or drop it here..."
                                    drop-placeholder="Drop image here..." />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12">
                            <!-- Category Type -->
                            <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
                                <b-form-group label="Category" :state="errors.length > 0 ? false : null">
                                    <v-select v-model="formData.category_id" :options="categories_product" @input="changeCatgeories" :clearable="false" :reduce="label => label.id" Label="name" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12">
                        <!-- Subcategory -->
                        <validation-provider #default="{ errors }" name="Subcategory" vid="sub_category" rules="required">
                            <b-form-group label="Subcategory" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.subcategory_id" :options="subcategories" :clearable="false" :reduce="label => label.id" Label="name" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                            </validation-provider>
                        </b-col>
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
                        <!-- Package Duration -->
                        <b-col cols="12" md="4">
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
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Notes -->
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Notes" vid="notes">
                                <b-form-group label="Notes">
                                    <b-form-textarea id="notes" v-model="formData.notes"
                                        :state="errors.length > 0 ? false : null" trim />

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
                            v-if="hasPermission('images-slider-add-or-edit')">
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
import { getDetail } from '@/network/web-itinerary'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData } from '@/network/web-itinerary'
import { getListCategories, getListSubcategory } from '@/network/catalog'
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
        return { hasPermission, avatarText }
    },
    methods: {
        inputImageRenderer() {
            this.formData.image = this.$refs.refInputEl.files[0]
            const file = this.$refs.refInputEl.files[0]
            const reader = new FileReader()
            console.log(this.formData.image_url)
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
                    if(key == 'category_name')
                        continue
                    if(key == 'subcategory_name')
                        continue

                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                postData(vForm).then(response => {
                    this.$bvToast.toast('Image Itinerary has been changed successfully', {
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
        },
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
    },
    data() {
        const formData = {}
        const subcategories = []
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            if(this.formData.category_id!=null && this.formData.category_id!='null'){
                getListSubcategory(this.formData.category_id).then(response => {
                    this.subcategories = response.data
                }).catch(error => {
                    this.$bvToast.toast(error, {
                    title: `Error`,
                    variant: 'danger',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                    })
                })
            }
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })
        const categories_product = []
        getListCategories().then(res => {
            this.categories_product = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        return {
            formData, required, numeric, categories_product, subcategories
        }
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';</style>
