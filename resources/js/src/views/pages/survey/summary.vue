<template>
    <div>
        <b-card>
            <div class="pt-1">
                <h3>{{ survey.title }}</h3>
                <h5>Responden: {{ survey.respons }}</h5>
                <div class="row mt-1 align-items-center">
                    <div class="col-sm-8 mb-1">
                        <label>Pilih Keberangkatan</label>
                        <v-select v-model="tripFilter" @input="refetchData" :options="tripOptions" :reduce="(label) => label.id" label="title" multiple />
                    </div>
                    <div class="col-sm-4 mb-1">
                        <label>Pilih Tanggal</label>
                        <div class="input-group">
                            <flat-pickr v-model="dateFilter" @on-change="refetchDataByDate" class="form-control" :config="{ mode: 'range', dateFormat: 'd/m/Y' }" />
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="button" title="Clear" @click="clearDate">
                                    <feather-icon icon="Trash2Icon" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <label>Pilih Pertanyaan</label>
                        <v-select v-model="questionFilter" @input="refetchData" :options="questionOptions" :reduce="(label) => label.id" multiple />
                    </div>
                    <div class="col-sm-4 d-flex justify-content-end">
                        <button class="btn btn-danger mr-1" type="button" @click="generateReport()" :disabled="isButtonLoading">
                            <b-spinner small v-show="isButtonLoading" /> Buat Laporan
                        </button>
                        <button class="btn btn-success" type="button" @click="exportRespons()" :disabled="isButtonLoading">
                            <b-spinner small v-show="isButtonLoading" /> Export Respons
                        </button>
                    </div>
                </div>
            </div>
        </b-card>

        <div class="mt-2">
            <b-overlay :show="loadingPage" rounded="sm">
                <b-row>
                    <template v-for="(question, index) in survey.questions">
                        <b-col :sm="question.section_id ? 12 : 6">
                            <b-card class="wrapper">
                                <div class="pt-1">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <h5>{{ question.question }}</h5>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" @click="exportData(question)" variant="primary" :disabled="isButtonLoading">
                                                <b-spinner small v-show="isButtonLoading" /> Export
                                            </b-button>
                                        </div>
                                    </div>
                                    
                                    <div v-if="listTypes.includes(question.type)">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item" v-for="(answer, index_answer) in question.answers">
                                                <div class="position-relative wrapper-answer">
                                                    {{ answer.answer }}
                                                    <div class="mt-1 d-flex align-items-center justify-content-between" v-if="question.section_id">
                                                        <div class="d-flex align-items-center">
                                                            <v-select class="mr-50 min-width" id="section_id" v-model="answer.section_option" :options="question.section_options" placeholder="Section Option" :clearable="true" />
                                                            <b-button variant="primary" @click="saveSection(answer, question.section_id)">Save</b-button>
                                                        </div>
                                                        <span class="small">{{ answer.trip }}</span>
                                                    </div>
                                                    <div class="editor-answer">
                                                        <span class="text-primary" @click="openEditorAnswer(answer)"><feather-icon icon="EditIcon" /> Edit</span>
                                                    </div>
                                                    <div v-if="editorAnwerModal && editorAnswerForm.id == answer.id" class="mt-1">
                                                        <b-input-group>
                                                            <b-form-input v-model="editorAnswerForm.answer" trim />
                                                            <b-input-group-append>
                                                                <b-button variant="primary" @click="saveAnswer(index, index_answer)"><b-spinner small v-show="isButtonLoading" /> Save</b-button>
                                                            </b-input-group-append>
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="barTypes.includes(question.type)">
                                        <apexchart :options="question.chartOptions" :series="question.chartSeries"/>
                                    </div>

                                    <div v-if="pieTypes.includes(question.type)">
                                        <apexchart :options="question.chartOptions" :series="question.chartSeries"/>

                                        <div v-if="question.answer_others.length > 0" class="py-2">
                                            <h6>Jawaban Lain</h6>
                                            <ul>
                                                <li v-for="(answer, i) in question.answer_others">{{ answer }}</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="py-2">
                                        <div v-if="question.tidakpuas">
                                            <h6>Keterangan Tidak Puas</h6>
                                            <ul>
                                                <li v-for="(answer, i) in question.tidakpuas">
                                                    {{ answer }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div v-if="question.sangattidakpuas">
                                            <h6>Keterangan Sangat Tidak Puas</h6>
                                            <ul>
                                                <li v-for="(answer, i) in question.sangattidakpuas">
                                                    {{ answer }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </b-card>
                        </b-col>
                    </template>
                </b-row>
            </b-overlay>
        </div>
    </div>
</template>

<script>
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox, BFormTextarea, BMedia, BOverlay, BSpinner } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import _ from 'lodash'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { avatarText } from '@core/utils/filter'
import { getDetail, getSummary, getTrips, exportSummaryQuestion, exportSummary, updateAnswerSection, updateAnswer } from '@/network/survey'
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
        BFormCheckboxGroup,
        BFormCheckbox,
        BFormTextarea,
        BMedia,
        BOverlay,
        BSpinner,
        vSelect,
        flatPickr,

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
        refetchDataByDate() {
            if(_.isEmpty(this.dateFilter))
                return

            this.refetchData()
        },
        refetchData() {
            this.loadingPage = true
            getSummary(this.formId, {trip: this.tripFilter, questionId: this.questionFilter, date: this.dateFilter}).then(response => {
                this.loadingPage = false
                this.survey = response.data
            }).catch(error => {
                this.loadingPage = false
                this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            })
        },
        clearDate() {
            this.dateFilter = ""
            this.refetchData()
        },
        exportData(val) {
            this.isButtonLoading = true
            exportSummaryQuestion({ trip: this.tripFilter, questionId: this.questionFilter, date: this.dateFilter, questionId: val.id }).then(response => {
                this.isButtonLoading = false
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
            }).catch(error => {
                this.isButtonLoading = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        saveSection(answer, section_id) {
            this.$swal({
                title: `Simpan perubahan section?`,
                text: "Klasifikasi jawaban berdasarkan section",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.isButtonLoading = true
                    answer.section_id = section_id
                    updateAnswerSection(answer).then(response => {
                        this.$bvToast.toast('Answer has been updated successfully', {
                            title: `Success`,
                            variant: 'primary',
                            toaster: 'b-toaster-top-center',
                            solid: true,
                        })
                        this.isButtonLoading = false
                    })
                    .catch(error => {
                        if (error.response.data.errors) {
                            this.$refs.refObsForm.setErrors(error.response.data.errors)
                        } else {
                            this.$refs.refObsForm.setErrors(error.response.data)
                        }
                        this.isButtonLoading = false
                    })
                }
            })
        },
        generateReport() {
            if(_.isEmpty(this.dateFilter) && _.isEmpty(this.tripFilter)) {
                this.$bvToast.toast('Tanggal atau Keberangkatan harus di pilih', {
                    title: `Warning`,
                    variant: 'danger',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
                return 
            }
            window.open("/spa/survey/"+this.formId+"/report" + '?date='+this.dateFilter+'&trip='+this.tripFilter, '_blank');
        },
        exportRespons() {
            this.isButtonLoading = true
            exportSummary({ form_id: this.formId, trip: this.tripFilter, date: this.dateFilter }).then(response => {
                this.isButtonLoading = false
                this.$swal({ icon: 'success', title: `Success`, text: `System is Now Downloading File and When it's Finished, System will Send Download Link to email`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            }).catch(error => {
                this.isButtonLoading = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        openEditorAnswer(value) {
            this.editorAnwerModal = false
            this.editorAnswerForm = {}
            this.editorAnswerForm.id = value.id
            this.editorAnswerForm.answer = value.answer
            this.editorAnwerModal = true
        },
        saveAnswer(index, index_answer) {
            this.$swal({
                title: `Ubah Jawaban`,
                text: "Simpan Perubahan Jawaban?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.isButtonLoading = true
                    updateAnswer(this.editorAnswerForm).then(response => {
                        this.$bvToast.toast('Answer has been updated successfully', {
                            title: `Success`,
                            variant: 'primary',
                            toaster: 'b-toaster-top-center',
                            solid: true,
                        })
                        this.survey.questions[index].answers[index_answer].answer = this.editorAnswerForm.answer
                        this.isButtonLoading = false
                        this.editorAnwerModal = false
                    })
                    .catch(error => {
                        this.$bvToast.toast(error, {
                            title: `Error`,
                            variant: 'danger',
                            toaster: 'b-toaster-top-center',
                            solid: true,
                        })
                        this.isButtonLoading = false
                        this.editorAnwerModal = false
                    })
                }
            })
        }
    },
    data() {
        const survey = {}
        const tripOptions = []
        const questionOptions = []

        const formId = parseInt(this.$route.params.id) || 0
        if (formId == 0) this.$router.back()
        
        getSummary(formId).then(response => {
            this.survey = response.data
            this.questionOptions = []
            this.survey.questions.forEach(element => {
                this.questionOptions.push({label:element.question,id:element.id})
            });
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        getTrips({id:formId}).then(response => {
            this.tripOptions = response.data
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        var listTypes = ['text','textarea','date'];
        var barTypes = ['checkbox'];
        var pieTypes = ['radio','option','scale'];

        return {
            isButtonLoading: false,
            loadingPage: false,
            formId, survey, required, numeric,
            listTypes, barTypes, pieTypes,
            tripOptions, tripFilter: "", dateFilter: "", questionFilter: "", questionOptions,
            editorAnwerModal: false, editorAnswerForm: {}
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import "~@resources/scss/vue/libs/vue-flatpicker.scss";

.wrapper {
    max-height: 600px;
    height: 600px;
    overflow: auto;
}
span.small {
    display: block;
    font-size: 10px;
    margin-top: 6px;
    float: right;
    color: #008ffb;
}
.min-width {
    min-width: 160px;
}
.wrapper-answer:hover .editor-answer {
    display: inline;
}
.editor-answer {
    margin-left: 12px;
    display: none;
    cursor: pointer;
}
</style>
