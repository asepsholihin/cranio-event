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

                    <b-media class="mb-2">
                        <template #aside>
                            <b-avatar :src="formData.image_url" :text="avatarText('NA')" size="90px" rounded />
                        </template>
                        <div class="d-flex flex-wrap">
                            <b-button v-if="hasPermission('survey-add-or-edit')" variant="primary"
                                @click="$refs.refInputEl.click()">
                                <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none"
                                    @input="inputImageRenderer">
                                <span class="d-none d-sm-inline">Change</span>
                                <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                            </b-button>
                        </div>
                        <div class="mt-1 text-muted">Recommended Size 800 x 340 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                    </b-media>

                    <b-row>
                        <b-col cols="12" md="6">
                            <!-- Title -->
                            <validation-provider #default="{ errors }" name="Department" vid="department_id" rules="required">
                                <b-form-group label="Department" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.department_id" :options="departmentOptions" :clearable="false"
                                    :reduce="label => label.value" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Title -->
                            <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                                <b-form-group label="Title">
                                    <b-form-input id="title" v-model="formData.title"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <validation-provider #default="{ errors }" name="Title in Report" vid="title_in_report" rules="required">
                                <b-form-group label="Title in Report" label-for="title_in_report" :state="errors.length > 0 ? false : null">
                                    <b-form-input id="title_in_report" v-model="formData.title_in_report"
                                        :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Slug -->
                            <validation-provider #default="{ errors }" name="Slug" vid="slug">
                                <b-form-group label="Slug" description="Leave this form for auto generate">
                                    <b-form-input id="slug" v-model="formData.slug"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Description -->
                        <b-col cols="12" md="12">
                            <validation-provider #default="{ errors }" name="Description" vid="description">
                                <b-form-group label="Description">
                                    <ckeditor :editor="editor" v-model="formData.description" :config="editorConfig"></ckeditor>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <div class="d-flex">
                        <!-- Status -->
                        <validation-provider #default="{ errors }" name="Status" vid="status">
                            <b-form-group label="Status" class="mr-2">
                                <b-form-checkbox v-model="formData.status" name="check-button" switch />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <!-- Umroh / Haji Trip Required -->
                        <validation-provider #default="{ errors }" name="Umroh / Haji Trip Require" vid="required_umroh_trip">
                            <b-form-group label="Umroh / Haji Trip Require">
                                <b-form-checkbox v-model="formData.required_umroh_trip" name="check-button" switch />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </div>
                    <template v-if="formData.required_umroh_trip">
                        <validation-provider #default="{ errors }" name="Package Name" vid="package">
                            <b-form-group label="Package (Option, Biarkan kosong jika ingin semua paket dimunculkan)" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.packages" multiple
                                    :options="packageNameOptions" :clearable="false"
                                    :reduce="label => label.name" label="name" />
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </template>

                    <hr>
                    <h3 class="mb-1">Question List</h3>
                    <b-list-group>
                        <draggable v-model="formData.questions">
                            <b-list-group-item v-for="(item, index) in formData.questions" :key="index">
                                <div>
                                    <b-row>
                                        <!-- Description -->
                                        <b-col cols="12" md="12">
                                            <div v-if="index > 0 || item.id">
                                                <b-button class="float-right p-0 text-danger" variant="link" @click="removeQuestionItem(index, item)">
                                                    <feather-icon icon="XCircleIcon" class="mr-25" />
                                                </b-button>
                                            </div>
                                        </b-col>
                                        <b-col sm="6">
                                            <validation-provider #default="{ errors }" name="Question" vid="question" rules="required">
                                                <b-form-group label="Question">
                                                    <b-form-textarea id="question" v-model="formData.questions[index].question"
                                                        :state="errors.length > 0 ? false : null" trim rows="1" max-rows="6"></b-form-textarea>

                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                        <b-col sm="3">
                                            <b-form-checkbox v-model="formData.questions[index].required" name="check-button" switch>
                                                Required
                                            </b-form-checkbox>

                                            <validation-provider #default="{ errors }" name="Type" vid="type" rules="required">
                                                <b-form-group label="Type" label-for="type" :state="errors.length > 0 ? false : null">
                                                    <v-select id="type" v-model="formData.questions[index].type" :options="typeOptions" @input="checkQuestion(index)"
                                                        :clearable="true" :reduce="(label) => label.value" />
                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                            
                                            <validation-provider #default="{ errors }" name="Section" vid="section_id">
                                                <b-form-group label="Section (Optional)" label-for="section_id" :state="errors.length > 0 ? false : null">
                                                    <v-select id="section_id" v-model="formData.questions[index].section_id" :options="sectionOptions"
                                                        :clearable="true" :reduce="(label) => label.id" label="name" />
                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                        <b-col sm="3">
                                            <b-form-checkbox v-model="formData.questions[index].view_in_report" name="check-button" switch>
                                                View in Report?
                                            </b-form-checkbox>

                                            <div v-if="formData.questions[index].view_in_report">
                                                <validation-provider #default="{ errors }" name="Title in Report" vid="title_in_report" rules="required">
                                                    <b-form-group label="Title in Report" label-for="title_in_report" :state="errors.length > 0 ? false : null">
                                                        <b-form-input id="title_in_report" v-model="formData.questions[index].title_in_report"
                                                            :state="errors.length > 0 ? false : null" trim />
                                                        <b-form-invalid-feedback>
                                                            {{ errors[0] }}
                                                        </b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>

                                                <validation-provider #default="{ errors }" name="Model in Report" vid="model_in_report" rules="required">
                                                    <b-form-group label="Model in Report" label-for="model_in_report" :state="errors.length > 0 ? false : null">
                                                        <v-select id="model_in_report" v-model="formData.questions[index].model_in_report" :options="modelOptions"
                                                            :clearable="true" :reduce="(label) => label.value" />
                                                        <b-form-invalid-feedback>
                                                            {{ errors[0] }}
                                                        </b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>
                                            </div>
                                        </b-col>
                                    </b-row>
                                    <b-row v-if="formData.questions[index].type != 'scale'">
                                        <b-col sm="6">
                                            <div class="d-flex align-items-center mb-1" v-for="(value, i) in formData.questions[index].option_value">
                                                <feather-icon icon="ListIcon" size="20" class="mr-1" />
                                                <input type="text" class="form-control mr-1" v-model="formData.questions[index].option_value[i].value" placeholder="Option value...">
                                                <b-button class="float-right p-0 text-danger" variant="link" @click="removeQuestionOptionItem(index, i, value)">
                                                    <feather-icon icon="Trash2Icon" class="mr-25" />
                                                </b-button>
                                                <b-button variant="default" @click="addQuestionOptionItem(index)">
                                                    <feather-icon icon="PlusIcon" class="mr-25" />
                                                </b-button>
                                            </div>
                                            <template v-if="formData.questions[index].type == 'checkbox' || formData.questions[index].type == 'radio'">
                                                <div class="d-flex align-items-center mb-1" v-if="formData.questions[index].has_other">
                                                    <feather-icon icon="ListIcon" size="20" class="mr-1" />
                                                    <input type="text" class="form-control mr-1" placeholder="Others" readonly>
                                                    <b-button class="float-right p-0 text-danger" variant="link" @click="removeQuestionOptionOther(index)">
                                                        <feather-icon icon="Trash2Icon" class="mr-25" />
                                                    </b-button>
                                                </div>
                                                <b-button variant="default" @click="addQuestionOptionOther(index)" v-if="!formData.questions[index].has_other">
                                                    <feather-icon icon="PlusIcon" class="mr-25" /> Add Others
                                                </b-button>
                                            </template>
                                        </b-col>
                                    </b-row>
                                    <div v-if="formData.questions[index].type == 'scale'" class="d-flex align-items-center mb-1">
                                        <div class="mr-1">
                                            <input type="text" class="form-control" v-model="formData.questions[index].label_scale_bottom" placeholder="Label (Optional)">
                                        </div>
                                        <div class="wrap-scale-number mr-1">
                                            <select class="form-control" v-model="formData.questions[index].scale_bottom">
                                                <option>0</option>
                                                <option>1</option>
                                            </select>
                                        </div>
                                        <div class="wrap-scale-number text-center mr-1">
                                            to
                                        </div>
                                        <div class="wrap-scale-number mr-1">
                                            <select class="form-control" v-model="formData.questions[index].scale_top">
                                                <option>0</option>
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                                <option>6</option>
                                                <option>7</option>
                                                <option>8</option>
                                                <option>9</option>
                                                <option>10</option>
                                            </select>
                                        </div>
                                        <div>
                                            <input type="text" class="form-control" v-model="formData.questions[index].label_scale_top" placeholder="Label (Optional)">
                                        </div>
                                    </div>
                                </div>
                            </b-list-group-item>
                        </draggable>
                    </b-list-group>
                    <div class="d-flex justify-content-end">
                        <b-button variant="default" @click="addQuestion()">
                            <feather-icon icon="PlusIcon" class="mr-25" />
                            <span>Add More Question</span>
                        </b-button>
                    </div>

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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox, BFormTextarea, BMedia, BListGroup, BListGroupItem } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { getDetail, postData } from '@/network/survey'
import { getFormSectionSearch } from '@/network/form-section'
import { hasPermission } from '@/auth/utils'
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from "ckeditor5-build-classic-image";
import draggable from 'vuedraggable'
import { getPackageSearch } from '@/network/package'

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
        BFormCheckboxGroup,
        BFormCheckbox,
        BFormTextarea,
        BMedia,
        BListGroup, 
        BListGroupItem,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
        ckeditor: CKEditor.component,
        draggable
    },
    directives: {
        Ripple,
    },
    setup() {
        // text,textarea,checkbox,radio,option,file,date,email,number,date,time
        const typeOptions = [
            { label: 'Short Text', value: 'text' }, 
            { label: 'Paragraph', value: 'textarea' }, 
            { label: 'Multiple Choice', value: 'radio' }, 
            { label: 'Checkbox', value: 'checkbox' }, 
            { label: 'Dropdown', value: 'option' }, 
            { label: 'Linear Scale', value: 'scale' },
            { label: 'Date', value: 'date' },
        ]
        const departmentOptions = [
            { label: 'Directors', value: 1 }, 
            { label: 'Management', value: 2 }, 
            { label: 'Sales', value: 3 }, 
            { label: 'Document', value: 4 }, 
            { label: 'Equipment', value: 5 }, 
            { label: 'Handling', value: 6 }, 
            { label: 'Finance', value: 7 }, 
            { label: 'Head Branch', value: 8 }, 
            { label: 'Sales Manager', value: 9 }, 
        ]
        const modelOptions = [
            { label: 'Vote', value: 'vote' }, 
            { label: 'Bar Chart', value: 'chart' }, 
            { label: 'Pie Chart', value: 'pie_chart' }, 
            { label: 'Text List', value: 'list' }, 
        ]

        return { hasPermission, avatarText, typeOptions, departmentOptions, modelOptions }
    },
    methods: {
        refetchData() {
            getDetail(this.formId).then(response => {
                this.formData = response.data
                this.formData['status'] = (response.data['status'] == 1) ? true : false
                this.formData['packages'] = response.data['packages'].split(',')
            }).catch(error => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors)
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data)
                }
            })
        },
        resetForm() {
            this.formData = { status:true, questions: [{option_value:[]}] }
        },
        addQuestion() {
            this.formData.questions.push({option_value:[]})
        },
		removeQuestionItem(index, item) {
			if(item.id != undefined) {
				this.deleteQuestionOptionItem(index, item)
			} else {
				this.formData.questions.splice(index, 1)
			}
		},
        checkQuestion(index) {
            var types = ['checkbox','radio','option']
            if(types.includes(this.formData.questions[index].type)) {
                this.formData.questions[index].option_value.push({value:''})
            } else {
                this.formData.questions[index].option_value = []
            }
        },
        addQuestionOptionItem(index) {
            this.formData.questions[index].option_value.push({value:''})
        },
		removeQuestionOptionItem(index, i, item) {
            this.formData.questions[index].option_value.splice(i, 1)
		},
        addQuestionOptionOther(index) {
            this.formData.questions[index].has_other = true
        },
        removeQuestionOptionOther(index) {
            this.formData.questions[index].has_other = false
        },
        deleteQuestionOptionItem(index, item) {
            this.$swal({
                title: `Delete ${item.question}?`,
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
                    this.questionDeletedIds.push(item.id)
				    this.formData.questions.splice(index, 1)
                }
            })
        },
        inputImageRenderer() {
            this.formData.image = this.$refs.refInputEl.files[0]
            const file = this.$refs.refInputEl.files[0]
            const reader = new FileReader()

            reader.addEventListener(
                'load',
                () => {
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
                    if (key == 'image_url')
                        continue
                    if (this.formData[key] != null && key != 'questions')
                        vForm.append(key, this.formData[key])
                    if (key == 'questions')
                        vForm.append('questions', JSON.stringify(this.formData[key]))
                }
                vForm.append('question_deleted_ids', JSON.stringify(this.questionDeletedIds))
                
                this.loadingSubmit = true
                postData(vForm).then(response => {
                    this.$bvToast.toast('Survey has been updated successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })

                    this.refetchData()
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
            status:true,
            questions: [{option_value:[]}]
        }

        const formId = parseInt(this.$route.params.id) || 0
        if (formId == 0) this.$router.back()
        
        getDetail(formId).then(response => {
            this.formData = response.data
            this.formData['status'] = (response.data['status'] == 1) ? true : false
            this.formData['packages'] = response.data['packages'].split(',')
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        const sectionOptions = [];
        getFormSectionSearch().then(res => {
            this.sectionOptions = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        const packageNameOptions = []
        getPackageSearch().then(response => {
            this.packageNameOptions = response.data;
        }).catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        var fonSizes = [
            9,10,11,12,13,
            'default',
            17,18,19,20,24,26,28,30,36,40,50,60,70,80
        ]

        return {
            formId, formData, required, numeric,
            editor: ClassicEditor,
            editorConfig: {
                fontFamily: {
                    options: [
                        'default',
                        'Arial',
                        'Courier New',
                        'Georgia',
                        'Lucida Sans Unicode',
                        'Tahoma',
                        'Times New Roman',
                        'Trebuchet MS',
                        'Verdana',
                        'Gotham',
                        'Montserrat, Helvetica, Arial, serif',
                        'Naskh Regular',
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: fonSizes,
                    supportAllValues: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    ]
                },
                toolbar: {
                    items: [
                        'heading',
                        '|', 
                        "fontFamily",
                        "fontSize",
                        'lineHeight',
                        "fontColor",
                        "bold",
                        "italic",
                        "underline",
                        "alignment",
                        "|",
                        "bulletedList",
                        "numberedList",
                        "|",
                        "indent",
                        "outdent",
                        "|",
                        "link",
                        "imageUpload",
                        "insertTable", "tableColumn", "tableRow", "mergeTableCells",
                        "mediaEmbed",
                        "blockQuote",
                        "|",
                        "removeFormat"
                    ]
                }
            },
            questionDeletedIds: [],
            loadingSubmit: false,
            sectionOptions,
            packageNameOptions
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
