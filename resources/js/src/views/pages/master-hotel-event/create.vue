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


                    <!-- TITLE INFORMATION -->
                    <div class="border-bottom">
                        <h5>Hotel Information</h5>
                    </div>

                    <b-row>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Hotel Name" vid="hotel_name" rules="required">
                              <b-form-group label="Hotel Name">
                                <b-form-input v-model="formData.hotel_name" placeholder="please type hotel name" name="hotel_name" :state="errors.length > 0 ? false : null" trim />

                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Hotel City" vid="hotel_city" rules="required">
                              <b-form-group label="Hotel City">
                                <v-select v-model="formData.hotel_city" :options="cityList"
                                    :clearable="true" :reduce="label => label.id" placeholder="Please select city" />
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Purpose" vid="purpose">
                              <b-form-group label="Purpose">
                                    <div class="d-flex">
                                        <b-form-checkbox class="mr-2" v-model="formData.is_manasik" @input="isManasik" value="1" unchecked-value="0">
                                            Manasik
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="formData.is_transit" @input="isTransit" value="1" unchecked-value="0">
                                            Transit
                                        </b-form-checkbox>
                                    </div>
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="PIC Name" vid="hotel_pic" rules="required">
                              <b-form-group label="PIC Name">
                                <b-form-input v-model="formData.hotel_pic" placeholder="please type pic name" name="pic_name" :state="errors.length > 0 ? false : null" trim />
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="PIC Number" vid="hotel_pic_number" rules="required">
                              <b-form-group label="PIC Number">
                                <b-form-input v-model="formData.hotel_pic_number" placeholder="please type hotel pic number" name="hotel_pic_number" :state="errors.length > 0 ? false : null" trim />
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="4">
                            <validation-provider #default="{ errors }" name="Google Map Link" vid="hotel_map_url" rules="required">
                              <b-form-group label="Google Map Link">
                                <b-form-input v-model="formData.hotel_map_url" placeholder="please paste google map url" name="hotel_map_url" :state="errors.length > 0 ? false : null" trim />
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="12">
                            <validation-provider #default="{ errors }" name="Hotel Address" vid="hotel_address" rules="required">
                              <b-form-group label="Hotel Address">
                                <b-form-textarea id="hotel_address" v-model="formData.hotel_address" placeholder="please type full address here"
                                        :state="errors.length > 0 ? false : null" trim />
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>


                    <!-- MANASIK INFOMATION -->
                    <div class="border-bottom" v-if="manasikForm">
                        <h5>Manasik Information</h5>
                    </div>

                    <b-row v-if="manasikForm">
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Half Day Price" vid="manasik_hd_price" >
                              <b-form-group label="Half Day Price">
                                <cleave v-model="formData.manasik_hd_price" class="form-control" :options="optionClave"/>
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                            <div class="" v-for="(intent, key) in formData.manasik_hd_list">
                                <div class="d-flex">
                                    <validation-provider #default="{ errors }" class="w-100 mr-2 me-2" name="Half Day Facilities" vid="intent.custom_text" >
                                        <b-form-group label="Half Day Facilities">
                                            <b-form-input v-model="intent.custom_text" placeholder="please type fasilities" name="custom_text" :state="errors.length > 0 ? false : null" trim />
                                            <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>

                                    <b-button class="bg-transparent remove"
                                        type="button" @click="remove('hd', key)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <b-button class="bg-transparent"
                                    type="button" @click="addMore('hd')">
                                    <feather-icon icon="PlusIcon" class="mr-25" /> add more
                                </b-button>
                            </div>
                        </b-col>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Full Day Price" vid="manasik_fd_price" >
                              <b-form-group label="Full Day Price">
                                <cleave v-model="formData.manasik_fd_price" class="form-control" :options="optionClave"/>
                                <b-form-invalid-feedback>
                                  {{ errors[0] }}
                                </b-form-invalid-feedback>
                              </b-form-group>
                            </validation-provider>
                            <div class="" v-for="(intent, key) in formData.manasik_fd_list">
                                <div class="d-flex">
                                    <validation-provider #default="{ errors }" name="Full Day Facilities" vid="intent.custom_text"  class="w-100 mr-2 me-2">
                                        <b-form-group label="Full Day Facilities">
                                            <b-form-input v-model="intent.custom_text" placeholder="please type fasilities" name="custom_text" :state="errors.length > 0 ? false : null" trim />
                                            <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                    <b-button class="bg-transparent remove"
                                        type="button" @click="remove('fd', key)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <b-button class="bg-transparent"
                                    type="button" @click="addMore('fd')">
                                    <feather-icon icon="PlusIcon" class="mr-25" /> add more
                                </b-button>
                            </div>
                        </b-col>
                    </b-row>

                    <!-- Advantages dan Disadvantages -->
                    <div class="border-bottom" v-if="manasikForm"><h5>Advanatges and Disadvantages</h5></div>

                    <b-row v-if="manasikForm">
                        <b-col cols="12" md="6">
                            <div class="" v-for="(intent, key) in formData.manasik_ad_list">
                                <div class="d-flex">
                                    <validation-provider #default="{ errors }" name="Advantages" vid="intent.custom_text"  class="w-100 mr-2 me-2">
                                        <b-form-group label="Advantages">
                                            <b-form-input v-model="intent.custom_text" placeholder="please type advantages" name="custom_text" :state="errors.length > 0 ? false : null" trim />
                                            <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                    <b-button class="bg-transparent remove"
                                        type="button" @click="remove('ad', key)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <b-button class="bg-transparent"
                                    type="button" @click="addMore('ad')">
                                    <feather-icon icon="PlusIcon" class="mr-25" /> add more
                                </b-button>
                            </div>
                        </b-col>
                        <b-col cols="12" md="6">
                            <div class="" v-for="(intent, key) in formData.manasik_da_list">
                                <div class="d-flex">
                                    <validation-provider #default="{ errors }" name="Disadvantages" vid="intent.custom_text"  class="w-100 mr-2 me-2">
                                        <b-form-group label="Disadvantages">
                                            <b-form-input v-model="intent.custom_text" placeholder="please type advantages" name="custom_text" :state="errors.length > 0 ? false : null" trim />
                                            <b-form-invalid-feedback>
                                            {{ errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                    <b-button class="bg-transparent remove"
                                        type="button" @click="remove('da', key)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <b-button
                                    type="button" @click="addMore('da')" class="bg-transparent">
                                    <feather-icon icon="PlusIcon" class="mr-25" /> add more
                                </b-button>
                            </div>
                        </b-col>
                    </b-row>

                     <!-- Transit Information -->
                     <div class="border-bottom" v-if="transitForm"><h5>Transit Information</h5></div>

                    <div class="row" v-if="transitForm">
                        <div class="col-12 border-bottom border-bottom-unset-height" v-for="(intent, key) in formData.hotel_event_list">
                            <div class="d-flex justify-content-end mb-2">
                                <b-button class="bg-transparent remove"
                                    type="button" @click="remove('he', key)">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                            <b-row>
                                <b-col cols="12" md="6">
                                    <div class="">
                                        <validation-provider #default="{ errors }" name="Price Category" vid="price_category" >
                                            <b-form-group label="Price Category">
                                            <v-select v-model="intent.rate_category" class="w-100" :options="priceCategory"
                                                    :clearable="true" :reduce="label => label.id" placeholder="Please select price category" />
                                                <b-form-invalid-feedback>
                                                {{ errors[0] }}
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                        </validation-provider>
                                        <div class="d-flex justify-content-end align-items-center">
                                            <b-button class="bg-transparent"
                                                type="button" @click="add(intent.rate_category)">
                                                <feather-icon icon="PlusIcon" class="mr-25" /> add
                                            </b-button>
                                        </div>
                                    </div>
                                </b-col>
                                <b-col cols="12">
                                    <div class="row" v-for="(childs) in formData.hotel_event_list[key].child">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end mb-2">
                                                <b-button class="bg-transparent remove"
                                                    type="button" @click="removeChild(key)">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <validation-provider #default="{ errors }" name="Item Name" vid="child.item_name" >
                                                <b-form-group label="Item Name">
                                                    <b-form-input v-model="childs.item_name" placeholder="please type item name" name="childs.item_name" :state="errors.length > 0 ? false : null" trim />
                                                    <b-form-invalid-feedback>
                                                    {{ errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </div>
                                        <div class="col-6">
                                            <validation-provider #default="{ errors }" name="Item Price" vid="childs.item_price" >
                                            <b-form-group label="Item Price">
                                                <cleave v-model="childs.item_price" class="form-control" :options="optionClave"/>
                                                <b-form-invalid-feedback>
                                                {{ errors[0] }}
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                            </validation-provider>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mb-2">
                                        <b-button
                                            type="button" @click="addMoreChild(key)" class="bg-transparent">
                                            <feather-icon icon="PlusIcon" class="mr-25" /> add more
                                        </b-button>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end" v-if="transitForm">
                        <b-button
                            type="button" @click="addMore('he')" class="bg-transparent">
                            <feather-icon icon="PlusIcon" class="mr-25" /> add more category
                        </b-button>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2 justify-content-end">
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2"
                            type="submit" v-if="hasPermission('master-hotel-event-add-or-edit')">
                            Add New Hotel
                        </b-button>
                        <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button"
                            variant="outline-secondary">
                            Back
                        </b-button>
                    </div>

                </validation-observer>
            </b-form>
        </p>

        <!-- ADD RATE CATEGORY -->
        <b-modal size="md" v-model="showAddRate" centered ok-title="Add" @ok="onSubmitCategory" @hidden="resetModal" no-close-on-backdrop>
            <template #modal-title>
                <h4>Rate Category</h4>
            </template>
            <div class="table-departments">
                <b-form-group label="Category Name">
                    <b-form-input v-model="categoryData.name" placeholder="please type category name" name="category_name" trim />
                </b-form-group>
            </div>
        </b-modal>
    </b-card>
</template>

<script>
import { BCard, BLink, BFormInvalidFeedback, BButton, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormFile, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BMedia, BFormRadioGroup, BFormRadio } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import Ripple from 'vue-ripple-directive'
import Cleave from 'vue-cleave-component'
import vSelect from 'vue-select'
import { postData, priceCategoryAll, postDataCategory} from '@/network/master-hotel-event'
import { getListAll as allCity } from '@/network/city'
import { hasPermission } from '@/auth/utils'

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
        BFormRadioGroup,
        BFormRadio,
        Cleave,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    setup() {
        return {hasPermission}
    },
    methods: {
        addMore(type){
            if(type == 'fd'){
                if(this.formData.manasik_fd_list){
                    this.formData.manasik_fd_list.push({'custom_text':'', 'category':'manasik_fd_price'});
                }else{
                    this.formData.manasik_fd_list = [{'custom_text':'', 'category':'manasik_fd_price'}];
                }
            }
            if(type == 'hd'){
                if(this.formData.manasik_hd_list){
                    this.formData.manasik_hd_list.push({'custom_text':'', 'category':'manasik_hd_price'});
                }else{
                    this.formData.manasik_hd_list = [{'custom_text':'', 'category':'manasik_hd_price'}];
                }
            }
            if(type == 'ad'){
                if(this.formData.manasik_ad_list){
                    this.formData.manasik_ad_list.push({'custom_text':'', 'category':'advantages'});
                }else{
                    this.formData.manasik_ad_list = [{'custom_text':'', 'category':'advantages'}];
                }
            }
            if(type == 'da'){
                if(this.formData.manasik_da_list){
                    this.formData.manasik_da_list.push({'custom_text':'', 'category':'disadvantages'});
                }else{
                    this.formData.manasik_da_list = [{'custom_text':'', 'category':'disadvantages'}];
                }
            }
            if(type == 'he'){
                if(this.formData.manasik_da_list){
                    this.formData.hotel_event_list.push({'rate_category':'', child: [{'item_name':'', 'item_price':''}]});
                }else{
                    this.formData.hotel_event_list = [{'rate_category':'', child: [{'item_name':'', 'item_price':''}]}];

                }
            }
        },
        addMoreChild(key){
            if(this.formData.hotel_event_list[key].child){
                this.formData.hotel_event_list[key].child.push({'item_name':'', 'item_price':''});
            }else{
                this.formData.hotel_event_list[key].child = [{'item_name':'', 'item_price':''}];
            }
        },
        removeChild(){
            this.formData.hotel_event_list[key].child.splice(key, 1);
        },
        remove(type, key){
            if(type == 'hd'){
                this.formData.manasik_hd_list.splice(key, 1);
            }
            if(type == 'fd'){
                this.formData.manasik_fd_list.splice(key, 1);
            }
            if(type == 'ad'){
                this.formData.manasik_ad_list.splice(key, 1);
            }
            if(type == 'da'){
                this.formData.manasik_da_list.splice(key, 1);
            }
            if(type == 'he'){
                this.formData.hotel_event_list.splice(key, 1);
            }
        },
        resetModal(){
            this.showAddRate = false
            this.categoryData = {}
        },
        add(item){
            this.showAddRate = true
        },
        onSubmitCategory(){
            postDataCategory(this.categoryData).then(response => {
                this.showAddRate = false
                this.categoryData = {}
                this.priceCategoryReplace()
            })
            .catch(error => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors)
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data)
                }
            })
        },
        onSubmit() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                postData(this.formData).then(response => {
                    this.$bvToast.toast('Master Hotel has been save successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })

                    setTimeout(() => {
                        this.$router.push({name:'master-hotel-event'});
                    }, 350);
                })
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors)
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data)
                    }
                })
            })
        },
        isTransit(){
            if(this.formData.is_transit == 1){
                this.transitForm = true
            }else{
                this.transitForm = false
            }
        },
        isManasik(){
            if(this.formData.is_manasik == 1){
                this.manasikForm = true
            }else{
                this.manasikForm = false
            }
        },
        priceCategoryReplace(){
            priceCategoryAll().then(response => {
                this.priceCategory = response.data
            }).catch(error => {
                this.$bvToast.toast(error, {
                    title: `Error`,
                    variant: 'danger',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
            })
        }
    },
    data() {
        const formData = {}
        const cityList = []
        allCity().then(response => {
            this.cityList = response.data
        }).catch(error => {
            this.$bvToast.toast(error, {
                title: `Error`,
                variant: 'danger',
                toaster: 'b-toaster-top-center',
                solid: true,
            })
        })
        const priceCategory = []
        this.priceCategoryReplace();

        formData.manasik_hd_list = [{'custom_text':'', 'category':'manasik_hd_price'}];
        formData.manasik_fd_list = [{'custom_text':'', 'category':'manasik_fd_price'}];
        formData.manasik_ad_list = [{'custom_text':'', 'category':'advantages'}];
        formData.manasik_da_list = [{'custom_text':'', 'category':'disadvantages'}];
        formData.hotel_event_list = [{'rate_category':'', child:[{'item_name':'', 'item_price':''}]}];
        return {
            optionClave: {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
            },
            formData,
            transitForm:false,
            required,
            priceCategory,
            manasikForm:false,
            showAddRate:false,
            categoryData:{},
            numeric,
            cityList
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
.border-bottom{
    border-bottom: 1px solid gray !important;
    margin-top: 30px;
    margin-bottom: 30px;
    height:40px;
}

.border-bottom-unset-height{
    margin-top: unset !important;
    height: unset !important;
}

.bg-transparent{
    padding:0 !important;
    border:0 !important;
    background-color: transparent !important;
    color:#8c0095 !important;
}
.bg-transparent:active{
    background-color: transparent !important;
}
.bg-transparent:hover{
    box-shadow:none !important;
}
.bg-transparent:focus{
    background-color: transparent !important;
}

.bg-transparent:focus-visible{
    outline: none !important;
}

.remove{
    font-size: 20px;
    color:red !important;
}
</style>
