<template>
    <div>
        <b-card>
            <p class="pt-1">
                <b-media class="mb-3" v-if="formData.type == 'banner'">
                    <template #aside>
                        <b-avatar :src="formData.image" :text="avatarText('NA')" size="480px" rounded />
                    </template>
                </b-media>

                <div v-if="formData.type == 'flyer'" class="mb-3">
                    <object :data="previewFlyerUrl" width="100%" height="650">
                        Loading...
                    </object>
                </div>

                <b-button v-if="formData.type == 'banner'" variant="primary" @click="downloadImage()">
                    <span class="text-nowrap">Download Image</span>
                </b-button>
                <div v-if="formData.type == 'flyer'">
                    <b-button variant="primary" @click="downloadFlyer()">
                        <span class="text-nowrap" :disabled="isDownloading"><b-spinner small v-show="isDownloading" /> Download Flyer</span>
                    </b-button>
                    <b-button variant="primary" @click="generateCustomFlyer()">
                        <span class="text-nowrap">Generate Custom Flyer</span>
                    </b-button>
                </div>
            </p>
        </b-card>

        <b-modal v-model="customFlyerModal" ok-title="Submit and Download Flyer"
            @ok="handleCustomFlyer" :busy="isDownloading" centered no-close-on-backdrop ok-only
            title="Generate Custom Name on Flyer">
            <validation-observer ref="valCustomFlyer">
                <!-- Form -->
                <b-form class="p-2">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>
                    <validation-provider #default="{ errors }" name="Name on Flyer" rules="required" vid="custom_name">
                        <b-form-group label="Name on Flyer">
                            <b-form-input v-model="customFlyer.custom_name" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                    <validation-provider #default="{ errors }" name="Phone Number on Flyer" rules="required" vid="custom_no_hp">
                        <b-form-group label="Phone Number on Flyer">
                            <b-form-input v-model="customFlyer.custom_no_hp" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                </b-form>
            </validation-observer>
        </b-modal>
    </div>
</template>

<script>
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia, BSpinner } from 'bootstrap-vue'
import { getDetail, getPreviewFlyerPDF, getDownloadFlyerPDF } from '@/network/media-marketing'
import { downloadMitraMedia } from '@/network/mitra'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import { avatarText } from '@core/utils/filter'
import { hasPermission } from '@/auth/utils'

export default {
    components: {
        BSpinner,
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
        BMedia,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        return {hasPermission, avatarText}
    },
    methods: {
        downloadImage() {
            window.open(downloadMitraMedia(this.formData.id), "_blank");
        },
        downloadFlyer() {
            this.isDownloading = true
            getDownloadFlyerPDF(this.formData.id, this.customFlyer).then(response => {
                const fileURL = window.URL.createObjectURL(new Blob([response.data]))
                const fileLink = document.createElement('a')
                const contentDisposition = response.headers['content-disposition']
                fileLink.href = fileURL;
                let fileName = 'unknown';
                if (contentDisposition) {
                    const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                    if (fileNameMatch.length === 2)
                        fileName = fileNameMatch[1];
                }
                fileLink.setAttribute('download', fileName);
                document.body.appendChild(fileLink);
                fileLink.click();
                this.isDownloading = false
            }).catch(error => {
                this.$bvToast.toast(`${error}`, {
                    title: `Error`,
                    variant: 'danger',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
                this.isDownloading = false
            })
        },
        generateCustomFlyer() {
            this.customFlyerModal = true
        },
        handleCustomFlyer(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.$refs.valCustomFlyer.validate().then(success => {
                if (!success) return
                
                this.downloadFlyer()
            })
        }
    },
    data() {
        const formData = {}
        const previewFlyerUrl = null
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['status'] = (response.data['status'] == 1) ? true : false
            this.previewFlyerUrl = getPreviewFlyerPDF(response.data.id)
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric, previewFlyerUrl, isDownloading: false, customFlyerModal: false, customFlyer: {}
        }
    }
}
</script>
