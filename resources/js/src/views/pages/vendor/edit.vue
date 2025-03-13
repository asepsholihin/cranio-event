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
                        <b-col sm="6">
                            <!-- Name -->
                            <validation-provider #default="{ errors }" name="Name" vid="name" rules="required">
                                <b-form-group label="Name">
                                <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Alamat -->
                            <validation-provider #default="{ errors }" name="Alamat" vid="address">
                                <b-form-group label="Address">
                                <b-form-textarea id="address" v-model="formData.address" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="4">
                            <!-- PIC Name -->
                            <validation-provider #default="{ errors }" name="PIC Name" vid="address">
                                <b-form-group label="PIC Name">
                                <b-form-input id="pic" v-model="formData.pic" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        
                        <b-col sm="4">
                            <!-- Phone -->
                            <validation-provider #default="{ errors }" name="Phone" vid="phone" rules="numeric">
                                <b-form-group label="Phone">
                                <b-form-input id="phone" v-model="formData.phone" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        
                        <b-col sm="4">
                            <!-- Email -->
                            <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                                <b-form-group label="Email">
                                <b-form-input id="email" v-model="formData.email" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="6">
                            <!-- Speciality -->
                            <validation-provider #default="{ errors }" name="Speciality" vid="speciality">
                                <b-form-group label="Speciality">
                                <b-form-input id="speciality" v-model="formData.speciality" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Equipments -->
                            <validation-provider #default="{ errors }" name="Equipments" vid="equipments">
                                <b-form-group label="Equipments">
                                <v-select id="equipments" multiple v-model="formData.equipment_ids" :options="equipmentList"
                                    :clearable="true" :reduce="label => label.id" label="name" />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="6">
                            <!-- Description -->
                            <validation-provider #default="{ errors }" name="Description" vid="description">
                                <b-form-group label="Description">
                                <b-form-textarea id="description" v-model="formData.description" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Batas Waktu Retur -->
                            <validation-provider #default="{ errors }" name="Batas Waktu Retur (Hari)" vid="guarantee_time" rules="numeric">
                                <b-form-group label="Batas Waktu Retur (Hari)">
                                <b-form-input id="guarantee_time" v-model="formData.guarantee_time" :state="errors.length > 0 ? false : null"
                                trim />

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
                            type="submit" v-if="hasPermission('vendor-add-or-edit')">
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
import { getDetail } from '@/network/vendor'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
import { postData } from '@/network/vendor'
import { hasPermission } from '@/auth/utils'
import { getEquipmentSearch } from '@/network/equipment'

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
        vSelect,

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
                    this.$bvToast.toast('Vendor has been changed successfully', {
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
        
        getEquipmentSearch().then(response => {
            this.equipmentList = response.data
            getDetail(id).then(response => {
                this.formData = response.data
                this.formData['equipment_ids'] = JSON.parse(response.data.equipment_ids)
            }).catch(error => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors)
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data)
                }
            })
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        return {
            formData, required, numeric, email,
            equipmentList: [],
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
