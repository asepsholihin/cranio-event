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
                        <!-- Page Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Page Name" vid="page_id" rules="required">
                                <b-form-group label="Page Name">
                                    <v-select v-model="formData.page_id" :options="pageNameOptions" :clearable="false"
                                        :reduce="label => label.value" />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Whatsapp Text -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Whatsapp Text" vid="whatsapp_text"
                                rules="required">
                                <b-form-group label="Whatsapp Text">
                                    <b-form-textarea id="whatsapp_text" v-model="formData.whatsapp_text"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Google Tag Event -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Google Tag Event" vid="google_tag_event">
                                <b-form-group label="Google Tag Event">
                                    <b-form-input id="google_tag_event" v-model="formData.google_tag_event"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Google Tag Event Category -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Google Tag Event Category"
                                vid="google_tag_event_category">
                                <b-form-group label="Google Tag Event Category">
                                    <b-form-input id="google_tag_event_category"
                                        v-model="formData.google_tag_event_category"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Google Tag Event Label -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Google Tag Event Label"
                                vid="google_tag_event_label">
                                <b-form-group label="Google Tag Event Label">
                                    <b-form-input id="google_tag_event_label" v-model="formData.google_tag_event_label"
                                        :state="errors.length > 0 ? false : null" trim />

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
                            <validation-provider #default="{ errors }" name="Status" vid="status">
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
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit"
                            v-if="hasPermission('web-link-text-add-or-edit')">
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea } from 'bootstrap-vue'
import { getDetail } from '@/network/web-link-text'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/web-link-text'
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
        vSelect,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        const pageNameOptions = [
            { label: 'Homepage', value: 1 },
            { label: 'Contact Us Page', value: 2 },
            { label: 'Umrah Parent Page', value: 3 },
            { label: 'Umrah Bersama Ust. Salim', value: 9 },
            { label: 'Umrah Lebih Hemat', value: 10 },
            { label: 'Umrah Lebih Nyaman', value: 11 },
            { label: 'Tabungan Umrah', value: 12 },
            { label: 'Product Detail Page', value: 4 },
            { label: 'Haji Parent Page', value: 13 },
            { label: 'Haji Khusus', value: 5 },
            { label: 'Haji Furoda', value: 6 },
            { label: 'Wisata Halal', value: 7 },
            { label: 'Tombol Daftar', value: 8 },
            { label: 'Badal Parent', value: 14 },
            { label: 'Badal Haji', value: 15 },
            { label: 'Badal Umroh', value: 16 },

            // new page
            { label: 'Umrah Bersama Ust. Salim Yaqin', value: 17 },
            { label: 'Umrah Bersama Ust. Salim Onyx', value: 18 },
            { label: 'Umrah Bersama Ust. Salim Ruby', value: 19 },
            { label: 'Umrah Bersama Ust. Salim Sapphire', value: 20 },
            { label: 'Umrah Bersama Ust. Salim Sapphire Plus', value: 21 },
            { label: 'Umrah Lebih Hemat Yaqin', value: 22 },
            { label: 'Umrah Lebih Hemat Konsorsium', value: 23 },
            { label: 'Umrah Lebih Hemat Onyx', value: 24 },
            { label: 'Umrah Lebih Hemat Ruby', value: 25 },
            { label: 'Umrah Lebih Nyaman Umrah Plus', value: 26 },
            { label: 'Umrah Lebih Nyaman Sapphire', value: 27 },
            { label: 'Umrah Lebih Nyaman Sapphire Plus', value: 28 },

            // page name
            { label: 'Google Demand Gen', value: 29 },
            { label: 'Google GDN Brand', value: 30 },
            { label: 'Google GDN Umroh', value: 31 },
            { label: 'Google GDN Haji', value: 32 },
            { label: 'Google SEM Haji', value: 33 },
            { label: 'Google SEM Umroh', value: 34 },
            { label: 'Google Youtube Bumper', value: 35 },
            { label: 'Google Youtube CPV', value: 36 },
            { label: 'Google Youtube CPM', value: 37 },
            { label: 'Meta Awareness Umroh', value: 38 },
            { label: 'Meta Awareness Haji', value: 39 },
            { label: 'Meta Traffic Umroh', value: 40 },
            { label: 'Meta Traffic Haji', value: 41 },
            { label: 'Meta Engage Umroh', value: 42 },
            { label: 'Meta Engage Haji', value: 43 },

            { label: 'Promo Ramadhan', value: 44 },
            { label: 'Pop Up Deals', value: 45 },
        ]

        return { hasPermission, pageNameOptions }
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
                    this.$bvToast.toast('Link text has been changed successfully', {
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

<style lang="scss">@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';</style>
