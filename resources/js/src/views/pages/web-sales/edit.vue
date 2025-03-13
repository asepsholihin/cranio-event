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
                        <!-- Sales Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Sales Name" vid="user_id" rules="required">
                                <b-form-group :state="errors.length > 0 ? false : null">
                                    <label>
                                        Sales Name
                                    </label>
                                    <v-select v-model="formData.user_id" :options="userOptions" :clearable="false"
                                        :reduce="user => user.id" label="name" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Whatsapp Number -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Whatsapp Number" vid="whatsapp_number" rules="required|numeric">
                                <b-form-group label="Whatsapp Number">
                                <b-form-input id="whatsapp_number" v-model="formData.whatsapp_number" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Order Number -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Order Number" vid="order_number" rules="required|numeric">
                                <b-form-group label="Order Number">
                                <b-form-input id="order_number" v-model="formData.order_number" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Whatsapp API -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Whatsapp API" vid="whatsapp_api">
                                <b-form-group label="Whatsapp API">
                                <b-form-textarea id="whatsapp_api" v-model="formData.whatsapp_api" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Status -->
                        <b-col cols="12" md="2">
                            <validation-provider #default="{ errors }" name="Status" vid="status">
                              <b-form-group label="Status">
                                <b-form-checkbox v-model="formData.status" name="check-button" switch />

                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>


                        <!-- Show in Footer -->
                        <b-col cols="12" md="2">
                            <validation-provider #default="{ errors }" name="Show in Footer" vid="show_in_footer">
                              <b-form-group label="Show in Footer">
                                <b-form-checkbox v-model="formData.show_in_footer" name="check-button" switch />

                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>


                        <!-- Whatsapp Service -->
                        <b-col cols="12" md="2">
                            <validation-provider #default="{ errors }" name="Whatsapp Service" vid="whatsapp_service">
                              <b-form-group label="Whatsapp Service">
                                <b-form-checkbox v-model="formData.whatsapp_service" name="check-button" switch />

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
                            type="submit" v-if="hasPermission('web-sales-add-or-edit')">
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea  } from 'bootstrap-vue'
import { getDetail } from '@/network/web-sales'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/web-sales'
import { hasPermission } from '@/auth/utils'
import { getUserSearch } from '@/network/user-platform'

export default {
    components: {
        BCard,
        BLink,
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
        vSelect
    },
    directives: {
        Ripple,
    },
    setup() {
        return {hasPermission}
    },
    methods: {
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (var key in this.formData) {
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                postData(vForm).then(response => {
                    this.$bvToast.toast('Sales has been changed successfully', {
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
            this.formData['show_in_footer'] = (response.data['show_in_footer'] == 1) ? true : false
            this.formData['whatsapp_service'] = (response.data['whatsapp_service'] == 1) ? true : false
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })
        const userOptions = []
        getUserSearch({}).then(response => {
            this.userOptions = response.data;
        }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, timer: 3500, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })

        return {
            formData, required, numeric, userOptions
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
