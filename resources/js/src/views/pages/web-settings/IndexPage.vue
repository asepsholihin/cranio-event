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

                <h3>Index Setting</h3>
                <hr />

                <!-- why us title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Why Us Title"
                            vid="why_us_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Why Us Title"
                                label-for="why-us-title"
                            >
                                <b-form-textarea
                                    id="why-us-title"
                                    v-model="form.why_us_title"
                                    name="why_us_title"
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
                <!--/ why us title -->

                <b-row>
                    <!-- about title -->
                    <b-col md="9">
                        <validation-provider
                            #default="{ errors }"
                            name="About Title"
                            vid="about_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="About Title"
                                label-for="about-title"
                            >
                                <b-form-textarea
                                    id="about-title"
                                    v-model="form.about_title"
                                    name="about_title"
                                    :state="errors.length > 0 ? false : null"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <validation-provider
                            #default="{ errors }"
                            name="About Link"
                            vid="about_link"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="About Link"
                                label-for="about-title"
                            >
                                <b-form-input
                                    id="about-title"
                                    v-model="form.about_link"
                                    name="about_link"
                                    :state="errors.length > 0 ? false : null"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ about title -->

                    <!-- about image background -->
                    <b-col md="3">
                        <validation-provider
                            #default="{ errors }"
                            vid="about_image"
                            name="About Image Background"
                        >
                            <b-form-group label="About Image Background" description="Recommended Size 1080 x 1080 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.about_image"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <tamplate v-if="form.about_image">
                            <b-img :src="form.about_image" fluid />
                        </tamplate>
                        <!-- Alt Image -->
                        <validation-provider #default="{ errors }" name="Alt Image" vid="about_image_alt" rules="required">
                            <b-form-group label="Alt Image">
                            <b-form-input id="about_image_alt" v-model="form.about_image_alt" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <!-- Title Image -->
                        <validation-provider #default="{ errors }" name="Title Image" vid="about_image_title" rules="required">
                            <b-form-group label="Title Image">
                            <b-form-input id="about_image_title" v-model="form.about_image_title" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ about image background -->
                </b-row>

                <b-row>
                    <!-- ustadz profile title -->
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Ustadz Profile Title"
                            vid="profile_ustadz_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Ustadz Profile Title"
                                label-for="profile-ustadz-title"
                            >
                                <b-form-textarea
                                    id="profile-ustadz-title"
                                    v-model="form.profile_ustadz_title"
                                    name="profile_ustadz_title"
                                    :state="errors.length > 0 ? false : null"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <validation-provider
                            #default="{ errors }"
                            name="Ustadz Profile Link"
                            vid="profile_ustadz_link"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Ustadz Profile Link"
                                label-for="profile-ustadz-title"
                            >
                                <b-form-input
                                    id="profile-ustadz-title"
                                    v-model="form.profile_ustadz_link"
                                    name="profile_ustadz_link"
                                    :state="errors.length > 0 ? false : null"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-input>
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ ustadz profile title -->

                    <!-- ustadz profile image background -->
                    <b-col md="3">
                        <validation-provider
                            #default="{ errors }"
                            vid="profile_ustadz_image"
                            name="Ustadz Profile Image Background"
                        >
                            <b-form-group label="Ustadz Profile Image Background" description="Recommended Size 1080 x 1080 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.profile_ustadz_image"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <tamplate v-if="form.profile_ustadz_image">
                            <b-img :src="form.profile_ustadz_image" fluid />
                        </tamplate>
                        <!-- Alt Image -->
                        <validation-provider #default="{ errors }" name="Alt Image" vid="profile_ustadz_image_alt" rules="required">
                            <b-form-group label="Alt Image">
                            <b-form-input id="profile_ustadz_image_alt" v-model="form.profile_ustadz_image_alt" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <!-- Title Image -->
                        <validation-provider #default="{ errors }" name="Title Image" vid="profile_ustadz_image_title" rules="required">
                            <b-form-group label="Title Image">
                            <b-form-input id="profile_ustadz_image_title" v-model="form.profile_ustadz_image_title" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!--/ ustadz profile image background -->
                </b-row>

                <!-- tour package title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Tour Package Title"
                            vid="tour_package_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Tour Package Title"
                                label-for="tour-package-title"
                            >
                                <b-form-textarea
                                    id="tour-package-title"
                                    v-model="form.tour_package_title"
                                    name="tour_package_title"
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
                <!--/ tour package title -->

                <!-- article head title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Article Head Title"
                            vid="article_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Article Head Title"
                                label-for="article-title"
                            >
                                <b-form-textarea
                                    id="article-title"
                                    v-model="form.article_title"
                                    name="article_title"
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
                <!--/ article head title -->

                <!-- youtube video url -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Youtube Video URL"
                            vid="video_url"
                            rules="required"
                        >
                            <b-form-group
                                label="Youtube Video URL"
                                label-for="video-url"
                            >
                                <b-form-input
                                    id="video-url"
                                    v-model="form.video_url"
                                    name="video_url"
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
                <!--/ youtube video url -->

                <!-- partner head title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Partner Head Title"
                            vid="partner_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Partner Head Title"
                                label-for="partner-title"
                            >
                                <b-form-textarea
                                    id="partner-title"
                                    v-model="form.partner_title"
                                    name="partner_title"
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
                <!--/ partner head title -->

                <!-- partner head title -->
                <b-row>
                    <b-col>
                        <validation-provider
                            #default="{ errors }"
                            name="Flagship Program Head Title"
                            vid="flagship_program_title"
                            rules="required|min:3"
                        >
                            <b-form-group
                                label="Flagship Program Head Title"
                                label-for="partner-title"
                            >
                                <b-form-textarea
                                    id="partner-title"
                                    v-model="form.flagship_program_title"
                                    name="flagship_program_title"
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
                <!--/ Flagship Program Head title -->

                <!-- background ustadz salim -->
                <b-row>
                    <b-col sm="6">
                        <validation-provider
                            #default="{ errors }"
                            vid="background_ustadz_salim"
                            name="Background Ustadz Salim"
                        >
                            <b-form-group label="Background Ustadz Salim" description="Recommended Size 1080 x 1080 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.background_ustadz_salim"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <tamplate v-if="form.background_ustadz_salim">
                            <b-img :src="form.background_ustadz_salim" fluid class="mb-2" />
                        </tamplate>
                        <!-- Alt Image -->
                        <validation-provider #default="{ errors }" name="Alt Image" vid="background_ustadz_salim_image_alt" rules="required">
                            <b-form-group label="Alt Image">
                            <b-form-input id="background_ustadz_salim_image_alt" v-model="form.background_ustadz_salim_image_alt" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <!-- Title Image -->
                        <validation-provider #default="{ errors }" name="Title Image" vid="background_ustadz_salim_image_title" rules="required">
                            <b-form-group label="Title Image">
                            <b-form-input id="background_ustadz_salim_image_title" v-model="form.background_ustadz_salim_image_title" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <b-col sm="6">
                        <validation-provider
                            #default="{ errors }"
                            vid="background_package"
                            name="Background Package"
                        >
                            <b-form-group label="Background Package" description="Recommended Size 1080 x 1080 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
                                <b-form-file
                                    accept="image/jpeg, image/png, image/webp"
                                    v-model="form.background_package"
                                    :state="errors.length > 0 ? false : null"
                                    placeholder="Choose image drop it here..."
                                    drop-placeholder="Drop image here..."
                                />

                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <tamplate v-if="form.background_package">
                            <b-img :src="form.background_package" fluid class="mb-2" />
                        </tamplate>
                        <!-- Alt Image -->
                        <validation-provider #default="{ errors }" name="Alt Image" vid="background_package_image_alt" rules="required">
                            <b-form-group label="Alt Image">
                            <b-form-input id="background_package_image_alt" v-model="form.background_package_image_alt" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                        <!-- Title Image -->
                        <validation-provider #default="{ errors }" name="Title Image" vid="background_package_image_title" rules="required">
                            <b-form-group label="Title Image">
                            <b-form-input id="background_package_image_title" v-model="form.background_package_image_title" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                </b-row>
                <!--/ background ustadz salim  -->

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
    BFormFile,
    BRow,
    BCol,
    BCard,
    BInputGroup,
    BInputGroupAppend,
    BSpinner,
    BFormTextarea,
    BFormInvalidFeedback,
    BImg,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import Ripple from "vue-ripple-directive";
