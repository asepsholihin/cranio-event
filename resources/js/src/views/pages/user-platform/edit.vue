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
                            <validation-provider #default="{ errors }" name="Full Name" vid="name" rules="required">
                                <b-form-group label="Full Name">
                                    <b-form-input v-model="formData.name" name="name"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Email -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
                                <b-form-group label="Email" label-for="email">
                                    <b-form-input v-model="formData.email" :state="errors.length > 0 ? false : null"
                                        trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Access Status -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Access Status" vid="access_status" rules="required">
                                <b-form-group label="Access Status" :state="errors.length > 0 ? false : null">
                                    <v-select v-model="formData.access_status" :options="[{ label: 'Active', value: 1 }, { label: 'Disabled', value: 2 }]" :clearable="false" :reduce="label => label.value" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Department -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Department" vid="departments">
                                <b-form-group label="Department" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.departments" :options="departmentOptions" :clearable="false"
                                    :reduce="label => label.id" label="name" multiple />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Password -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Password" vid="password">
                                <b-form-group label="Change Password"><small>Leave blank if won't change</small>
                                    <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                                    <b-form-input  v-model="formData.password" :state="errors.length > 0 ? false : null"
                                        :type="passwordFieldTypeNew" name="password" autocomplete="off" />
                                    <b-input-group-append is-text>
                                        <feather-icon :icon="passwordToggleIconNew" class="cursor-pointer" @click="togglePasswordNew" />
                                    </b-input-group-append>
                                    </b-input-group>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Role -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Role" vid="role_id" rules="required">
                                <b-form-group label="Role" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.role_id" :options="roleOptions" :clearable="false"
                                    :reduce="label => label.value" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <br/>
                    <b-row>
                         <!-- Permissions -->
                        <b-col cols="12">
                            <div class="mb-1">
                                <b-form-checkbox v-model="allSelected" :indeterminate="indeterminate" @change="toggleAll">
                                    {{ allSelected ? 'Un-select All' : 'Select All' }}
                                </b-form-checkbox>
                            </div>
                            <b-form-group label="Permissions">
                                <b-form-checkbox-group
                                    v-model="permission_list_checked"
                                    :options="permissions_list" switches stacked />
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            type="submit" v-if="hasPermission('user-platform-add-or-edit')">
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormCheckboxGroup  } from 'bootstrap-vue'
import { getDetail } from '@/network/user-platform'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { postData, getDepartmentSearch } from '@/network/user-platform'
import { hasPermission } from '@/auth/utils'
import _ from 'lodash'

export default {
    components: {
        BCard,
        BLink,
        BFormInvalidFeedback,
        BButton,
        vSelect,
        flatPickr,
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
        BFormCheckboxGroup,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        const departmentOptions = []
        const roleOptions = [
            { label: 'Administrator', value: 1 },
            { label: 'Staff', value: 2 },
            { label: 'Manager', value: 3 },
            { label: 'Vice President', value: 4 },
            { label: 'Directors', value: 5 }
        ]
        return {
            hasPermission,
            departmentOptions,
            roleOptions
        }
    },
    computed: {
        passwordToggleIconNew() {
            return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
    },
    watch: {
        permission_list_checked(newValue, oldValue) {
        var lengthPermission = Object.keys(this.permissions_list).length;
        if (newValue.length === 0) {
          this.indeterminate = false
          this.allSelected = false
        } else if (newValue.length === lengthPermission) {
          this.indeterminate = false
          this.allSelected = true
        } else {
          this.indeterminate = true
          this.allSelected = false
        }
      }
    },
    methods: {
        toggleAll(checked) {
            var keys = [];
            for (var key in this.permissions_list) {
                keys.push(key);
            }
            this.permission_list_checked = checked ? keys : []
        },
        togglePasswordNew() {
            this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
        },
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (var key in this.formData) {
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                vForm.append('permission_checked', this.permission_list_checked);
                postData(vForm).then(response => {
                    this.$bvToast.toast('Participant has been changed successfully', {
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
        getDepartmentSearch().then(res => {
            this.departmentOptions = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        getDetail(id).then(response => {
            this.formData = response.data
            var formDepartments = []
            response.data.departments.forEach(element => {
                formDepartments.push(element.id)
            });

            this.formData.departments = formDepartments
            this.permission_list_checked=response.data.permission_checked
            this.permissions_list=response.data.permissions_list
            delete this.formData['permissions_list']
            delete this.formData['roles']
            delete this.formData['permissions']
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric, email, password: '', passwordFieldTypeNew: 'password',
            permission_list_checked:[], permissions_list:[],
            allSelected: false,
            indeterminate: false
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
