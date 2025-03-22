<template>
    <div>
        <b-row class="mb-2">
            <b-col cols="12">
                <h4>{{ participant.name }}</h4>
                <h5>JI CODE: {{ participant.ji_code }}</h5>

                <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                    size="sm" @click="downloadFileAll()" class="mt-1 mb-2" :disabled="isDownloading">
                    <b-spinner small v-show="isDownloading" />
                    <feather-icon icon="DownloadIcon" size="16" />
                    <span class="d-none d-sm-inline">Download All Document</span>
                </b-button>
            </b-col>
        </b-row>

        <b-row class="mb-2">
            <b-col cols="12" sm="6">
                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>1. Pas Photo</h4> 
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Pas Photo')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <b-img v-if="filePhotoVerifyImg != null" center :src="filePhotoVerifyImg" thumbnail fluid />
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl0.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl0" type="file" class="d-none"
                            @input="inputImageRenderer(0)">
                        <feather-icon icon="ImageIcon" />
                        <span class="d-none d-sm-inline">Change</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[0]" variant="primary"
                        size="sm" @click="uploadPassPhoto" class="mt-25 ml-50" :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>2. KTP</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('KTP')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <div v-if="fileKTPVerifyImg != null">
                    <object v-if="fileKTPVerifyImg.includes('.pdf')" :data="fileKTPVerifyImg" width="100%" height="400" />
                    <b-img v-else center :src="fileKTPVerifyImg" thumbnail fluid />
                </div>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl1.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl1" type="file" class="d-none"
                            @input="inputImageRenderer(1)">
                        <span class="d-none d-sm-inline">Change</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileKTPVerifyImg != null" variant="danger"
                        size="sm" @click="deleteFile(1, 'KTP', 'file_ktp_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[1]" variant="primary"
                        size="sm" @click="uploadVerificationFile(1, 'KTP')" class="mt-25 ml-50" :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>3. KK</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Kartu Keluarga')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <div v-if="fileKKVerifyImg != null">
                    <object v-if="fileKKVerifyImg.includes('.pdf')" :data="fileKKVerifyImg" width="100%" height="400" />
                    <b-img v-else center :src="fileKKVerifyImg" thumbnail fluid />
                </div>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl2.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl2" type="file" class="d-none"
                            @input="inputImageRenderer(2)">
                        <span class="d-none d-sm-inline">Change</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileKKVerifyImg != null" variant="danger"
                        size="sm" @click="deleteFile(2, 'Kartu Keluarga', 'file_kk_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[2]" variant="primary"
                        size="sm" @click="uploadVerificationFile(2, 'Kartu Keluarga')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>4. Passport</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Passport')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col cols="6">
                        <b-form-group label="No Passport">
                            <b-form-input v-model="verificationForm.no_passport" trim />
                        </b-form-group>
                    </b-col>
                    <b-col cols="6">
                        <b-form-group label="Expired Date">
                            <flat-pickr v-model="verificationForm.passport_expired_date" class="form-control"
                                :config="{ altInput: true }" />
                        </b-form-group>
                    </b-col>
                    <b-col cols="12">
                        <b-form-group label="Catatan">
                            <b-form-input v-model="verificationForm.passport_notes" trim />
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row v-if="filePassportImages.length > 0">
                    <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in filePassportImages" :key="index">
                        <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                        <b-img v-else center :src="index" thumbnail fluid />
                        <b-button v-if="hasPermission('participant-file-upload')" variant="danger" size="sm"
                            @click="deleteSpecificFile(3, 'Passport', index, i)" class="mt-25" :disabled="isSubmitModal">
                            <b-spinner small v-show="isSubmitModal" />
                            <feather-icon icon="Trash2Icon" size="16" />
                            <span class="d-none d-sm-inline">Delete</span>
                        </b-button>
                    </b-col>
                </b-row>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl3.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl3" multiple type="file" class="d-none"
                            @input="inputImageRenderer(3)" @click="$refs.refInputEl3.value=null">
                        <span class="d-none d-sm-inline">Add</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && filePassportImages.length > 0" variant="danger"
                        size="sm" @click="deleteFile(3, 'Passport', 'file_passport_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete</span>
                    </b-button>

                    <b-button
                        v-if="hasPermission('participant-file-upload') && verificationForm.photo[3] && filePassportFiles.length > 0"
                        variant="primary" size="sm" @click="uploadVerificationFile(3, 'Passport')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>5. Akta</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Akta')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <div v-if="fileAktaVerifyImg != null">
                    <object v-if="fileAktaVerifyImg.includes('.pdf')" :data="fileAktaVerifyImg" width="100%" height="400" />
                    <b-img v-else center :src="fileAktaVerifyImg" thumbnail fluid />
                </div>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl4.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl4" type="file" class="d-none"
                            @input="inputImageRenderer(4)">
                        <span class="d-none d-sm-inline">Change</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileAktaVerifyImg != null" variant="danger"
                        size="sm" @click="deleteFile(4, 'Akta', 'file_akta_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[4]" variant="primary"
                        size="sm" @click="uploadVerificationFile(4, 'Akta')" class="mt-25 ml-50" :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>6. Buku Nikah</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Buku Nikah')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <b-row v-if="fileBNikahImages.length > 0">
                    <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in fileBNikahImages" :key="index">
                        <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                        <b-img v-else center :src="index" thumbnail fluid />
                        <b-button v-if="hasPermission('participant-file-upload')" variant="danger" size="sm"
                            @click="deleteSpecificFile(5, 'Buku Nikah', index, i)" class="mt-25" :disabled="isSubmitModal">
                            <b-spinner small v-show="isSubmitModal" />
                            <feather-icon icon="Trash2Icon" size="16" />
                            <span class="d-none d-sm-inline">Delete</span>
                        </b-button>
                    </b-col>
                </b-row>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl5.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl5" multiple type="file" class="d-none"
                            @input="inputImageRenderer(5)" @click="$refs.refInputEl5.value=null">
                        <span class="d-none d-sm-inline">Add</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileBNikahImages.length > 0" variant="danger"
                        size="sm" @click="deleteFile(5, 'Buku Nikah', 'file_buku_nikah_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete All</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[5]" variant="primary"
                        size="sm" @click="uploadVerificationFile(5, 'Buku Nikah')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>7. Buku Kuning</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Buku Kuning')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">
                        <b-form-group label="Catatan">
                            <b-form-input v-model="verificationForm.buku_kuning_notes" trim />
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row v-if="fileBKuningImages.length > 0">
                    <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in fileBKuningImages" :key="index">
                        <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                        <b-img v-else center :src="index" thumbnail fluid />
                        <b-button v-if="hasPermission('participant-file-upload')" variant="danger" size="sm"
                            @click="deleteSpecificFile(6, 'Buku Kuning', index, i)" class="mt-25" :disabled="isSubmitModal">
                            <b-spinner small v-show="isSubmitModal" />
                            <feather-icon icon="Trash2Icon" size="16" />
                            <span class="d-none d-sm-inline">Delete</span>
                        </b-button>
                    </b-col>
                </b-row>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl6.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl6" multiple type="file" class="d-none"
                            @input="inputImageRenderer(6)" @click="$refs.refInputEl6.value=null">
                        <span class="d-none d-sm-inline">Add</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileBKuningImages.length > 0" variant="danger"
                        size="sm" @click="deleteFile(6, 'Buku Kuning', 'file_buku_kuning_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete All</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[6]" variant="primary"
                        size="sm" @click="uploadVerificationFile(6, 'Buku Kuning')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-2 mb-md-1">
                        <h4>8. Kartu Vaksin</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('Kartu Vaksin')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">
                        <b-form-group label="Nama Sesuai Kartu Vaksin">
                            <b-form-input v-model="verificationForm.full_name_vaccine" trim />
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row v-if="fileKVaksinImages.length > 0">
                    <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in fileKVaksinImages" :key="index">
                        <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                        <b-img v-else center :src="index" thumbnail fluid />
                        <b-button v-if="hasPermission('participant-file-upload')" variant="danger" size="sm"
                            @click="deleteSpecificFile(7, 'Kartu Vaksin', index, i)" class="mt-25" :disabled="isSubmitModal">
                            <b-spinner small v-show="isSubmitModal" />
                            <feather-icon icon="Trash2Icon" size="16" />
                            <span class="d-none d-sm-inline">Delete</span>
                        </b-button>
                    </b-col>
                </b-row>
                <h5 v-else>No Available</h5>
                <div>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl7.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, image/webp, application/pdf" ref="refInputEl7" multiple type="file" class="d-none"
                            @input="inputImageRenderer(7)" @click="$refs.refInputEl7.value=null">
                        <span class="d-none d-sm-inline">Add</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileKVaksinImages.length > 0" variant="danger"
                        size="sm" @click="deleteFile(7, 'Kartu Vaksin', 'file_kartu_vaksin_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="16" />
                        <span class="d-none d-sm-inline">Delete All</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[7]" variant="primary"
                        size="sm" @click="uploadVerificationFile(7, 'Kartu Vaksin')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="16" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                
                <hr />

                <b-row>
                    <b-col cols="12" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
                        <h4>9. BPJS</h4>
                        <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                            size="sm" @click="downloadFile('BPJS')" class="ml-1" :disabled="isDownloading">
                            <b-spinner small v-show="isDownloading" />
                            <feather-icon icon="DownloadIcon" size="16" />
                            <span class="d-none d-sm-inline">Download</span>
                        </b-button>
                    </b-col>
                </b-row>
                <b-row v-if="fileBPJSImages.length > 0">
                    <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in fileBPJSImages" :key="index">
                        <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                        <b-img v-else center :src="index" thumbnail fluid />
                        <b-button v-if="hasPermission('participant-file-upload')" variant="danger" size="sm"
                            @click="deleteSpecificFile(8, 'BPJS', index, i)" class="mt-25" :disabled="isSubmitModal">
                            <b-spinner small v-show="isSubmitModal" />
                            <feather-icon icon="Trash2Icon" size="12" />
                            <span class="d-none d-sm-inline">Delete</span>
                        </b-button>
                    </b-col>
                </b-row>
                <h5 v-else class="text-center">No Available</h5>
                <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                    @click="$refs.refInputEl8.click()" class="mt-25" :disabled="isSubmitModal">
                    <input accept="image/jpeg, image/png, application/pdf" ref="refInputEl8" multiple type="file" class="d-none"
                        @input="inputImageRenderer(8)" @click="$refs.refInputEl8.value=null">
                    <span class="d-none d-sm-inline">Add</span>
                    <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                </b-button>

                <b-button v-if="hasPermission('participant-file-upload') && fileBPJSImages.length > 0" variant="danger"
                    size="sm" @click="deleteFile(8, 'BPJS', 'file_bpjs_verified')" class="mt-25 ml-50"
                    :disabled="isSubmitModal">
                    <b-spinner small v-show="isSubmitModal" />
                    <feather-icon icon="Trash2Icon" size="12" />
                    <span class="d-none d-sm-inline">Delete All</span>
                </b-button>

                <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[8]" variant="primary"
                    size="sm" @click="uploadVerificationFile(8, 'BPJS')" class="mt-25 ml-50" :disabled="isSubmitModal">
                    <b-spinner small v-show="isSubmitModal" />
                    <feather-icon icon="UploadCloudIcon" size="12" />
                    <span class="d-none d-sm-inline">Upload</span>
                </b-button>
                <hr />

                <div>
                    <b-row>
                        <b-col cols="12" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
                            <h4>10. Hasil MCU Kesehatan</h4>
                            <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                                size="sm" @click="downloadFile('MCU')" class="ml-1" :disabled="isDownloading">
                                <b-spinner small v-show="isDownloading" />
                                <feather-icon icon="DownloadIcon" size="16" />
                                <span class="d-none d-sm-inline">Download</span>
                            </b-button>
                        </b-col>
                    </b-row>
                    <div v-if="fileMCUVerifyImg != null">
                        <object v-if="fileMCUVerifyImg.includes('.pdf')" :data="fileMCUVerifyImg" width="100%" height="400" />
                        <b-img v-else center :src="fileMCUVerifyImg" thumbnail fluid />
                    </div>
                    <h5 v-else class="text-center">No Available</h5>
                    <b-button v-if="hasPermission('participant-file-upload')" variant="primary" size="sm"
                        @click="$refs.refInputEl10.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, application/pdf" ref="refInputEl10" type="file" class="d-none"
                            @input="inputImageRenderer(10)">
                        <span class="d-none d-sm-inline">Change</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && fileMCUVerifyImg != null" variant="danger"
                        size="sm" @click="deleteFile(9, 'MCU', 'file_mcu_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="12" />
                        <span class="d-none d-sm-inline">Delete</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[10]" variant="primary"
                        size="sm" @click="uploadVerificationFile(10, 'MCU')" class="mt-25 ml-50" :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="12" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
                <hr/>

                <div>
                    <b-row>
                        <b-col cols="12" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
                            <h4>11. Surat Keterangan</h4>
                            <b-button v-if="hasPermission('participant-file-download')" variant="primary"
                                size="sm" @click="downloadFile('Surat Keterangan')" class="ml-1" :disabled="isDownloading">
                                <b-spinner small v-show="isDownloading" />
                                <feather-icon icon="DownloadIcon" size="16" />
                                <span class="d-none d-sm-inline">Download</span>
                            </b-button>
                        </b-col>
                        <b-col cols="12">
                            <small>(Keterangan layak terbang, surat keterangan sehat / dokter)</small>
                        </b-col>
                    </b-row>
                    <b-row v-if="fileSuratKeteranganImages.length > 0">
                        <b-col cols="6" class="mb-2 text-center" v-for="(index, i) in fileSuratKeteranganImages" :key="index">
                            <object v-if="index.includes('.pdf') || index.includes('blob')" :data="index" width="100%" height="400" />
                            <b-img v-else center :src="index" thumbnail fluid />
                            <b-button v-if="hasPermission('umroh-trip-add-or-edit')" variant="danger" size="sm"
                                @click="deleteSpecificFile(11, 'Surat Keterangan', index, i)" class="mt-25" :disabled="isSubmitModal">
                                <b-spinner small v-show="isSubmitModal" />
                                <feather-icon icon="Trash2Icon" size="12" />
                                <span class="d-none d-sm-inline">Delete</span>
                            </b-button>
                        </b-col>
                    </b-row>
                    <h5 v-else class="text-center">No Available</h5>
                    <b-button v-if="hasPermission('umroh-trip-add-or-edit')" variant="primary" size="sm"
                        @click="$refs.refInputEl11.click()" class="mt-25" :disabled="isSubmitModal">
                        <input accept="image/jpeg, image/png, application/pdf" ref="refInputEl11" multiple type="file" class="d-none"
                            @input="inputImageRenderer(11)" @click="$refs.refInputEl11.value=null">
                        <span class="d-none d-sm-inline">Add</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>

                    <b-button v-if="hasPermission('umroh-trip-add-or-edit') && fileSuratKeteranganImages.length > 0" variant="danger"
                        size="sm" @click="deleteFile(10, 'Surat Keterangan', 'file_surat_keterangan_verified')" class="mt-25 ml-50"
                        :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="Trash2Icon" size="12" />
                        <span class="d-none d-sm-inline">Delete All</span>
                    </b-button>

                    <b-button v-if="hasPermission('participant-file-upload') && verificationForm.photo[11]" variant="primary"
                        size="sm" @click="uploadVerificationFile(11, 'Surat Keterangan')" class="mt-25 ml-50" :disabled="isSubmitModal">
                        <b-spinner small v-show="isSubmitModal" />
                        <feather-icon icon="UploadCloudIcon" size="12" />
                        <span class="d-none d-sm-inline">Upload</span>
                    </b-button>
                </div>
            </b-col>
        </b-row>
    </div>
