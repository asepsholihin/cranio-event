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

.seo-wrapper {
    min-height: 400px;
    overflow: auto;
}
</style>

<template>
    <b-card>
        <p class="pt-1">
            <!-- Form -->
            <b-form>
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
                            <b-button v-if="hasPermission('article-add-or-edit')" variant="primary"
                                @click="$refs.refInputEl.click()">
                                <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none"
                                    @input="inputImageRenderer">
                                <span class="d-none d-sm-inline">Change</span>
                                <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                            </b-button>
                        </div>
                        <div class="mt-1 text-muted">Recommended Size 1080 x 780 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB</div>
                    </b-media>

                    <b-row>
                        <b-col cols="12" md="3">
                            <!-- Show in Page -->
                            <validation-provider #default="{ errors }" vid="show_in_page" name="Show in Page">
                                <b-form-group label="Show in Page" label-for="show_in_page">
                                    <v-select id="show_in_page" v-model="formData.show_in_page" :options="pageOptions"
                                        :clearable="true" :reduce="label => label.value" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <template v-if="formData.show_in_page == 'participant-room'">
                            <b-col cols="12" md="3">
                                <!-- Type -->
                                <validation-provider #default="{ errors }" vid="type" name="Type">
                                    <b-form-group label="Type" label-for="type">
                                        <v-select id="type" v-model="formData.type" :options="typeOptions"
                                            :clearable="true" :reduce="label => label.value" />
                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </b-col>
                        </template>
                    </b-row>
                    <b-row>
                        <b-col cols="12" md="6">
                            <template v-if="formData.show_in_page == 'participant-room'">
                            
                                <!-- Asatidz -->
                                <validation-provider #default="{ errors }" vid="asatidz_id" name="Asatidz">
                                    <b-form-group label="Asatidz" label-for="asatidz_id">
                                        <v-select id="asatidz_id" v-model="formData.asatidz_id" :options="asatidzs"
                                            :clearable="true" :reduce="(label) => label.id" label="name" />
                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            
                                <!-- Youtube Link -->
                                <validation-provider #default="{ errors }" name="outube Link" vid="youtube_link">
                                    <b-form-group label="Youtube Link">
                                        <b-form-input id="youtube_link" v-model="formData.youtube_link"
                                            :state="errors.length > 0 ? false : null" trim />

                                        <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                                
                            </template>

                            <!-- Category -->
                            <validation-provider #default="{ errors }" vid="category_id" name="Category">
                                <b-form-group label="Category" label-for="category_id">
                                    <v-select id="category_id" v-model="formData.category_id" :options="categories"
                                        :clearable="true" :reduce="(label) => label.id" label="name" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Title -->
                            <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
                                <b-form-group label="Title">
                                    <b-form-input id="title" v-model="formData.title" @input="seoChecker"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Slug -->
                            <validation-provider #default="{ errors }" name="Slug" vid="slug">
                                <b-form-group label="Slug">
                                    <b-form-input id="slug" v-model="formData.slug" @input="seoChecker"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Keywords -->
                            <validation-provider #default="{ errors }" name="Keywords" vid="keywords" rules="required">
                                <b-form-group label="Keywords">
                                    <b-form-input id="keywords" v-model="formData.keywords" @input="seoChecker"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Meta Title -->
                            <validation-provider #default="{ errors }" name="Meta Title" vid="meta_title">
                                <b-form-group label="Meta Title">
                                    <b-form-input id="meta_title" v-model="formData.meta_title" @input="seoChecker"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>

                            <!-- Meta Description -->
                            <validation-provider #default="{ errors }" name="Meta Description" vid="meta_description">
                                <b-form-group label="Meta Description" description="Maximum : 300 Words">
                                    <b-form-textarea id="meta_description" v-model="formData.meta_description" @input="seoChecker"
                                        name="meta_description"
                                        :state="errors.length > 0 ? false : null" rows="3" max-rows="6"></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Canonical -->
                            <validation-provider #default="{ errors }" name="Canonical" vid="canonical">
                                <b-form-group label="Canonical">
                                    <b-form-input id="canonical" v-model="formData.canonical"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                            
                            <!-- Written By -->
                            <validation-provider #default="{ errors }" vid="written_by_id" name="Written By">
                                <b-form-group label="Written By" label-for="written_by_id">
                                    <v-select id="written_by_id" v-model="formData.written_by_id" :options="asatidzs"
                                        :clearable="true" :reduce="(label) => label.id" label="name" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="6">
                            <div v-if="loadingSEO" style="position: absolute;top: -50px;left: 25px;">
                                <b-spinner variant="primary" label="Spinning"></b-spinner>
                            </div>
                            <div v-if="seoAssessments.length" class="seo-wrapper">
                                <div class="mb-3 col-md-12">
                                    <h3>SEO assessments</h3>
                                    <ul class="bulleted-list">
                                        <li class="col-md-12 pb-1 pl-0 score seo-score-icon seo-score-text"
                                            :class="item.rating" v-for="item in seoAssessments" v-html="item.text"></li>
                                    </ul>
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="12" md="6">
                            <!-- Reviewed -->
                            <validation-provider #default="{ errors }" name="Reviewed By" vid="reviewed_by">
                                <b-form-group label="Reviewed By">
                                    <b-form-input id="reviewed_by" v-model="formData.reviewed_by"
                                        :state="errors.length > 0 ? false : null" trim />

                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Content -->
                        <b-col cols="12" md="12">
                            <validation-provider #default="{ errors }" name="Content" vid="content">
                                <b-form-group label="Content">
                                    <ckeditor :editor="editor" v-model="formData.content" :config="editorConfig" @input="seoChecker"></ckeditor>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <!-- Robot Meta -->
                    <validation-provider #default="{ errors }" name="Robots Meta" vid="meta_index">
                        <b-form-group label="Robots Meta">
                            <b-form-checkbox-group v-model="meta_index" :options="robotsMeta" value-field="item"
                                text-field="name" />

                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Views Counter Visibility -->
                    <validation-provider #default="{ errors }" name="Views Counter Visibility" vid="views_count_visible">
                        <b-form-group label="Views Counter Visibility">
                            <b-form-checkbox v-model="formData.views_count_visible" name="check-button" switch />

                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" @click.prevent="onSaveAs('draft')"
                            variant="secondary" class="mr-2" v-if="hasPermission('article-add-or-edit')" :disabled="loadingSubmit">
                            <b-spinner v-show="loadingSubmit" small></b-spinner>
                            Save as Draft
                        </b-button>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" @click.prevent="onSaveAs('publish')"
                            value="publish" variant="primary" class="mr-2" v-if="hasPermission('article-add-or-edit')" :disabled="loadingSubmit">
                            <b-spinner v-show="loadingSubmit" small></b-spinner>
                            Save &amp; Publish
                        </b-button>
                        <b-button @click.prevent="onSaveAs('preview')" v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            type="button" class="mr-2" variant="outline-primary" :disabled="loadingSubmit">
                            <b-spinner v-show="loadingSubmit" small></b-spinner>
                            Preview Article
                        </b-button>
                        <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button"
                            variant="outline-secondary">
                            Back
                        </b-button>
                    </div>

                    <b-row class="mt-4">
                        <b-col cols="6">
                            <div v-if="contentAssessments.length" class="seo-wrapper">
                                <div class="mb-3 col-md-12">
                                    <h3>Content assessments</h3>
                                    <ul class="bulleted-list">
                                        <li class="col-md-12 pb-1 pl-0 score seo-score-icon seo-score-text" :class="item.rating"
                                            v-for="item in contentAssessments" v-html="item.text"></li>
                                    </ul>
                                </div>
                            </div>
                        </b-col>
                    </b-row>

                </validation-observer>
            </b-form>
        </p>

    </b-card>
