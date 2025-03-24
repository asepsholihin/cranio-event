<template>
<div>
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
                        <!-- Event -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Event" vid="event" rules="required">
                                <b-form-group label="Event" :state="errors.length > 0 ? false : null">
                                    <v-select v-model="formData.event" :options="[{ label: 'Seminar', value: 'Seminar' }, {label: 'Workshop', value: 'Workshop'}]" :clearable="false" :reduce="label => label.value" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Event Name -->
                        <b-col cols="12" md="4">
                             <validation-provider #default="{ errors }" name="Event Name" vid="event_name" rules="required">
                                <b-form-group label="Event Name">
                                    <b-form-input v-model="formData.name" name="event_name" :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Access Date -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="event_date" rules="required" name="Event Date">
                                <b-form-group label="Event Date" :state="errors.length > 0 ? false : null">
                                    <flat-pickr v-model="formData.event_date" class="form-control" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Access At -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="event_at" rules="required" name="Event Start At">
                                <b-form-group label="Event Start At" :state="errors.length > 0 ? false : null">
                                    <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.event_at" class="form-control" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <b-col cols="12" md="4">
                        <!-- Event End At -->
                        <validation-provider #default="{ errors }" vid="event_end_at" rules="required" name="Event End At">
                            <b-form-group label="Event End At" :state="errors.length > 0 ? false : null">
                                <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.event_end_at" class="form-control" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        </b-col>

                        <!-- Location -->
                        <b-col cols="12" md="4">
                             <validation-provider #default="{ errors }" name="Location" vid="location" rules="required">
                                <b-form-group label="Location">
                                    <b-form-input v-model="formData.location" name="location" :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <b-col cols="12" md="4">
                            <!-- Hotel Name -->
                            <validation-provider #default="{ errors }" name="Hotel Name" vid="hotel_name">
                                <b-form-group label="Hotel Name">
                                    <b-form-input v-model="formData.hotel_name" name="hotel_name" :state="errors.length > 0 ? false : null" trim />
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
                            type="submit" v-if="hasPermission('event-attendance-add-or-edit')">
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
</div>
</template>

<script>
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox } from 'bootstrap-vue'
import { getDetail, postData } from '@/network/event-attendance'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { hasPermission } from '@/auth/utils'
import { getHotelSearch } from '@/network/hotel'
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
                postData(this.formData).then(response => {
                    this.$bvToast.toast('Data has been changed successfully', {
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
        const transitOptions = []
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['open_gate_split'] = (response.data['open_gate_split'] == 1) ? true : false
            this.formData['open_gate'] = JSON.parse(response.data['open_gate'])
            // console.log(JSON.parse(response.data.open_gate))
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        getHotelSearch().then(response => {
            this.transitOptions = response.data;
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        return {
            transitOptions,
            formData, required, numeric,
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