import { required, confirmed, min } from "@validations";
import {
    getIndexPageSetting,
    storeIndexPageSetting,
} from "@/network/web-settings";

export default {
    components: {
        BButton,
        BSpinner,
        BForm,
        BFormGroup,
        BFormInput,
        BFormFile,
        BRow,
        BCol,
        BCard,
        BInputGroup,
        BInputGroupAppend,
        BFormInvalidFeedback,
        ValidationProvider,
        ValidationObserver,
        BFormTextarea,
        BImg
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            isSubmitting: false,
            form: {
                why_us_title: "",
                about_image: null,
                about_title: "",
                about_image_alt: "",
                about_image_title: "",
                about_link: "",
                profile_ustadz_image: null,
                profile_ustadz_image_alt: "",
                profile_ustadz_image_title: "",
                profile_ustadz_title: "",
                profile_ustadz_link: "",
                tour_package_title: "",
                article_title: "",
                partner_title: "",
                video_url: "",
                background_ustadz_salim: "",
                background_ustadz_salim_image_alt: "",
                background_ustadz_salim_image_title: "",
                background_package: "",
                flagship_program_title: "",
            },

            //validation
            required,
            confirmed,
            min,
        };
    },
    methods: {
        save() {
            this.$refs.obsVal.validate().then(async (success) => {
                if (!success) return;
                this.isInvalid = true;
                this.isSubmitting = true;

                await storeIndexPageSetting(this.form)
                    .then(this.afterSuccessStore)
                    .catch(this.afterFailedStore);
            });
        },
        afterSuccessStore() {
            this.isSubmitting = false;
            this.isInvalid = false;

            this.$bvToast.toast(
                `Index Page setting has been updated successfully`,
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

            this.$bvToast.toast(`Failed to update index page setting`, {
                title: `Error`,
                variant: "danger",
                toaster: "b-toaster-top-center",
                solid: true,
            });
        },
    },
    async mounted() {
        const response = await getIndexPageSetting();
        const data = response.data;

        if (Object.prototype.toString.call(data) !== "[object Object]") return;

        this.form.why_us_title = data.why_us_title;
        this.form.about_image = data.about_image
        this.form.about_image_alt = data.about_image_alt
        this.form.about_image_title = data.about_image_title
        this.form.about_title = data.about_title;
        this.form.about_link = data.about_link;
        this.form.profile_ustadz_image = data.profile_ustadz_image
        this.form.profile_ustadz_image_alt = data.profile_ustadz_image_alt
        this.form.profile_ustadz_image_title = data.profile_ustadz_image_title
        this.form.profile_ustadz_title = data.profile_ustadz_title;
        this.form.profile_ustadz_link = data.profile_ustadz_link;
        this.form.tour_package_title = data.tour_package_title;
        this.form.article_title = data.article_title;
        this.form.partner_title = data.partner_title;
        this.form.video_url = data.video_url;
        this.form.background_ustadz_salim = data.background_ustadz_salim;
        this.form.background_ustadz_salim_image_alt = data.background_ustadz_salim_image_alt
        this.form.background_ustadz_salim_image_title = data.background_ustadz_salim_image_title
        this.form.background_package = data.background_package;
        this.form.background_package_image_alt = data.background_package_image_alt;
        this.form.background_package_image_title = data.background_package_image_title;
        this.form.flagship_program_title = data.flagship_program_title;
    },
};
</script>
