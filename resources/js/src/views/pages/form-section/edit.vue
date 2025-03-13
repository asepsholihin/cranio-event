<style>

.bulleted-list .seo-score-icon.bad:before {
    border: 5px solid #dc3232;
}

.bulleted-list .seo-score-icon.good:before {
    border: 5px solid #7ad03a;
}

.bulleted-list .seo-score-icon.ok:before {
    border: 5px solid #ee7c1b;
}

.bulleted-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
}

.bulleted-list li:before {
    content: '';
    width: 0;
    height: 0;
    top: 6px;
    left: 0;
    z-index: 1;
    margin-right: 8px;
    position: absolute;
}

.bulleted-list li {
    padding-left: 20px !important;
}
</style>

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
                        <b-col cols="12" md="6">
                            <!-- Section Name -->
                            <validation-provider #default="{ errors }" name="Section Name" vid="name" rules="required">
                                <b-form-group label="Section Name">
                                    <b-form-input id="name" v-model="formData.name"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <hr>
                    <h3 class="mb-1">Section Options</h3>
                    
                    <b-row>
                        <!-- Description -->
                        <b-col cols="12" md="6">
                            <div v-for="(item, index) in formData.options" :key="index">
                                <div v-if="index > 0 || item.id">
                                    <b-button class="float-right p-0 text-danger" variant="link" @click="removeOption(index)">
                                        <feather-icon icon="XCircleIcon" class="mr-25" />
                                    </b-button>
                                </div>
                                <validation-provider #default="{ errors }" name="Name" vid="option_name" rules="required">
                                    <b-form-group label="Name">
                                        <b-form-input id="option_name" v-model="formData.options[index]"
                                            :state="errors.length > 0 ? false : null" trim />

                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <b-button variant="default" @click="addOption()">
                                    <feather-icon icon="PlusIcon" class="mr-25" />
                                    <span>Add More Options</span>
                                </b-button>
                            </div>
                        </b-col>
                    </b-row>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            @click.prevent="onSubmit" :disabled="loadingSubmit" v-if="hasPermission('survey-add-or-edit')">
                            Submit
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox, BFormTextarea, BMedia, BListGroup, BListGroupItem } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData, getDetail } from '@/network/form-section'
import { hasPermission } from '@/auth/utils'

export default {
    components: {
        BCard,
        BLink,
        BFormInvalidFeedback,
        BButton,
        vSelect,
        BAlert,
        BForm,
        BRow,
        BCol,
        BFormGroup,
        BFormInput,
        BFormFile,
        BInputGroup,
        BInputGroupAppend,
        BFormCheckboxGroup,
        BFormCheckbox,
        BFormTextarea,
        BMedia,
        BListGroup, 
        BListGroupItem,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        return { hasPermission }
    },
    methods: {
        resetForm() {
            this.formData = { options: [""] }
        },
        addOption() {
            this.formData.options.push("")
        },
		removeOption(index) {
			this.formData.options.splice(index, 1)
		},
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (var key in this.formData) {
                    if (this.formData[key] != null && key != 'options')
                        vForm.append(key, this.formData[key])
                    if (key == 'options')
                        vForm.append('options', JSON.stringify(this.formData[key]))
                }
                this.loadingSubmit = true
                postData(vForm).then(response => {
                    this.$bvToast.toast('Form Section has been created successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })

                    this.$router.push({ name: 'form-section' })
                    this.loadingSubmit = false
                })
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors)
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data)
                    }
                    this.loadingSubmit = false
                })
            })
        },
    },
    data() {
        const formData = {
            options: [""]
        }

        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['options'] = JSON.parse(response.data.options)
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric,
            loadingSubmit: false
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
