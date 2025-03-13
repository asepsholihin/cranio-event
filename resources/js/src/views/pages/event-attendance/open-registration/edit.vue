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

                        <b-media class="mb-2">
                            <template #aside>
                            <b-avatar :src="formData.image_url" :text="avatarText('NA')" size="90px" rounded />
                            </template>
                            <div class="d-flex flex-wrap">
                            <b-button v-if="hasPermission('event-attendance-add-or-edit')" variant="primary" @click="$refs.refInputEl.click()">
                                <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                                <span class="d-none d-sm-inline">Change</span>
                                <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                            </b-button>
                            </div>
                            <div class="mt-1 text-muted">Recommended Size 750 x 750 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                        </b-media>
                        
                        <b-row>
                            <!-- Event Name -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Event Name" vid="name"
                                    rules="required">
                                    <b-form-group label="Event Name">
                                        <b-form-textarea v-model="formData.name" name="name"
                                            :state="errors.length > 0 ? false : null" rows="2" trim />

                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Event Description -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Event Description" vid="description"
                                    rules="required">
                                    <b-form-group label="Event Description">
                                        <b-form-textarea v-model="formData.description" name="description"
                                            :state="errors.length > 0 ? false : null" rows="2" trim />

                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Location -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Location" vid="location"
                                    rules="required">
                                    <b-form-group label="Location">
                                        <b-form-input v-model="formData.location" name="location"
                                            :state="errors.length > 0 ? false : null" trim />

                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Event Date -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" vid="event_date" rules="required"
                                    name="Event Date">
                                    <b-form-group label="Event Date" :state="errors.length > 0 ? false : null">
                                        <flat-pickr v-model="formData.event_date" class="form-control" />
                                        <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Event Start At -->
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

                            <!-- Event End At -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" vid="event_end_at" rules="required" name="Event End At">
                                    <b-form-group label="Event End At" :state="errors.length > 0 ? false : null">
                                    <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.event_end_at" class="form-control" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Speaker -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Speaker" vid="speaker" rules="required">
                                    <b-form-group label="Speaker">
                                    <b-form-input v-model="formData.speaker" name="speaker" :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Paid Event -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Paid Event" vid="is_paid_event">
                                    <b-form-group label="Paid Event">
                                    <b-form-checkbox v-model="formData.is_paid_event" name="check-button" switch />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Number of Seats -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Attendee Limit" vid="number_of_seats" rules="numeric">
                                    <b-form-group label="Attendee Limit">
                                    <b-form-input v-model="formData.number_of_seats" name="pax" :state="errors.length > 0 ? false : null" trim type="number" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>

                            <!-- Price -->
                            <b-col cols="12" md="4">
                                <validation-provider #default="{ errors }" name="Ticket Price" vid="price" rules="numeric">
                                    <b-form-group label="Ticket Price">
                                    <cleave v-model="formData.price" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>
                            
                            <b-col cols="12" md="4">
                                <b-form-checkbox class="mt-75" checked="false"
                                    v-model="formData.close_registration" switch inline>
                                    <b-form-group label="Close Registration?" />
                                </b-form-checkbox>
                            </b-col>
                        </b-row>

                        <!-- Form Actions -->
                        <div class="d-flex mt-2">
                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                                type="submit" v-if="hasPermission('event-attendance-add-or-edit')" :disabled="isLoading">
                                <b-spinner small v-show="isLoading" /> Save Changes
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia, BSpinner } from 'bootstrap-vue'
import { getDetail, postData } from '@/network/event-open-registration'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import { avatarText } from '@core/utils/filter'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { hasPermission } from '@/auth/utils'
import Cleave from 'vue-cleave-component'

export default {
    components: {
        BFormTextarea,
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
        Cleave,
        BMedia,
        BSpinner,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        return { hasPermission, avatarText }
    },
    methods: {
        inputImageRenderer() {
            this.formData.image = this.$refs.refInputEl.files[0]
            const file = this.$refs.refInputEl.files[0]
            const reader = new FileReader()

            reader.addEventListener('load', () => {
                    this.formData.image_url = reader.result
                },
                false,
            )

            if (file) {
                reader.readAsDataURL(file)
            }
        },
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (var key in this.formData) {
                    if (key == 'image_url' || key == 'image_thumbnail_url')
                        continue
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                this.isLoading = true
                postData(vForm).then(response => {
                    this.isLoading = false
                    this.$bvToast.toast('Data has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                })
                .catch(error => {
                    this.isLoading = false
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
        const uuid = this.$route.params.id || ''
        getDetail(uuid).then(response => {
            this.formData = response.data
            this.formData['is_paid_event'] = (response.data['is_paid_event'] == 1) ? true : false
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric, email,
            optionClave: {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
            },
            isLoading: false
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
