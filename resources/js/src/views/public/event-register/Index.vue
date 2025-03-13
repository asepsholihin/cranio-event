<template>
    <div class="auth-wrapper auth-v2">
        <b-row class="auth-inner m-0">
            <!-- Left Text-->
            <b-col lg="3" class="d-none d-lg-flex align-items-center p-5">
            </b-col>
            <!-- /Left Text-->

            <!-- Register-->
            <b-col lg="6" class="d-flex align-items-center auth-bg px-2 p-lg-5">
                <b-col sm="8" md="8" lg="12" class="px-xl-2 mx-auto">
                    <center><a href="https://www.jejakimani.com/" target="_blank">
                            <b-img :src="logoApp" height="120" />
                        </a></center><br /><br />
                    <b-card-title title-tag="h2" class="font-weight-bold mb-1" v-html="withBrTags(eventData.name)">
                    </b-card-title>
                    <b-card-text class="mb-2" v-html="withBrTags(eventData.description)">
                    </b-card-text>
                    <div v-if="! isSucces">
                        <!-- form -->
                        <validation-observer ref="registerForm">
                            <b-form class="auth-register-form mt-2">
                                <!-- Nama Lengkap -->
                                <b-form-group label="Nama Lengkap" label-for="register-name">
                                    <validation-provider #default="{ errors }" name="Nama Lengkap" vid="name"
                                        rules="required">
                                        <b-form-input size="lg" id="register-name" v-model="formData.name"
                                            name="register-name" :state="errors.length > 0 ? false : null"
                                            placeholder="Nama Lengkap" />
                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>

                                <!-- No Whatsapp -->
                                <b-form-group label="No Whatsapp" label-for="register-no_wa">
                                    <validation-provider #default="{ errors }" name="No Whatsapp" vid="no_hp"
                                        rules="required|integer|min:8|max:14">
                                        <b-form-input size="lg" id="register-no_wa" v-model="formData.no_hp"
                                            name="register-no_wa" :state="errors.length > 0 ? false : null"
                                            placeholder="No Whatsapp" type="number" />
                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>

                                <!-- email -->
                                <b-form-group label="Email  (Untuk Pengiriman Barcode Absensi)" label-for="register-email">
                                    <validation-provider #default="{ errors }" name="Email" rules="required|email"
                                        vid="email">
                                        <b-form-input size="lg" id="register-email" v-model="formData.email"
                                            name="register-email" :state="errors.length > 0 ? false : null"
                                            placeholder="Email" />
                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>

                                <b-form-checkbox class="my-25" id="is-alumni-id" v-model="formData.is_alumni" switch
                                    inline>
                                    <b-form-group label="Sudah Pernah Haji, Umrah dan Islamic Tours Bersama Jejak Imani"
                                        label-for="is-alumni-id" />
                                </b-form-checkbox>

                                <b-button class="mt-1" variant="primary" block type="submit"
                                    @click.prevent="validationForm">
                                    Daftar
                                </b-button>
                            </b-form>
                        </validation-observer>
                    </div>
                    <div v-else>
                        <h3>Berhasil terdaftar, berikut barcode registrasi anda</h3>
                        <center><b-img :src="barcodeUrl" /></center>
                    </div>
                    <!-- divider -->
                    <div class="divider my-2">
                    </div>

                    <div class="auth-footer-btn d-flex justify-content-center">
                        <b-button class="btn-icon rounded-circle" size="lg" variant="outline-instagram"
                            href="https://www.instagram.com/JejakImani/" target="_blank">
                            <feather-icon icon="InstagramIcon" />
                        </b-button>
                        <b-button class="btn-icon rounded-circle" size="lg" variant="outline-facebook"
                            href="https://www.facebook.com/jejakimani/" target="_blank">
                            <feather-icon icon="FacebookIcon" />
                        </b-button>
                        <b-button class="btn-icon rounded-circle" size="lg" variant="outline-twitter"
                            href="https://twitter.com/jejakimani" target="_blank">
                            <feather-icon icon="TwitterIcon" />
                        </b-button>
                        <b-button class="btn-icon rounded-circle" size="lg" variant="outline-google"
                            href="https://www.youtube.com/channel/UCx095g0HfU_-_czn8n1MLUA" target="_blank">
                            <feather-icon icon="YoutubeIcon" />
                        </b-button>
                    </div>
                </b-col>
            </b-col>
            <!-- /Register-->
        </b-row>
    </div>
</template>

<script>
/* eslint-disable global-require */
import Vue from 'vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
// import { VueReCaptcha } from 'vue-recaptcha-v3'
import {
    BRow, BCol, BLink, BButton, BForm, BFormCheckbox, BFormGroup, BFormInput, BInputGroup, BInputGroupAppend, BImg, BCardTitle, BCardText,
} from 'bootstrap-vue'
import { required, email, integer } from '@validations'
import { getPublicDetail, postPublic } from '@/network/event-open-registration'

// Vue.use(VueReCaptcha, { siteKey: '6LcrSkYhAAAAAA9QZy5kYUQBm1WSMmrJnA2yWXGJ', loaderOptions: { autoHideBadge: true } })

export default {
    components: {
        BRow,
        BImg,
        BCol,
        BLink,
        BButton,
        BForm,
        BCardText,
        BCardTitle,
        BFormCheckbox,
        BFormGroup,
        BFormInput,
        BInputGroup,
        BInputGroupAppend,
        // validations
        ValidationProvider,
        ValidationObserver,
    },
    data() {
        const uuid = this.$route.params.id
        getPublicDetail(uuid).then(response => {
            this.eventData = response.data
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            }
        })

        return {
            isSucces: false,
            barcodeUrl: null,
            formData: {},
            eventData: {},
            logoApp: require('@/assets/images/logo/logo.png'),
            // validation
            required,
            email,
            integer
        }
    },
    methods: {
        validationForm() {
            this.formData.event_open_registration_id = this.eventData.uuid
            this.$refs.registerForm.validate().then(success => {
                if (!success)
                    return;
                let ref = this;
                // const recaptcha = this.$recaptchaInstance
                // recaptcha.execute('reCAPTCHA_site_key', { action: 'submit' }).then(function (token) {
                //     ref.formData.token = token
                //     postPublic(ref.formData).then(response => {
                //         ref.barcodeUrl = response.data.barcodeUrl
                //         ref.isSucces = true
                //     }).catch(error => {
                //         if (error.response.data.errors) {
                //             ref.$refs.registerForm.setErrors(error.response.data.errors)
                //         } else {
                //             ref.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                //         }
                //     })
                // });
            })
        },
        withBrTags(item) {
            const itemString = String(item).replace(/\n/g, '<br/>')
            return itemString
        }
    }
}
/* eslint-disable global-require */
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/page-auth.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
