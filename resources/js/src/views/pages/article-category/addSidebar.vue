<template>
    <b-sidebar
        id="add-new-sidebar"
        :visible="isAddSidebarActive"
        bg-variant="white"
        sidebar-class="sidebar-lg"
        shadow
        backdrop
        no-close-on-backdrop
        no-header
        right
        @hidden="reset"
        @change="(val) => $emit('update:is-add-sidebar-active', val)"
    >
        <template #default="{ hide }">
            <!-- Header -->
            <div class="px-2 py-1 d-flex justify-content-between align-items-center content-sidebar-header">
                <h5 class="mb-0">Add New Category</h5>

                <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />
            </div>

            <!-- BODY -->
            <validation-observer ref="refObsForm">
                <!-- Form -->
                <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="reset">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>

                    <!-- Category Name -->
                    <validation-provider #default="{ errors }" vid="name" name="Category Name" rules="required|min:3">
                        <b-form-group label="Category Name" label-for="name">
                            <b-form-input id="name" v-model="form.name" name="name" :state="errors.length > 0 ? false : null" type="text"
                            ></b-form-input>
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Category Slug -->
                    <validation-provider #default="{ errors }" vid="slug" name="Category Slug">
                        <b-form-group label="Category Slug" label-for="slug">
                            <b-form-input id="slug" v-model="form.slug" name="slug" :state="errors.length > 0 ? false : null" type="text"></b-form-input>
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Parent Category -->
                    <validation-provider #default="{ errors }" vid="category_id" name="Parent Category">
                        <b-form-group label="Parent Category" label-for="parent_category_id" description="Kosongkan jika sebagai parent category">
                            <v-select id="parent_category_id" v-model="form.parent_category_id" :options="categories" :clearable="true"
                                :reduce="(label) => label.id" label="name" />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Description -->
                    <validation-provider #default="{ errors }" vid="description" name="Description">
                        <b-form-group label="Description" label-for="description">
                            <b-form-textarea id="description" v-model="form.description" name="description" :state="errors.length > 0 ? false : null" rows="3" max-rows="6"></b-form-textarea>
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Order -->
                    <validation-provider #default="{ errors }" vid="order" name="Order" rules="required|numeric">
                        <b-form-group label="Order" label-for="order">
                            <b-form-input id="order" v-model="form.order" name="order" :state="errors.length > 0 ? false : null"></b-form-input>
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Form Actions -->
                    <div class="mt-2 d-flex">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit" :disabled="isButtonLoading">
                            <b-spinner small v-show="isButtonLoading" /> Add
                        </b-button>
                        <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary" @click="hide">
                            Cancel
                        </b-button>
                    </div>
                </b-form>
            </validation-observer>
        </template>
    </b-sidebar>
</template>

<script>
import {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BFormInvalidFeedback,
    BButton,
    BAlert,
    BSpinner,
    BFormCheckbox,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required, numeric, email } from "@validations";
import Ripple from "vue-ripple-directive";
import vSelect from "vue-select";
import { getCategorySearch, postData } from "@/network/article-category";
import flatPickr from "vue-flatpickr-component";

export default {
    components: {
        BSidebar,
        BForm,
        BFormGroup,
        BFormInput,
        BFormTextarea,
        BFormFile,
        BFormCheckbox,
        BAlert,
        BFormInvalidFeedback,
        BButton,
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
    model: {
        prop: "isAddSidebarActive",
        event: "update:is-add-sidebar-active",
    },
    props: {
        isAddSidebarActive: {
            type: Boolean,
            required: true,
        },
    },
    data() {
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
        return {
            isButtonLoading: false,
            required,
            numeric,
            email,
            form: {
                name: "",
                description: "",
                order: "",
                active: true,
                status: true,
                slug: "",
            },
            categories,
        };
    },
    methods: {
        reset() {
            for (var key in this.form) {
                this.form[key] = null;
            }
            this.$refs.refObsForm.reset();
            getCategorySearch().then(response => {
                this.categories = response.data;
            }).catch(error => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors)
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data)
                }
            })
        },
        onSubmit() {
            this.$refs.refObsForm.validate().then((success) => {
                if (!success) return;
                this.isButtonLoading = true;
                postData(this.form).then((response) => {
                    this.$bvToast.toast(
                        `${this.form.name} has been added successfully`,
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
                        this.$refs.refObsForm.setErrors(
                            error.response.data
                        );
                    }
                    this.isButtonLoading = false;
                });
            });
        },
    },
};
</script>

<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
@import "~@resources/scss/vue/libs/vue-flatpicker.scss";

#add-new-sidebar {
    .vs__dropdown-menu {
        max-height: 200px !important;
    }
}
</style>
