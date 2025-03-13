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

                        <!-- Hotel -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Select Hotel" vid="hotel_id" rules="required">
                                <b-form-group label="Select Hotel" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.hotel_id" :options="hotelOptions" :clearable="false"
                                :filterable="false" @search="onSearch" :reduce="label => label.id" label="name" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Room Type Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Room Type Name" vid="name" rules="required">
                                <b-form-group label="Room Type Name">
                                <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Pax Per Room -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Pax Per Room" vid="pax_per_room" rules="numeric|max_value:5|min_value:0">
                                <b-form-group label="Pax Per Room">
                                <b-form-input type="number" id="pax_per_room" v-model="formData.pax_per_room" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Price Per Pax -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Price Per Pax" vid="price_per_pax" rules="required|numeric">
                                <b-form-group label="Price Per Pax">
                                <cleave v-model="formData.price_per_pax" id="number" class="form-control"
                                    :options="optionClave"
                                    :state="errors.length > 0 ? false : null" />

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
import vSelect from 'vue-select'
import { getDetail } from '@/network/hotel-room-type'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import { postData } from '@/network/hotel-room-type'
import { hasPermission } from '@/auth/utils'
import { getHotelSearch } from '@/network/hotel'
import _ from 'lodash'
import Cleave from 'vue-cleave-component'

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
        vSelect,
        Cleave,
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
                    this.$bvToast.toast('Hotel has been changed successfully', {
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
        getHotelSearch({ q: search })
        .then(res => {
            vm.hotelOptions = res.data
            loading(false)
        })
        .catch(error => {
            vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            loading(false)
        })
        }, 300),
    },
    data() {
        const hotelOptions =[]
        getHotelSearch({ q: '' })
        .then(res => {
            this.hotelOptions = res.data
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
            formData, required, numeric, hotelOptions, 
            optionClave:{
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
            },
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