</template>

<script>
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox, BFormTextarea, BMedia, BSpinner } from 'bootstrap-vue'
import { getDetail } from '@/network/article'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { postData } from '@/network/article'
import { getCategorySearch } from "@/network/article-category";
import { getAsatidzSearch } from "@/network/asatidz";
import { hasPermission } from '@/auth/utils'
import { Paper, ContentAssessor, Researcher, SnippetPreview } from "yoastseo";
import SEOAssessor from 'yoastseo/src/seoAssessor';
import Jed from 'jed';
import Presenter from '../../../libs/presenter'
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from "ckeditor5-build-classic-image";
import _debounce from 'lodash/debounce'

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

        // Form Validation
        ValidationProvider,
        ValidationObserver,
        Paper,
        ContentAssessor,
        Researcher,
        SnippetPreview,
        SEOAssessor,
        Presenter,
        Jed,
        ckeditor: CKEditor.component,
        BSpinner
    },
    directives: {
        Ripple,
    },
    setup() {
        const pageOptions = [{ label: 'Artikel', value: 'article' }, { label: 'Ruang Participant', value: 'participant-room' }]
        const typeOptions = [{ label: 'Artikel', value: 'article' }, { label: 'Kajian', value: 'kajian' }]
        return { hasPermission, avatarText, pageOptions, typeOptions }
    },
    methods: {
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
        seoChecker: _debounce(function () {
            this.loadingSEO = true;

            var content = this.formData.content.replace(/<img[^>"']*((("[^"]*")|('[^']*'))[^"'>]*)*>/g,"");

            const paper = new Paper(content, {
                keyword: this.formData.keywords,
                title: this.formData.title,
                description: this.formData.meta_description,
                url: this.formData.slug,
                metaDescription: this.formData.meta_description,
                titleWidth: '',
                locale: this.en_locale,
                permalink: this.permalink
            });
            const contentAssessor = new ContentAssessor(this.i18n());
            const seoAssessor = new SEOAssessor(this.i18n());
            contentAssessor.assess(paper);
            seoAssessor.assess(paper);
            const final_scores = this.getScores(seoAssessor, contentAssessor);
            if(final_scores.seo) {
                this.loadingSEO = false;
            }
            this.seoAssessments = final_scores.seo;
            this.contentAssessments = final_scores.content;
        }),
        onSaveAs(value) {
            this.saveAs = value
            this.onSubmit()
        },
        onSubmit() {
            this.loadingSubmit = true
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                const vForm = new FormData()
                for (let key in this.formData) {
                    if (key == 'image_url') continue
                    if (key == 'meta_index') continue
                    if (this.formData[key] != null) vForm.append(key, this.formData[key])
                }
                if (this.meta_index.length > 0) {
                    vForm.append('meta_index', JSON.stringify(this.meta_index))
                } else {
                    vForm.append('meta_index', '')
                }
                vForm.append('save_as', this.saveAs)
                postData(vForm).then(response => {
                    this.loadingSubmit = false
                    this.$bvToast.toast('Article has been changed successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                    if (this.saveAs == "preview") {
                        var url = "https://www.jejakimani.com/artikel/" + response.data.slug + "?preview=true"
                        // let url = "http://localhost:4200/artikel/" + response.data.slug + "?preview=true"
                        window.open(url, '_blank');
                        this.$router.push({ name: 'article-detail', params: { id: response.data.id } })
                    }
                })
                    .catch(error => {
                        this.loadingSubmit = false
                        if (error.response.data.errors) {
                            this.$refs.refObsForm.setErrors(error.response.data.errors)
                        } else {
                            this.$refs.refObsForm.setErrors(error.response.data)
                        }
                    })
            })
        },
        getScores(seoAssessor, contentAssessor) {
            return {
                seo: new Presenter().getScoresWithRatings(seoAssessor),
                content: new Presenter().getScoresWithRatings(contentAssessor)
            }
        },
        i18n() {
            return new Jed({
                domain: `js-text-analysis`,
                locale_data: {
                    "js-text-analysis": { "": {} }
                }
            })
        },
    },
    data() {
        const formData = {}
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        getDetail(id).then(response => {
            this.formData = response.data
            this.formData['status'] = (response.data['status'] == 1) ? true : false

            JSON.parse(this.formData.meta_index).forEach(element => {
                this.meta_index.push(element)
            });

        }).catch(error => {
            // this.$bvToast.toast(error, {
            //     title: `Error`,
            //     variant: 'danger',
            //     toaster: 'b-toaster-top-center',
            //     solid: true,
            // })
        })

        const categories = []
        getCategorySearch().then(response => {
            this.categories = response.data;
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        const asatidzs = []
        getAsatidzSearch().then(response => {
            this.asatidzs = response.data;
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })

        const robotsMeta = [
            { item: 'noindex', name: 'No Index' },
            { item: 'nofollow', name: 'No Follow' },
            { item: 'noimageindex', name: 'No Image Index' },
            { item: 'noarchive', name: 'No Archive' },
            { item: 'nosnippet', name: 'No Snippet' },
        ]

        var fonSizes = [
            9,10,11,12,13,
            'default',
            17,18,19,20,24,26,28,30,36,40,50,60,70,80
        ]

        return {
            loadingSubmit: false,
            loadingSEO: false,
            formData, required, numeric,
            content: '',
            editorOption: {},
            titleWidth: '',
            en_locale: 'id_ID',
            permalink: '',
            seoAssessments: [],
            contentAssessments: [],
            robotsMeta,
            meta_index: [],
            saveAs: '',
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
                    supportAllValues: false
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
                        'fontFamily',
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
            categories,
            asatidzs
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
