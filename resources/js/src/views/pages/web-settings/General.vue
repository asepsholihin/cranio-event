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

                <h3>Main Info</h3>
                <hr />

                <b-row>
                    <!-- web logo -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            vid="web_logo"
                            name="Web Logo"
                        >
                            <b-form-group label="Web Logo" description="Recommended Size 600 x 600 px (Format : PNG, JPG) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.web_logo"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <img :src="form.web_logo_path" height="36" class="mb-2">
                    </b-col>
                    <!--/ web logo -->

                    <!-- web favicon -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            vid="web_favicon"
                            name="Web Favicon"
                        >
                            <b-form-group label="Web Favicon" description="Recommended Size 100 x 100 px (Format : PNG) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.web_favicon"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <img :src="form.web_favicon_path" height="36" class="mb-2">
                    </b-col>
                    <!--/ web favicon -->
                </b-row>

                <!-- homepage title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Homepage Title"
                            vid="web_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Homepage Title"
                                label-for="web-title"
                            >
                                <b-form-input
                                    id="web-title"
                                    v-model="form.web_title"
                                    name="web_title"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>
                <!--/ homepage title -->

                <!-- meta keywords -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Meta Keywords"
                            vid="meta_keywords"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Meta Keywords"
                                label-for="meta-keywords"
                            >
                                <b-form-input
                                    id="meta-keywords"
                                    v-model="form.meta_keywords"
                                    name="meta_keywords"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>
                <!--/ meta keywords -->

                <!-- meta description -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Meta Description"
                            vid="meta_description"
                            rules="required|min:7"
                        >
                            <b-form-group
                                label="Meta Description"
                                label-for="meta-description"
                            >
                                <b-form-input
                                    id="meta-description"
                                    v-model="form.meta_description"
                                    name="meta_description"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>
                <!--/ meta description -->

                <!-- cta button text -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="CTA Button Text"
                            vid="cta_button_text"
                            rules="required|min:7"
                        >
                            <b-form-group
                                label="CTA Button Text"
                                label-for="cta-button-text"
                            >
                                <b-form-input
                                    id="cta-button-text"
                                    v-model="form.cta_button_text"
                                    name="cta_button_text"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>
                <!--/ cta button text -->

                <h3 class="mt-3">Contact Info</h3>
                <hr />

                <b-row>
                    <!-- email -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Email"
                            vid="web_email"
                            rules="required|email"
                        >
                            <b-form-group label="Email" label-for="web-email">
                                <b-form-input
                                    id="web-email"
                                    v-model="form.web_email"
                                    name="web_email"
                                    :state="errors.length > 0 ? false : null"
                                    type="email"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ email -->

                    <!-- phone number -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Phone Number"
                            vid="phone_number"
                            rules="required|min:10"
                        >
                            <b-form-group
                                label="Phone Number"
                                label-for="phone-number"
                            >
                                <b-form-input
                                    id="phone-number"
                                    v-model="form.phone_number"
                                    name="phone_number"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ phone number -->
                </b-row>

                <b-row>
                    <!-- wa number 1 -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Whatsapp Number 1"
                            vid="wa_number_1"
                            rules="required|min:10"
                        >
                            <b-form-group
                                label="Whatsapp Number 1"
                                label-for="wa-number-1"
                            >
                                <b-form-input
                                    id="wa-number-1"
                                    v-model="form.wa_number_1"
                                    name="wa_number_1"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ wa number 1 -->
                    <!-- wa number 2 -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Whatsapp Number 2"
                            vid="wa_number_2"
                            rules="required|min:10"
                        >
                            <b-form-group
                                label="Whatsapp Number 2"
                                label-for="wa-number-2"
                            >
                                <b-form-input
                                    id="wa-number-2"
                                    v-model="form.wa_number_2"
                                    name="wa_number_2"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ wa number 2 -->
                </b-row>

                <h3 class="mt-3">Shaum Information Alert</h3>
                <hr />

                <b-row>
                    <!-- shaum_information -->
                    <b-col md="12">
                        <validation-provider #default="{ errors }" name="Shaum Information" vid="shaum_information">
                            <b-form-group label="Shaum Information">
                                <ckeditor :editor="editor" v-model="form.shaum_information" :config="editorConfig"></ckeditor>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        
                        <!--/ shaum_information -->
                        <validation-provider #default="{ errors }" name="Show in Alert" vid="show">
                            <b-form-group label="Show in Alert">
                                <b-form-checkbox v-model="form.shaum_information_alert" name="check-button" switch />
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>

                <h3 class="mt-3">Footer Info</h3>
                <hr />

                <b-row>
                    <!-- copyright text -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Copyright Text"
                            vid="copyright_text"
                            rules="required"
                        >
                            <b-form-group
                                label="Copyright Text"
                                label-for="copyright-text"
                            >
                                <b-form-input
                                    id="copyright-text"
                                    v-model="form.copyright_text"
                                    name="copyright_text"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ copyright text -->
                    <!-- consultation title -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Consultation Title"
                            vid="footer_consultation"
                            rules="required"
                        >
                            <b-form-group
                                label="Consultation Title"
                                label-for="footer-consultation"
                            >
                                <b-form-input
                                    id="footer-consultation"
                                    v-model="form.footer_consultation"
                                    name="footer_consultation"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ consultation title -->
                </b-row>

                <b-row>
                    <!-- Location -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Location Title"
                            vid="footer_location"
                            rules="required|min:10"
                        >
                            <b-form-group
                                label="Location Title"
                                label-for="footer-location"
                            >
                                <b-form-input
                                    id="footer-location"
                                    v-model="form.footer_location"
                                    name="footer_location"
                                    :state="errors.length > 0 ? false : null"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ Location -->
                    <!-- copyright text -->
                    <b-col md="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Partnership and Branch"
                            vid="partnership_contact"
                            rules="required"
                        >
                            <b-form-group
                                label="Partnership and Branch"
                                label-for="partnership-contact"
                            >
                                <b-form-input
                                    id="partnership-contact"
                                    v-model="form.partnership_contact"
                                    name="partnership_contact"
                                    :state="errors.length > 0 ? false : null"
                                    type="text"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ copyright text -->
                </b-row>

                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Head Office Address"
                            vid="head_office_address"
                            rules="required"
                        >
                            <b-form-group
                                label="Head Office Address"
                                label-for="head_office_address"
                            >
                                <b-form-textarea
                                    id="head_office_address"
                                    v-model="form.head_office_address"
                                    name="head_office_address"
                                    :state="errors.length > 0 ? false : null"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>
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
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="primary"
                            class="mt-1 mr-1"
                            type="submit"
                            :disabled="isInvalid"
                        >
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
    BFormCheckbox
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import Ripple from "vue-ripple-directive";
import { required, confirmed, min } from "@validations";
import { getGeneralSetting, storeGeneralSetting } from "@/network/web-settings";
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
        BFormCheckbox,
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
                shaum_information: "",
                shaum_information_alert: false,
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
                `General setting has been updated successfully`,
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

            this.$bvToast.toast(`Failed to update general setting`, {
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

        this.form.web_logo_path = data.web_logo
        this.form.web_favicon_path = data.web_favicon
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
        this.form.shaum_information = data.shaum_information;
        this.form.shaum_information_alert = data.shaum_information_alert;
        this.form.head_office_address = data.head_office_address;
        this.form.partnership_contact = data.partnership_contact;
    },
};
</script>
