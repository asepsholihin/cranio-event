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
                        <!-- Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Name" vid="name" rules="required">
                              <b-form-group label="Name">
                                <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>

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
                            type="submit" v-if="hasPermission('land-arrangement-add-or-edit')">
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
import { getDetail } from '@/network/city'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import { postData } from '@/network/city'
import { hasPermission } from '@/auth/utils'

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
                    this.$bvToast.toast('City has been changed successfully', {
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
