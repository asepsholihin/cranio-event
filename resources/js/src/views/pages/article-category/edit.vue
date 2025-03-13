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
                        <!-- Category Name -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="name" name="Category Name" rules="required|min:3">
                                <b-form-group label="Category Name" label-for="name">
                                    <b-form-input id="name" v-model="form.name" name="name" :state="errors.length > 0 ? false : null" type="text"></b-form-input>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Category Slug -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="slug" name="Category Slug">
                                <b-form-group label="Category Slug" label-for="slug">
                                    <b-form-input id="slug" v-model="form.slug" name="slug" :state="errors.length > 0 ? false : null" type="text"
                                    ></b-form-input>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Parent Category -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="category_id" name="Parent Category">
                                <b-form-group label="Parent Category" label-for="parent_category_id" description="Kosongkan jika sebagai parent category">
                                    <v-select id="parent_category_id" v-model="form.parent_category_id" :options="categories" :clearable="true"
                                        :reduce="(label) => label.id" label="name" />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <b-row>
                        <!-- Description -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="description" name="Description" >
                                <b-form-group label="Description" label-for="description">
                                    <b-form-textarea id="description" v-model="form.description" name="description" :state="errors.length > 0 ? false : null" rows="3" max-rows="6"></b-form-textarea>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Order -->
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" vid="order" name="Order" rules="required|numeric">
                                <b-form-group label="Order" label-for="order">
                                    <b-form-input id="order" v-model="form.order" name="order" :state="errors.length > 0 ? false : null"></b-form-input>
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                    <!-- Form Actions -->
                    <div class="mt-2 d-flex">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit" v-if="hasPermission('article-add-or-edit')">
                            Save Changes
                        </b-button>
                        <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary">
                            Back
                        </b-button>
                    </div>
                </validation-observer>
            </b-form>
        </p>
    </b-card>
</template>

<script>
import {
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
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required, numeric } from "@validations";
import Ripple from "vue-ripple-directive";
import vSelect from "vue-select";
import { avatarText } from "@core/utils/filter";
import { postData, getDetail, getCategorySearch } from "@/network/article-category";
import { hasPermission } from "@/auth/utils";

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
        return { hasPermission, avatarText };
    },
    methods: {
        async onSubmit() {
            const success = await this.$refs.refObsForm.validate();

            if (!success) return;
            this.isButtonLoading = true;

            await postData(this.form)
                .then((response) => {
                    this.$bvToast.toast(
                        `${this.form.name} has been updated successfully`,
                        {
                            title: `Success`,
                            variant: "primary",
                            toaster: "b-toaster-top-center",
                            solid: true,
                        }
                    );
                    this.$emit("refetch-data");
                    this.$emit("update:is-add-sidebar-active", false);
                    this.isButtonLoading = false;
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(
                            error.response.data.errors
                        );
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data);
                    }
                    this.isButtonLoading = false;
                });
        },
    },
    data() {
        const form = {}
        const id = parseInt(this.$route.params.id) || 0;
        if (id == 0) this.$router.back();



        const categories = []
        getCategorySearch({id:id}).then(response => {
            this.categories = response.data;

            getDetail(id).then((response) => {
                this.form = response.data;
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors);
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data);
                }
            });
        }).catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })
        
        return {
            form,
            required,
            numeric,
            categories
        };
    },
};
</script>

<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
@import "~@resources/scss/vue/libs/vue-flatpicker.scss";
</style>
