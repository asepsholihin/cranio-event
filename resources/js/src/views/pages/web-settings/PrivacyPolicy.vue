<style>
ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

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
    height: 400px;
    overflow: auto;
}
</style>

<template>
    <b-card>
        <!-- form -->
        <validation-observer ref="obsVal" #default="{ isInvalid }">
            <b-form class="auth-login-form mt-2" @submit.prevent="save">
                <validation-provider #default="{ errors }" vid="message">
                    <b-alert variant="danger" show v-if="errors[0]">
                        <div class="alert-body">
                            {{ errors[0] }}
                        </div>
                    </b-alert>
                </validation-provider>

                <b-row>
                    <!-- Privacy Policy -->
                    <b-col cols="12" md="12">
                        <validation-provider #default="{ errors }" name="Privacy Policy" vid="privacy_policy">
                            <b-form-group label="Privacy Policy">
                                <ckeditor :editor="editor" v-model="form.privacy_policy" :config="editorConfig"></ckeditor>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>

                <!-- form actions -->
                <b-row>
                    <b-col>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mt-1 mr-1"
                            type="submit" :disabled="isInvalid">
                            <b-spinner small v-show="isSubmitting" />
                            Save changes
                        </b-button>
                    </b-col>
                </b-row>
                <!--/ form actions -->

            </b-form>
        </validation-observer>
    </b-card>
</template>

<script>
import {
    BButton,
    BForm,
    BFormGroup,
    BFormInput,
    BRow,
    BCol,
    BCard,
    BInputGroup,
    BInputGroupAppend,
    BSpinner,
    BFormInvalidFeedback,
    BFormTextarea,
    BFormFile,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import Ripple from "vue-ripple-directive";
import { required, confirmed, min } from "@validations";
import { getGeneralSetting   , storeGeneralSetting } from "@/network/web-settings";
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from "ckeditor5-build-classic-image";

export default {
    components: {
        BButton,
        BSpinner,
        BForm,
        BFormGroup,
        BFormInput,
        BRow,
        BCol,
        BCard,
        BInputGroup,
        BInputGroupAppend,
        BFormInvalidFeedback,
        BFormTextarea,
        BFormFile,
        ValidationProvider,
        ValidationObserver,
        ckeditor: CKEditor.component
    },
    directives: {
        Ripple,
    },
    data() {
        var fonSizes = [
            9, 10, 11, 12, 13,
            'default',
            17, 18, 19, 20, 24, 26, 28, 30, 36, 40, 50, 60, 70, 80
        ]
        return {
            isSubmitting: false,
            form: {
                web_logo: null,
                web_favicon: null,
                web_title: "",
                meta_keywords: "",
                meta_description: "",
                cta_button_text: "",
                web_email: "",
                phone_number: "",
                wa_number_1: "",
                wa_number_2: "",
                copyright_text: "",
                footer_location: "",
                footer_consultation: "",
                privacy_policy: "",
            },

            //validation
            required,
            confirmed,
            min,
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
        };
    },
    methods: {
        save() {
            this.$refs.obsVal.validate().then(async (success) => {
                if (!success) return;
                this.isInvalid = true;
                this.isSubmitting = true;

                await storeGeneralSetting(this.form)
                    .then(this.afterSuccessStore)
                    .catch(this.afterFailedStore);
            });
        },
        afterSuccessStore() {
            this.isSubmitting = false;
            this.isInvalid = false;

            this.$bvToast.toast(
                `Privacy Policy has been updated successfully`,
                {
                    title: `Success`,
                    variant: "primary",
                    toaster: "b-toaster-top-center",
                    solid: true,
                }
            );
        },
        afterFailedStore(err) {
            this.isSubmitting = false;
            this.isInvalid = false;

            this.$bvToast.toast(`Failed to update privacy policy`, {
                title: `Error`,
                variant: "danger",
                toaster: "b-toaster-top-center",
                solid: true,
            });
        },
    },
    async mounted() {
        const response = await getGeneralSetting();
        const data = response.data;

        if (Object.prototype.toString.call(data) !== "[object Object]") return;
         this.form.web_title = data.web_title;
        this.form.meta_keywords = data.meta_keywords;
        this.form.meta_description = data.meta_description;
        this.form.cta_button_text = data.cta_button_text;
        this.form.web_email = data.web_email;
        this.form.phone_number = data.phone_number;
        this.form.wa_number_1 = data.wa_number_1;
        this.form.wa_number_2 = data.wa_number_2;
        this.form.copyright_text = data.copyright_text;
        this.form.footer_location = data.footer_location;
        this.form.footer_consultation = data.footer_consultation;
        this.form.privacy_policy = data.privacy_policy;
    },
};
</script>
