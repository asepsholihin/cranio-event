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

                    <b-media class="mb-2" v-if="formData.type == 'banner'">
                        <template #aside>
                            <b-avatar :src="formData.image" :text="avatarText('NA')" size="90px" rounded />
                        </template>
                        <div class="d-flex flex-wrap">
                            <b-button v-if="hasPermission('media-marketing-add-or-edit')" variant="primary" @click="$refs.refInputEl.click()">
                                <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                                <span class="d-none d-sm-inline">Change</span>
                                <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                            </b-button>
                        </div>
                        <div class="mt-1 text-muted">Recommended Size 300 x 300 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                    </b-media>

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
                            <b-col sm="3" v-for="(item, key) in images" :key="key" class="position-relative mb-1">
                                <img v-if="item.image" :src="item.image" class="img-fluid rounded" />
                                <b-form-input class="mt-1" v-model="item.order" trim />
                                <b-button variant="danger" size="sm" class="btn-delete" @click="deleteImage(key, item)">
                                    <feather-icon icon="Trash2Icon" />
                                </b-button>
                            </b-col>
                        </b-row>
                    </div>
                    
                    <b-row>
                        <!-- Title -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                              <b-form-group label="Title" :state="errors.length > 0 ? false : null">
                                <b-form-input v-model="formData.title" name="title" :state="errors.length > 0 ? false : null" trim />

                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Type -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Type" vid="type" rules="required">
                                <b-form-group label="Type">
                                <v-select v-model="formData.type" :options="typeOptions"
                                    :clearable="false" :reduce="label => label.value"/>

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Category -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Category" vid="category" rules="required">
                                <b-form-group label="Category">
                                <v-select v-model="formData.category" :options="categoryOptions"
                                    :clearable="false" :reduce="label => label.value"/>

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
                            type="submit" v-if="hasPermission('web-settings-add-or-edit')">
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
import { getDetail } from '@/network/media-marketing'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData, postDeleteImage } from '@/network/media-marketing'
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
        const typeOptions = [
            { label: 'Banner', value: 'banner' },
            { label: 'Flyer', value: 'flyer' },
        ]
        const categoryOptions = [
            { label: 'Mitra', value: 'mitra' },
            { label: 'Campaign', value: 'campaign' },
        ]
        
        return {
            hasPermission, avatarText, categoryOptions, typeOptions
        }
    },
    methods: {
        inputImageRenderer() {
          this.formData.file_image = this.$refs.refInputEl.files[0]
          const file = this.$refs.refInputEl.files[0]
          const reader = new FileReader()

          reader.addEventListener(
            'load',
            () => {
              this.formData.image = reader.result
            },
            false,
          )

          if (file) {
            reader.readAsDataURL(file)
          }
        },
        onFileChange(e) {
            let selectedFiles = this.$refs.refInputEl.files;
            if (!selectedFiles.length) {
                return;
            }

            for (var i = 0; i < selectedFiles.length; i++) {
                const file = this.$refs.refInputEl.files[i]
                this.imageFiles.push(file)
                this.images.push({ stored: false, order:i, image: URL.createObjectURL(file)})
            }
            this.$refs.refInputEl.value = null;
        },
        deleteImage(index, item) {
            if(item.stored) {
                this.$swal({
                    title: `Delete image?`,
                    text: "It cannot be reverted",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-primary ml-1',
                    },
                    buttonsStyling: false,
                }).then(result => {
                    if (result.value) {
                        const vForm = new FormData()
                        vForm.append('id', item.id)
                        postDeleteImage(vForm).then(response => {
                            this.$swal({ icon: 'success', title: 'Success', text: `Delivery evidence has been deleted successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                            this.imageFiles.splice(index, 1)
                            this.images.splice(index, 1)
                        }).catch(error => {
                            this.$swal({ icon: 'error', title: 'Error', text: `${error}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        })
                    }
                });
            } else {
                this.imageFiles.splice(index, 1)
                this.images.splice(index, 1)
            }
            this.$refs.refInputEl.value = null;
        },
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (var key in this.formData) {
                    if (key == 'images') continue
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                for (const row of this.imageFiles) {
                    vForm.append("file_image[]", row)
                }
                vForm.append("images_order", JSON.stringify(this.images))
                postData(vForm).then(response => {
                    this.$bvToast.toast('Media Marketing has been changed successfully', {
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
            for (const image of this.formData.images) {
                this.images.push({ stored: true, id:image.id, order:image.order, image: image.image_url})
            }
        }).catch(error => {
            this.$bvToast.toast(error, {
                title: `Error`,
                variant: 'danger',
                toaster: 'b-toaster-top-center',
                solid: true,
            })
        })

        return {
            formData, required, numeric,
            images: [],
            imageFiles: []
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
.btn-delete {
    position: absolute;
    right: 0;
    top: 8px;
}
</style>