</template>
<script>
import { BTable, BFormRadio, BFormRadioGroup, BImg, BSidebar, BButton, BRow, BCol, BAvatar, BForm, BMedia, BSpinner, BPagination, BFormInput, BDropdown, BDropdownItem, BDropdownDivider, BFormGroup, BFormInvalidFeedback, BFormCheckbox, BFormTextarea, BFormFile, BInputGroupAppend, BInputGroup, BAlert, BListGroup, BListGroupItem } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import vSelect from 'vue-select'
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import { hasPermission } from '@/auth/utils'
import { required } from '@validations'
import { postAction, postParticipant, postVerification, getParticipantLetter, getParticipantCertificate, importSiskopatuh, importSeat, downloadParticipantDocuments, downloadLetters, getParticipantDetail, getLetterInformation, getChartDocumentParticipant, updateWithoutBedParticipant } from '@/network/umroh-trip'
import { getFiles, getParticipantSearch, uploadPasPhoto, uploadFile, deleteFile, deleteSpecificFile, uploadReceiveDocument } from '@/network/participant'
import _ from 'lodash'
import Ripple from 'vue-ripple-directive'
import flatPickr from 'vue-flatpickr-component'

export default {
    components: {
        BTable,
        BFormRadio,
        BFormRadioGroup,
        BSidebar,
        BButton,
        BRow,
        BCol,
        BAvatar,
        BForm,
        vSelect,
        BMedia,
        BSpinner,
        BPagination,
        BFormInput,
        BDropdown,
        BDropdownItem,
        BDropdownDivider,
        BFormInvalidFeedback,
        BAlert,
        BFormGroup,
        BImg,
        BFormCheckbox,
        BFormTextarea,
        flatPickr,
        BFormFile,
        BInputGroupAppend,
        BInputGroup,
        BListGroup,
        BListGroupItem,

        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    props: ['participants'],
    watch: { 
        participant: function(_participant) { 
            this.showVerification(_participant)
        }
    },
    data() {
        return {
            required,
            isSubmitModal: false, isDownloading: false,
            verificationData: {}, filePhotoVerifyImg: null,
            verificationForm: { photo: [] }, receiveDocModal: false, passportReceiveAt: null, bukuKuningReceiveAt: null, filePhotoVerified: 0, verificationDataName: '',
            fileKTPVerifyImg: null, fileKTPVerified: null, fileKKVerifyImg: null, fileKKVerified: null, filePassportVerifyImg: null, filePassportVerified: null, fileKVaksinVerified: null, fileAktaVerifyImg: null, fileAktaVerified: null, fileBNikahVerifyImg: null, fileBNikahVerified: null, fileBKuningVerifyImg: null, fileBkuningVerified: null, fileKVaksinVerifyImg: null, fileMCUVerifyImg: null, fileMCUVerified: null, fileSuratKeteranganVerified: null, fileSuratKeteranganVerifyImg: null,
            fileBPJSVerified: null, fileBPJSImages: [], fileBPJSFiles: [], fileSuratKeteranganImages: [], fileSuratKeteranganFiles: [],
            filePassportImages: [], filePassportFiles: [], fileBNikahImages: [], fileBNikahFiles: [], fileBKuningImages: [], fileBKuningFiles: [], fileKVaksinImages: [], fileKVaksinFiles: [],
        }
    },
    methods: {
        showVerification(_participant) {
            this.isSubmitModal = true
            this.verificationForm = { photo: [] }
            this.verificationModal = true
            this.verificationData = _participant
            this.verificationForm.passport_expired_date = _participant.passport_expired_date
            this.verificationForm.no_passport = _participant.no_passport
            this.verificationForm.full_name_vaccine = _participant.full_name_vaccine
            this.verificationForm.passport_notes = _participant.passport_notes
            this.verificationForm.buku_kuning_notes = _participant.buku_kuning_notes
            this.filePhotoVerified = _participant.file_photo_verified

            this.fileKTPVerified = _participant.file_ktp_verified
            this.fileKKVerified = _participant.file_kk_verified
            this.filePassportVerified = _participant.file_passport_verified
            this.fileAktaVerified = _participant.file_akta_verified
            this.fileBNikahVerified = _participant.file_buku_nikah_verified
            this.fileBkuningVerified = _participant.file_buku_kuning_verified
            this.fileKVaksinVerified = _participant.file_kartu_vaksin_verified
            this.fileBPJSVerified = _participant.file_bpjs_verified
            this.fileMCUVerified = _participant.file_mcu_verified
            this.fileSuratKeteranganVerified = _participant.file_surat_keterangan_verified

            getFiles(_participant.participant_id).then(response => {
                this.isSubmitModal = false
                this.filePhotoVerifyImg = response.data.profile_photo
                this.fileKTPVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'KTP')
                this.fileKKVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Kartu Keluarga')
                this.filePassportVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Passport')
                this.fileAktaVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Akta')
                this.fileBNikahVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Buku Nikah')
                this.fileBKuningVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Buku Kuning')
                this.fileKVaksinVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Kartu Vaksin')
                this.fileBPJSVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'BPJS')
                this.fileMCUVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'MCU')
                this.fileSuratKeteranganVerifyImg = this.getPhotoUrlByTitle(response.data.files, 'Surat Keterangan')

                this.filePassportImages = this.getFilesByTitle(response.data.files, 'Passport')
                this.fileBNikahImages = this.getFilesByTitle(response.data.files, 'Buku Nikah')
                this.fileBKuningImages = this.getFilesByTitle(response.data.files, 'Buku Kuning')
                this.fileKVaksinImages = this.getFilesByTitle(response.data.files, 'Kartu Vaksin')
            }).catch(error => {
                this.verificationModal = false;
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        getPhotoUrlByTitle(files, key) {
            let filePathUrl = null
            files.forEach(function (item) {
                if (item.title == key) {
                    filePathUrl = item.file_path_url
                    return
                }
            })
            return filePathUrl
        },
        getFilesByTitle(files, key) {
            let filePathUrl = []
            files.forEach(function (item) {
                if (item.title == key) {
                    filePathUrl.push(item.file_path_url)
                    return
                }
            })
            return filePathUrl
        },
        inputImageRenderer(id) {
            let selectedFiles = this.$refs['refInputEl' + id].files;
            if (!selectedFiles.length) {
                return;
            }

            for (var i = 0; i < selectedFiles.length; i++) {
                this.verificationForm.photo[id] = this.$refs['refInputEl' + id].files[i]
                const file = this.$refs['refInputEl' + id].files[i]
                const reader = new FileReader()
                if (id == 3) {
                    // if(!this.filePassportFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.filePassportFiles.push(this.$refs['refInputEl' + id].files[i])
                }
                if (id == 5) {
                    // if(!this.fileBNikahFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.fileBNikahFiles.push(this.$refs['refInputEl' + id].files[i])
                }
                if (id == 6) {
                    // if(!this.fileBKuningFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.fileBKuningFiles.push(this.$refs['refInputEl' + id].files[i])
                }
                if (id == 7) {
                    // if(!this.fileKVaksinFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.fileKVaksinFiles.push(this.$refs['refInputEl' + id].files[i])
                }
                if (id == 8) {
                    // if(!this.fileKVaksinFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.fileBPJSFiles.push(this.$refs['refInputEl' + id].files[i])
                }
                if (id == 11) {
                    // console.log(this.$refs['refInputEl' + id].files[i])
                    // if(!this.fileSuratKeteranganFiles.includes(this.$refs['refInputEl' + id].files[i]))
                    this.fileSuratKeteranganFiles.push(this.$refs['refInputEl' + id].files[i])
                }

                reader.addEventListener(
                    'load',
                    () => {
                        switch (id) {
                            case 0:
                                this.filePhotoVerifyImg = reader.result
                                break;
                            case 1:
                                this.fileKTPVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileKTPVerifyImg = URL.createObjectURL(file)
                                break;
                            case 2:
                                this.fileKKVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileKKVerifyImg = URL.createObjectURL(file)
                                break;
                            case 3:
                                this.filePassportVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.filePassportVerifyImg = URL.createObjectURL(file)

                                if (!this.filePassportImages.includes(this.filePassportVerifyImg))
                                    this.filePassportImages.push(this.filePassportVerifyImg)
                                break;
                            case 4:
                                this.fileAktaVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileAktaVerifyImg = URL.createObjectURL(file)
                                break;
                            case 5:
                                this.fileBNikahVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileBNikahVerifyImg = URL.createObjectURL(file)

                                if (!this.fileBNikahImages.includes(this.fileBNikahVerifyImg))
                                    this.fileBNikahImages.push(this.fileBNikahVerifyImg)
                                break;
                            case 6:
                                this.fileBKuningVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileBKuningVerifyImg = URL.createObjectURL(file)

                                if (!this.fileBKuningImages.includes(this.fileBKuningVerifyImg))
                                    this.fileBKuningImages.push(this.fileBKuningVerifyImg)
                                break;
                            case 7:
                                this.fileKVaksinVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileKVaksinVerifyImg = URL.createObjectURL(file)

                                if (!this.fileKVaksinImages.includes(this.fileKVaksinVerifyImg))
                                    this.fileKVaksinImages.push(this.fileKVaksinVerifyImg)
                                break;
                            case 8:
                                this.fileBPJSVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileBPJSVerifyImg = URL.createObjectURL(file)

                                if (!this.fileBPJSImages.includes(this.fileBPJSVerifyImg))
                                    this.fileBPJSImages.push(this.fileBPJSVerifyImg)
                                break;
                            case 10:
                                this.fileMCUVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileMCUVerifyImg = URL.createObjectURL(file)
                                break;
                            case 11:
                                this.fileSuratKeteranganVerifyImg = reader.result
                                if (file.type == "application/pdf")
                                    this.fileSuratKeteranganVerifyImg = URL.createObjectURL(file)

                                if (!this.fileSuratKeteranganImages.includes(this.fileSuratKeteranganVerifyImg))
                                    this.fileSuratKeteranganImages.push(this.fileSuratKeteranganVerifyImg)
                                break;
                        }
                    },
                    false,
                )

                if (file) {
                    reader.readAsDataURL(file)
                }
            }
        },
        uploadVerificationFile(idFile, title) {
            this.isSubmitModal = true
            const vForm = new FormData()
            vForm.append('participant_id', this.participant.participant_id)
            vForm.append('title', title)
            if (title != 'Passport' && title != 'Buku Nikah' && title != 'Buku Kuning' && title != 'Kartu Vaksin' && title != 'BPJS' && title != 'Surat Keterangan') {
                vForm.append('file_upload[]', this.verificationForm.photo[idFile])
            }
            if (title == 'Passport') {
                for (const row of this.filePassportFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            if (title == 'Buku Nikah') {
                for (const row of this.fileBNikahFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            if (title == 'Buku Kuning') {
                for (const row of this.fileBKuningFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            if (title == 'Kartu Vaksin') {
                for (const row of this.fileKVaksinFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            if (title == 'BPJS') {
                for (const row of this.fileBPJSFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            if (title == 'Surat Keterangan') {
                for (const row of this.fileSuratKeteranganFiles) {
                    vForm.append('file_upload[]', row)
                }
            }
            uploadFile(vForm).then(response => {
                this.isSubmitModal = false
                this.verificationForm.photo[idFile] = null
                this.filePassportFiles = []
                this.fileBNikahFiles = []
                this.fileBKuningFiles = []
                this.fileKVaksinFiles = []
                this.fileBPJSFiles = []
                this.fileSuratKeteranganFiles = []
                this.$swal({ icon: 'success', title: 'Success', text: `File has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            }).catch(error => {
                this.isSubmitModal = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        uploadPassPhoto() {
            this.isSubmitModal = true
            const vForm = new FormData()
            vForm.append('id', this.participant.participant_id)
            vForm.append('file_upload', this.verificationForm.photo[0])
            uploadPasPhoto(vForm).then(response => {
                this.isSubmitModal = false
                this.verificationForm.photo[0] = null
                this.$swal({ icon: 'success', title: 'Success', text: `Photo has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            }).catch(error => {
                this.isSubmitModal = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        deleteFile(id, title, verification) {
            const vForm = new FormData()
            vForm.append('umrohTripId', this.umrohTripId)
            vForm.append('participant_id', this.verificationData.participant_id)
            vForm.append('title', title)
            vForm.append(verification, 0)
            this.$swal({
                title: `Delete ${title}?`,
                text: "It cannot be reverted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    deleteFile(vForm).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `File has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        switch (id) {
                            case 0:
                                this.filePhotoVerifyImg = null
                                break;
                            case 1:
                                this.fileKTPVerifyImg = null
                                break;
                            case 2:
                                this.fileKKVerifyImg = null
                                break;
                            case 3:
                                this.filePassportVerifyImg = null
                                this.filePassportImages = []
                                break;
                            case 4:
                                this.fileAktaVerifyImg = null
                                break;
                            case 5:
                                this.fileBNikahVerifyImg = null
                                this.fileBNikahImages = []
                                break;
                            case 6:
                                this.fileBKuningVerifyImg = null
                                this.fileBKuningImages = []
                                break;
                            case 7:
                                this.fileKVaksinVerifyImg = null
                                this.fileKVaksinImages = []
                            case 8:
                                this.fileBPJSVerifyImg = null
                                this.fileBPJSImages = []
                                break;
                            case 9:
                                this.fileMCUVerifyImg = null
                                break;
                            case 10:
                                this.fileSuratKeteranganVerifyImg = null
                                this.fileSuratKeteranganImages = []
                                break;
                        }
                    }).catch(error => {
                        this.isSubmitModal = false
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                    })
                }
            })
        },
        deleteSpecificFile(id, title, fileName, index) {
            this.$swal({
                title: `Delete ${title}?`,
                text: "It cannot be reverted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    if (!fileName.includes('base64') && !fileName.includes('blob')) {
                        var reg = /.+?:\/\/.+?(\/.+?)(?:#|\?|$)/;
                        var pathname = reg.exec(fileName)[1].replace(/^\/[\w\d]+\//, '')
                        const vForm = new FormData()
                        vForm.append('participant_id', this.verificationData.participant_id)
                        vForm.append('file_path', pathname)
                        deleteSpecificFile(vForm).then(response => {
                            this.$swal({ icon: 'success', title: 'Success', text: `File has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                            deleted = true
                        }).catch(error => {
                            this.isSubmitModal = false
                            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        })
                    }

                    switch (id) {
                        case 0:
                            break;
                        case 1:
                            break;
                        case 2:
                            break;
                        case 3:
                            this.filePassportFiles.splice(index, 1)
                            this.filePassportImages.splice(index, 1)
                            break;
                        case 4:
                            break;
                        case 5:
                            this.fileBNikahFiles.splice(index, 1)
                            this.fileBNikahImages.splice(index, 1)
                            break;
                        case 6:
                            this.fileBKuningFiles.splice(index, 1)
                            this.fileBKuningImages.splice(index, 1)
                            break;
                        case 7:
                            this.fileKVaksinFiles.splice(index, 1)
                            this.fileKVaksinImages.splice(index, 1)
                            break;
                        case 8:
                            this.fileBPJSFiles.splice(index, 1)
                            this.fileBPJSImages.splice(index, 1)
                            break;
                        case 11:
                            this.fileSuratKeteranganFiles.splice(index, 1)
                            this.fileSuratKeteranganImages.splice(index, 1)
                            break;
                    }
                    if (this.filePassportFiles.length == 0) {
                        this.filePassportFiles = []
                    }
                    if (this.fileBNikahFiles.length == 0) {
                        this.fileBNikahFiles = []
                    }
                    if (this.fileBKuningFiles.length == 0) {
                        this.fileBKuningFiles = []
                    }
                    if (this.fileKVaksinFiles.length == 0) {
                        this.fileKVaksinFiles = []
                    }
                    if (this.fileSuratKeteranganFiles.length == 0) {
                        this.fileSuratKeteranganFiles = []
                    }
                    this.isSubmitModal = false
                    this.filePassportVerifyImg = null
                    this.fileBNikahVerifyImg = null
                    this.fileBKuningVerifyImg = null
                    this.fileKVaksinVerifyImg = null
                    this.fileBPJSVerifyImg = null
                    this.fileSuratKeteranganVerifyImg = null
                }
            })
        },
        downloadFile(type) {
            this.isDownloading = true
            const vForm = {}
            const ids = []
            ids.push(this.participant.participant_id)
            const downloadFiles = []
            downloadFiles.push(type)
            vForm.ids = ids
            vForm.individual = true
            vForm.downloadFiles = downloadFiles
            postAction(vForm).then(response => {
                this.isDownloading = false
                window.location = response.data.downloadLink
            }).catch(error => {
                this.isDownloading = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        downloadFileAll() {
            this.isDownloading = true
            const vForm = {}
            const ids = []
            ids.push(this.participant.participant_id)
            vForm.ids = ids
            vForm.individual = true
            vForm.downloadFiles = ['Pas Photo', 'KTP', 'Kartu Keluarga', 'Passport', 'Akta', 'Buku Nikah', 'Buku Kuning', 'Kartu Vaksin', 'BPJS', 'MCU', 'Surat Keterangan']
            postAction(vForm).then(response => {
                this.isDownloading = false
                window.location = response.data.downloadLink
            }).catch(error => {
                this.isDownloading = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        }
    },
    setup() {
        return {
            hasPermission,
        }
    }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
