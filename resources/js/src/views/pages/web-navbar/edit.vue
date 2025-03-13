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

                        <!-- Parent Navbar -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Select Parent Navbar" vid="parent_id">
                                <b-form-group label="Select Parent Navbar" description="Leave this option for parent navbar" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.parent_id" :options="parentOptions" :clearable="false"
                                :filterable="false" @search="onSearch" :reduce="label => label.id" label="title" />
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
                                <b-form-input id="title" v-model="formData.title" :state="errors.length > 0 ? false : null"
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

                        <!-- Order -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Order" vid="order" rules="required|numeric">
                                <b-form-group label="Order">
                                <b-form-input id="order" v-model="formData.order" :state="errors.length > 0 ? false : null" type="number"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Status -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Show in Menu" vid="show" rules="required">
                                <b-form-group label="Show in Menu">
                                <b-form-checkbox v-model="formData.show" name="check-button" switch />

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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea  } from 'bootstrap-vue'
import vSelect from 'vue-select'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import { hasPermission } from '@/auth/utils'
import { getDetail, postData, getParentNavbarSearch } from '@/network/web-navbar'
import _ from 'lodash'

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
                    this.$bvToast.toast('Navbar has been changed successfully', {
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
        onSearch(search, loading) {
        if (search.length) {
            loading(true);
            this.search(loading, search, this);
        }
        },
        search: _.debounce((loading, search, vm) => {
        getParentNavbarSearch({ q: search })
        .then(res => {
            vm.parentOptions = res.data
            loading(false)
        })
        .catch(error => {
            vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            loading(false)
        })
        }, 300),
    },
    data() {
        const parentOptions = []
        getParentNavbarSearch()
        .then(res => {
            this.parentOptions = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        
        const formData = {}
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['show'] = (response.data['show'] == 1) ? true : false
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric, parentOptions
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
