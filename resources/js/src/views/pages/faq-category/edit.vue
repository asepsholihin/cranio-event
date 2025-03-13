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

                        <!-- Parent Category -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Select Parent Category" vid="parent_id">
                                <b-form-group label="Select Parent Category" description="Leave this option for parent category" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.parent_id" :options="parentCategoryOptions" :clearable="false"
                                :filterable="false" @search="onSearch" :reduce="label => label.id" label="name" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Category Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Category Name" vid="name" rules="required">
                                <b-form-group label="Category Name">
                                <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Category Name -->
                        <!-- <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Slug" vid="slug" rules="required">
                                <b-form-group label="Slug">
                                <b-form-input id="slug" v-model="formData.slug" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col> -->

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
                            type="submit" v-if="hasPermission('faq-add-or-edit')">
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
import { getDetail, postData, getParentCategoriesSearch } from '@/network/faq-category'
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
                    this.$bvToast.toast('category has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                    this.$router.push({ name: 'faq-category' })
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
        getParentCategoriesSearch({ q: search })
        .then(res => {
            vm.parentCategoryOptions = res.data
            loading(false)
        })
        .catch(error => {
            vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            loading(false)
        })
        }, 300),
    },
    data() {
        const parentCategoryOptions =[]
        getParentCategoriesSearch({ q: '' })
        .then(res => {
            this.parentCategoryOptions = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        
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
            formData, required, numeric, parentCategoryOptions
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
