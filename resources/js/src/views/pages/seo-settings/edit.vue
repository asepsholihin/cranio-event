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
                    
                    <!-- Meta Title -->
                    <b-row>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Meta Title" vid="meta_title">
                                <b-form-group label="Meta Title">
                                <b-form-input id="meta_title" name="meta_title" v-model="formData.meta_title" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <!-- Meta Keyword -->
                    <b-row>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Meta Keyword" vid="meta_keyword">
                                <b-form-group label="Meta Keyword">
                                <b-form-input id="meta_keyword" name="meta_keyword" v-model="formData.meta_keyword" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <!-- Meta Description -->
                    <b-row>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Meta Description" vid="meta_description">
                                <b-form-group label="Meta Description" description="Maximum : 300 Words">
                                    <b-form-textarea id="meta_description" name="meta_description" v-model="formData.meta_description"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"
                                    ></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <!-- Canonical -->
                    <b-row>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Canonical" vid="canonical">
                                <b-form-group label="Canonical">
                                <b-form-input id="canonical" name="canonical" v-model="formData.canonical" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row class="mt-2">
                        <b-col cols="12" md="6">
                            <b-media class="mb-2">
                                <template #aside>
                                <b-avatar :src="formData.og_image" :text="avatarText('NA')" size="90px" rounded />
                                </template>
                                <div class="d-flex flex-wrap">
                                <b-button v-if="hasPermission('web-settings-add-or-edit')" variant="primary" @click="$refs.refInputElOg.click()">
                                    <input ref="refInputElOg" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer('og')">
                                    <span class="d-none d-sm-inline">og:image</span>
                                    <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                                </b-button>
                                </div>
                                <div class="mt-1 text-muted">Recommended Size 200 x 200 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                            </b-media>

                            <!-- og:type -->
                            <validation-provider #default="{ errors }" name="og:type" vid="og_type" rules="required">
                                <b-form-group :state="errors.length > 0 ? false : null">
                                    <template slot="label">og:type</template>
                                    <v-select v-model="formData.og_type" :options="ogTypeOptions"
                                        :clearable="false" :reduce="label => label.value" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- og:url -->
                            <validation-provider #default="{ errors }" name="og:url" vid="og_url">
                                <b-form-group label="og:url">
                                <b-form-input id="og_url" name="og_url" v-model="formData.og_url" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- og:title -->
                            <validation-provider #default="{ errors }" name="og:title" vid="og_title">
                                <b-form-group label="og:title">
                                <b-form-input id="og_title" name="og_title" v-model="formData.og_title" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- og:description -->
                            <validation-provider #default="{ errors }" name="og:description" vid="og_description">
                                <b-form-group label="og:description" description="Maximum : 300 Words">
                                    <b-form-textarea id="og_description" name="og_description" v-model="formData.og_description"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"
                                    ></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        
                        <b-col cols="12" md="6">
                            <b-media class="mb-2">
                                <template #aside>
                                <b-avatar :src="formData.twitter_image" :text="avatarText('NA')" size="90px" rounded />
                                </template>
                                <div class="d-flex flex-wrap">
                                <b-button v-if="hasPermission('web-settings-add-or-edit')" variant="primary" @click="$refs.refInputElTwitter.click()">
                                    <input ref="refInputElTwitter" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer('twitter')">
                                    <span class="d-none d-sm-inline">twitter:image</span>
                                    <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                                </b-button>
                                </div>
                                <div class="mt-1 text-muted">Recommended Size 200 x 200 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                            </b-media>

                            <!-- twitter:card -->
                            <validation-provider #default="{ errors }" name="twitter:card" vid="twitter_card" rules="required">
                                <b-form-group :state="errors.length > 0 ? false : null">
                                    <template slot="label">twitter:card</template>
                                    <v-select v-model="formData.twitter_card" :options="cardOptions"
                                        :clearable="false" :reduce="label => label.value" />
                                    <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- twitter:site -->
                            <validation-provider #default="{ errors }" name="twitter:site" vid="twitter_site">
                                <b-form-group label="twitter:site" description="@username for the website used in the card footer.">
                                    <b-form-input id="twitter_site" name="twitter_site" v-model="formData.twitter_site" :state="errors.length > 0 ? false : null"
                                    trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- twitter:title -->
                            <validation-provider #default="{ errors }" name="twitter:title" vid="twitter_title">
                                <b-form-group label="twitter:title">
                                <b-form-input id="twitter_title" name="twitter_title" v-model="formData.twitter_title" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        
                            <!-- twitter:description -->
                            <validation-provider #default="{ errors }" name="twitter:description" vid="twitter_description">
                                <b-form-group label="twitter:description" description="Maximum : 300 Words">
                                    <b-form-textarea id="twitter_description" name="twitter_description" v-model="formData.twitter_description"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"
                                    ></b-form-textarea>
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData, getDetail } from '@/network/seo-settings'
import { hasPermission } from '@/auth/utils'

export default {
    components: {
        BCard,
        BLink,
        BFormInvalidFeedback,
        BButton,
        vSelect,
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
        BMedia,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        const ogTypeOptions = [
            { label: 'Website', value: 'website' },
            { label: 'Article', value: 'article' },
        ]
        const cardOptions = [
            { label: 'Summary', value: 'summary' },
            { label: 'Summary Large Image', value: 'summary_large_image' },
            { label: 'App', value: 'app' },
            { label: 'Player', value: 'player' },
        ]
        return {hasPermission, avatarText, ogTypeOptions, cardOptions}
    },
    methods: {
        inputImageRenderer(type) {
            var file = null
            if(type == 'og') {
                file = this.$refs.refInputElOg.files[0]
                this.formData.og_file = this.$refs.refInputElOg.files[0]
            }
            if(type == 'twitter') {
                file = this.$refs.refInputElTwitter.files[0]
                this.formData.twitter_file = this.$refs.refInputElTwitter.files[0]
            }
            const reader = new FileReader()

            reader.addEventListener(
                'load',
                () => {
                    if(type == 'og') {
                        this.formData.og_image = reader.result
                    }
                    if(type == 'twitter') {
                        this.formData.twitter_image = reader.result
                    }
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
                    if (key == 'og_image')
                        continue
                    if (key == 'twitter_image')
                        continue
                    if (this.formData[key] != null)
                        vForm.append(key, this.formData[key])
                }
                postData(vForm).then(response => {
                    this.$bvToast.toast('SEO settings has been changed successfully', {
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

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
