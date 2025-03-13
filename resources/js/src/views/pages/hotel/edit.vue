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
                            <!-- City -->
                            <validation-provider #default="{ errors }" name="Select City" vid="city_id" rules="required">
                                <b-form-group label="Select City" :state="errors.length > 0 ? false : null">
                                <v-select v-model="formData.city_id" :options="cityOptions" :clearable="false"
                                :reduce="label => label.id" label="name" />
                                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Hotel Name -->
                            <validation-provider #default="{ errors }" name="Hotel Name" vid="name" rules="required">
                                <b-form-group label="Hotel Name">
                                <b-form-input id="name" v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        
                            <!-- Description -->
                            <validation-provider #default="{ errors }" name="Description" vid="description">
                                <b-form-group label="Description">
                                    <b-form-textarea id="description" v-model="formData.description"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"
                                    ></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Address -->
                            <validation-provider #default="{ errors }" name="Address" vid="address">
                                <b-form-group label="Address">
                                    <b-form-textarea id="address" v-model="formData.address"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"
                                    ></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Google Map Link -->
                            <validation-provider #default="{ errors }" name="Google Map Link" vid="map_url">
                                <b-form-group label="Google Map Link">
                                    <b-form-input id="map_url" v-model="formData.map_url" :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Star -->
                        <b-col cols="6" md="3">
                            <validation-provider #default="{ errors }" name="Hotel Star" vid="star" rules="numeric|max_value:5|min_value:0">
                                <b-form-group label="Hotel Star">
                                <b-form-input type="number" id="star" v-model="formData.star" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <!-- Email -->
                        <b-col cols="6" md="3">
                            <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                                <b-form-group label="Email">
                                <b-form-input id="email" v-model="formData.email" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="6" md="3">
                            <!-- PIC Name -->
                            <validation-provider #default="{ errors }" name="PIC Name" vid="pic_name">
                                <b-form-group label="PIC Name">
                                <b-form-input id="pic_name" v-model="formData.pic_name" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="6" md="3">
                            <!-- PIC Phone -->
                            <validation-provider #default="{ errors }" name="PIC Phone" vid="pic_phone" rules="numeric">
                                <b-form-group label="PIC Phone">
                                <b-form-input type="number" id="pic_phone" v-model="formData.pic_phone" :state="errors.length > 0 ? false : null"
                                trim />

                                <b-form-invalid-feedback>
                                {{ errors[0] }}
                                </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <hr>
                    <h4 class="mb-1">Information Detail</h4>
                    <b-list-group>
                        <draggable v-model="formData.informations">
                            <b-list-group-item v-for="(item, index) in formData.informations" :key="index" handle=".handle-info">
                                <div class="handle handle-info"><feather-icon icon="GridIcon" /></div>
                                <div>
                                    <div style="position:absolute;right:0" v-if="index > 0 || item.id">
                                        <b-button class="text-danger" variant="link" @click="removeItem(index, item)">
                                            <feather-icon icon="XCircleIcon" size="18" />
                                        </b-button>
                                    </div>
                                    <b-row>
                                        <b-col cols="12" md="3">
                                            <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                                                <b-form-group label="Title">
                                                    <b-form-input id="title" v-model="formData.informations[index].title"
                                                        :state="errors.length > 0 ? false : null" trim rows="1" max-rows="6"></b-form-input>

                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                        <b-col cols="12" md="3">
                                            <validation-provider #default="{ errors }" name="Type" vid="type" rules="required">
                                                <b-form-group label="Type" label-for="type" :state="errors.length > 0 ? false : null">
                                                    <v-select id="type" v-model="formData.informations[index].type" :options="typeOptions"
                                                        :clearable="true" :reduce="(label) => label.value" />
                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col md="6" v-if="formData.informations[index].type == 'text'">
                                            <validation-provider #default="{ errors }" name="Content" vid="content">
                                                <b-form-group label="Content">
                                                    <b-form-textarea id="content" v-model="formData.informations[index].content"
                                                        :state="errors.length > 0 ? false : null" trim rows="1" max-rows="6"></b-form-textarea>

                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                        <b-col md="6" v-if="formData.informations[index].type == 'html'">
                                            <validation-provider #default="{ errors }" name="Content" vid="content">
                                                <b-form-group label="Content">
                                                    <ckeditor :editor="editor" v-model="formData.informations[index].content" :config="editorConfig"></ckeditor>
                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>
                                    </b-row>
                                </div>
                            </b-list-group-item>
                        </draggable>
                    </b-list-group>
                    <div class="d-flex justify-content-end">
                        <b-button variant="default" @click="addItem()">
                            <feather-icon icon="PlusIcon" class="mr-25" />
                            <span>Add More Information</span>
                        </b-button>
                    </div>

                    <hr>
                    <h4 class="mb-1">Hotel Images</h4>
                    <b-list-group>
                        <draggable v-model="formData.images" handle=".handle-image">
                            <b-list-group-item v-for="(item, index) in formData.images" :key="index">
                                <div class="handle handle-image"><feather-icon icon="GridIcon" /></div>
                                <div style="position:absolute;right:0" v-if="index > 0 || item.id">
                                    <b-button class="text-danger" variant="link" @click="removeImage(index, item)">
                                        <feather-icon icon="XCircleIcon" size="18" />
                                    </b-button>
                                </div>
                                <div>
                                    <b-row>
                                        <b-col md="6">
                                            <validation-provider #default="{ errors }" name="Title" vid="title">
                                                <b-form-group label="Title">
                                                    <b-form-input id="title" v-model="formData.images[index].title"
                                                        :state="errors.length > 0 ? false : null" trim rows="1" max-rows="6"></b-form-input>

                                                    <b-form-invalid-feedback>
                                                        {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                            
                                            <b-button variant="primary" @click="fileImage(index)">
                                                <input ref="refInputEl" type="file" accept="image/jpeg, image/png" class="d-none"
                                                    @input="inputImageRenderer(index)">
                                                <span>Add Image</span>
                                            </b-button>
                                            <p>Recommended Size 750 x 750 px (Format : PNG, JPG) Max Size: 1.5MB</p>
                                            <div class="my-1" v-if="formData.images[index].url">
                                                <b-img :src="formData.images[index].url" thumbnail fluid />
                                            </div>
                                        </b-col>
                                    </b-row>
                                </div>
                            </b-list-group-item>
                        </draggable>
                    </b-list-group>
                    <div class="d-flex justify-content-end">
                        <b-button variant="default" @click="addImage()">
                            <feather-icon icon="PlusIcon" class="mr-25" />
                            <span>Add More Images</span>
                        </b-button>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            type="submit" v-if="hasPermission('land-arrangement-add-or-edit')" @click.prevent="onSubmit" :disabled="loadingSubmit">
                            <b-spinner small v-show="loadingSubmit" /> Save
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
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BListGroup, BListGroupItem, BImg, BSpinner, BFormRadioGroup, BFormRadio } from 'bootstrap-vue'
import vSelect from 'vue-select'
import { getDetail } from '@/network/hotel'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import { postData } from '@/network/hotel'
import { hasPermission } from '@/auth/utils'
import { getCitySearch } from '@/network/city'
import CKEditor from '@ckeditor/ckeditor5-vue2'
import ClassicEditor from "ckeditor5-build-classic-image"
import draggable from 'vuedraggable'

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
        BListGroup, 
        BListGroupItem,
        BImg,
        BSpinner,
        BFormRadioGroup,
        BFormRadio,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
        vSelect,
        ckeditor: CKEditor.component,
        draggable
    },
    directives: {
        Ripple,
    },
    setup() {
        const typeOptions = [
            { label: 'Text', value: 'text' }, 
            { label: 'Html', value: 'html' }
        ]

        return { hasPermission, typeOptions}
    },
    methods: {
        fileImage(index) {
            this.$refs.refInputEl[index].click()
        },
        addItem() {
            this.formData.informations.push({ title:"", content:"", type:"text" })
        },
		removeItem(index, item) {
			this.formData.informations.splice(index, 1)
		},
        addImage() {
            this.formData.images.push({ title:"", file:null, url:"" })
        },
		removeImage(index, item) {
			this.formData.images.splice(index, 1)
		},
        inputImageRenderer(index) {
            const file = this.$refs.refInputEl[index].files[0]
            const reader = new FileReader()

            if (file.size > 1024 * 1024) {
                this.$bvToast.toast('Image harus kurang dari 1MB', {
                    title: `Error`,
                    variant: 'warning',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
                return;
            }

            reader.addEventListener(
                'load',
                () => {
                    this.formData.images[index].url = reader.result
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
                    if (this.formData[key] != null && key != 'informations' && key != 'images')
                        vForm.append(key, this.formData[key])
                    if (key == 'informations')
                        vForm.append('informations', JSON.stringify(this.formData[key]))
                    if (key == 'images')
                        vForm.append('images', JSON.stringify(this.formData[key]))
                }
                this.loadingSubmit = true
                postData(vForm).then(response => {
                    this.$bvToast.toast('Hotel has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
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
        const cityOptions =[]
        getCitySearch({ q: '' })
        .then(res => {
            this.cityOptions = res.data
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
        
        const formData = { 
            informations: [{ title:"", content:"", type:"text" }],
            images: [{ title:"", url:"" }]
        }
        var fonSizes = [
            9,10,11,12,13,
            'default',
            17,18,19,20,24,26,28,30,36,40,50,60,70,80
        ]

        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['informations'] = JSON.parse(response.data['informations'])
            this.formData['images'] = JSON.parse(response.data['images'])
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        return {
            formData, required, numeric, cityOptions,
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
            loadingSubmit: false
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
.ck-content.ck-editor__editable {
    min-height: 300px;
}
.handle {
  padding-bottom: 12px;
}
</style>
