<template>
    <div>
       <check-in-barcode-sidebar :event-attendance="event" :is-barcode-sidebar-active.sync="isBarcodeSidebarActive"
            @refetch-data="refreshDataPage" v-if="hasPermission('event-attendance-add-or-edit')" />
        <b-card>
            <h3 class="text-default">{{ event.name }} - {{ event.description }}</h3>
            <h4>{{ formatDate(event.event_date) }}</h4>
            <h4>Total Check In: <span class="align-text-top">{{ event.total_checkin }} of {{ event.total_attendance }}</span></h4>

            <b-row>
                <b-col cols="12">
                    <div class="d-flex align-items-center justify-content-end">
                        <b-button variant="success" :to="{ name: 'event-attendance-open-registration-report', params: { id: event.uuid, name: event.name } }" class="mr-1"
                            v-if="hasPermission('event-attendance-view')">
                            <span class="text-nowrap"><feather-icon icon="PieChartIcon" size="14" /> Report</span>
                        </b-button>
                        <b-button variant="info" @click="generateOpenRegistrationLink()" class="mr-1"
                            v-if="hasPermission('event-attendance-view')">
                            <span class="text-nowrap">Generate Link Registrasi</span>
                        </b-button>
                        <b-button variant="primary" @click="isBarcodeSidebarActive = true" class="mr-1"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">Check In Barcode</span>
                        </b-button>
                        <b-button variant="primary" @click="addAttendee()" class="mr-1"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">Add Attendee</span>
                        </b-button>
                    </div>
                    <div class="d-flex align-items-center justify-content-end mt-1">
                        <b-button variant="primary" :to="{ name: 'checkin-attendee-open-registration', params: { id: event.uuid, name: event.name } }" class="mr-1"
                            v-if="hasPermission('event-attendance-view') && event.is_paid_event">
                            <span class="text-nowrap">Checkin Event</span>
                        </b-button>
                        <b-button variant="warning" :to="{ name: 'mapping-attendee-open-registration', params: { id: event.uuid, name: event.name } }" class="mr-1"
                            v-if="hasPermission('event-attendance-view') && event.is_paid_event">
                            <span class="text-nowrap">Mapping Seat Number</span>
                        </b-button>
                        <b-button variant="danger" @click="sendAllBarcode()" class="mr-1" :disabled="isButtonLoading"
                            v-if="hasPermission('event-attendance-view') && event.is_paid_event">
                            <span class="text-nowrap"><b-spinner small v-show="isButtonLoading" /> Kirim Barcode Ke Semua</span>
                        </b-button>
                        <b-button variant="danger" @click="downloadAllBarcode()" class="mr-1" :disabled="isButtonLoading"
                            v-if="hasPermission('event-attendance-view') && event.is_paid_event">
                            <span class="text-nowrap"><b-spinner small v-show="isButtonLoading" /> Download Semua Barcode</span>
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <!-- Table Top -->
            <div class="m-2">
                <b-row>

                    <!-- Per Page -->
                    <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
                        <label>Show</label>
                        <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
                            class="per-page-selector d-inline-block mx-50" />
                        <label>entries</label>
                    </b-col>

                    <!-- Search -->
                    <b-col cols="12" md="6">
                        <div class="d-flex align-items-center justify-content-end">
                            <b-form-input v-model="searchQuery" debounce="350" class="d-inline-block" type="search"
                                placeholder="Search..." />

                        </div>
                    </b-col>
                </b-row>

            </div>

            <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
                :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty
                empty-text="No matching records found" :sort-desc.sync="isSortDirDesc" :tbody-tr-class="rowVariant">

                <template #cell(name)="data">
                    <span @click="editAttendee(data.item)" class="text-primary">{{ data.item.name }}</span>
                </template>
                <!-- Column: Pax -->
                <template #cell(pax)="data">
                    <span @click="viewSeats(data.item)" class="text-primary">{{ data.item.pax }} Orang</span>
                </template>
                <!-- Column: Actual Pax -->
                <template #cell(actual_pax)="data">
                    <span class="text-nowrap">{{ data.item.actual_pax }} Orang</span>
                </template>
                
                <!-- Column: Alumni -->
                <template #cell(is_alumni)="data">
                    <center>
                        <span v-if="data.item.is_alumni">Ya</span>
                        <span v-else>Bukan</span>
                    </center>
                </template>

                <!-- Column: Actions -->
                <template #cell(actions)="data">
                    <b-button size="sm" :variant="(data.item.actual_pax == 0) ? 'primary' : 'secondary'"
                        :disabled="actionCIButton" @click="attendeeAction(data.item)"
                        v-if="hasPermission('event-attendance-add-or-edit')">
                        <span class="text-nowrap">{{ resolveCheckIn(data.item.actual_pax) }}</span>
                    </b-button>
                    
                    <b-dropdown variant="link" no-caret>
                        <template #button-content>
                            <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
                        </template>
                        <b-dropdown-item variant="info" @click="sendBarcode(data.item)" v-if="hasPermission('event-attendance-add-or-edit')">
                            <feather-icon icon="SendIcon" />
                            <span class="align-middle ml-50">Kirim Barcode</span>
                        </b-dropdown-item>
                    </b-dropdown>
                </template>

                <!-- Column: Actions -->
                <template #cell(check_in_at)="data">
                    {{ formatDateTime(data.item.check_in_at) }}
                </template>

            </b-table>
            <div class="mx-2 mb-2">
                <b-row>

                    <b-col cols="12" sm="6"
                        class="d-flex align-items-center justify-content-center justify-content-sm-start">
                        <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
                            entries</span>
                    </b-col>
                    <!-- Pagination -->
                    <b-col cols="12" sm="6"
                        class="d-flex align-items-center justify-content-center justify-content-sm-end">

                        <b-pagination v-model="currentPage" :total-rows="totalUsers" :per-page="perPage" first-number
                            last-number class="mb-0 mt-1 mt-sm-0" prev-class="prev-item" next-class="next-item">
                            <template #prev-text>
                                <feather-icon icon="ChevronLeftIcon" size="18" />
                            </template>
                            <template #next-text>
                                <feather-icon icon="ChevronRightIcon" size="18" />
                            </template>
                        </b-pagination>

                    </b-col>

                </b-row>
            </div>

        </b-card>

        <b-modal v-model="addAttendeeModal" ok-title="Add" @hidden="resetModal" @ok="handleAddAttendee"
            :busy="isSubmitModal" centered no-close-on-backdrop ok-only
            :title="`Add Attendee`">
            <validation-observer ref="refObsForm">
                <!-- Form -->
                <b-form class="p-2" @submit.prevent="onSubmitAddAttendance">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>

                    <validation-provider #default="{ errors }" name="Name" vid="name" rules="required">
                        <b-form-group label="Name">
                            <b-form-input v-model="formData.name" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider #default="{ errors }" name="No. HP" vid="no_hp" rules="required|numeric">
                        <b-form-group label="No. HP">
                            <b-form-input v-model="formData.no_hp" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                        <b-form-group label="Email">
                            <b-form-input v-model="formData.email" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider #default="{ errors }" name="Total Pax" vid="pax" rules="required|numeric">
                        <b-form-group label="Total Pax">
                            <b-form-input v-model="formData.pax" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <b-row>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Total Ikhwan" vid="pax_ikhwan" rules="numeric">
                                <b-form-group label="Total Ikhwan">
                                    <b-form-input v-model="formData.pax_ikhwan" :state="errors.length > 0 ? false : null"
                                        trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                        <b-col cols="12" md="6">
                            <validation-provider #default="{ errors }" name="Total Akhwat" vid="pax_akhwat" rules="numeric">
                                <b-form-group label="Total Akhwat">
                                    <b-form-input v-model="formData.pax_akhwat" :state="errors.length > 0 ? false : null"
                                        trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>

                    <validation-provider #default="{ errors }" name="Apakah Alumni?" vid="is_alumni">
                        <b-form-group label="Apakah Alumni?">
                            <b-form-checkbox v-model="formData.is_alumni" name="check-button" switch />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <template v-if="formData.is_alumni">
                        <validation-provider #default="{ errors }" name="Keberangkatan Umrah/Haji" vid="last_umroh_trip">
                            <b-form-group label="Keberangkatan Umrah/Haji">
                                <b-form-input v-model="formData.last_umroh_trip" :state="errors.length > 0 ? false : null"
                                    trim />
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </template>

                    <validation-provider #default="{ errors }" name="Notes" vid="notes">
                        <b-form-group label="Notes">
                            <b-form-input v-model="formData.notes" :state="errors.length > 0 ? false : null"
                                trim />
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                </b-form>
            </validation-observer>
        </b-modal>

        <b-modal v-model="viewSeatModal" ok-title="Tutup"
            :busy="isSubmitModal" centered no-close-on-backdrop ok-only
            :title="`View Seat - ${attendeeSeat.name}`">
            
            <template v-if="attendeeSeat.seats.length > 0">
                <ul>
                    <li v-for="(seat, index) in attendeeSeat.seats">{{ seat }}</li>
                </ul>
            </template>
            <template v-else>
                <p>Tempat duduk belum dimapping</p>
            </template>

        </b-modal>

        <b-modal v-model="checkinModal" ok-title="Check In" @hidden="resetModal" @ok="handleCheckinAttendee"
            :busy="isSubmitModal" centered no-close-on-backdrop ok-only
            :title="`Check In Attendee`">
            
            <v-select v-model="formCheckin.pax" :options="paxInAccountOption" :reduce="(label) => label.value" :clearable="false" />

        </b-modal>

    </div>

</template>

<script>
import { BCard, BAvatar, BMedia, BRow, BLink, BDropdown, BDropdownItem, BPagination, BTable, BCol, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import vSelect from 'vue-select'
import { getDetail, postAttendee, deleteAttendeeNetwork, generateOpenRegistrationLink, sendBarcode, postAddAttendee, downloadBarcode } from '@/network/event-open-registration'
import flatPickr from 'vue-flatpickr-component'
import useAttendeeList from './useAttendeeList'
import { hasPermission } from '@/auth/utils'
import checkInBarcodeSidebar from './checkInBarcodeSidebar.vue'
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'

export default {
    components: {
        BRow,
        BMedia,
        BAvatar,
        BPagination,
        BDropdownItem,
        BDropdown,
        BLink,
        BCol,
        BTable,
        BCard,
        BForm,
        BFormGroup,
        BFormInput,
        BAlert,
        BFormInvalidFeedback,
        BButton,
        BSpinner,
        BInputGroup,
        BInputGroupAppend,
        BFormCheckboxGroup,
        BFormCheckbox,
        vSelect,
        flatPickr,
        checkInBarcodeSidebar,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    setup() {
        const isAddAttendeeSidebarActive = ref(false)
        const isBarcodeSidebarActive = ref(false)
        const resolveCheckIn = (checkIn) => {
            if (checkIn == 0) return "Check In"
            return "Update"
        }
        const resolveUserRoleVariant = (item) => {
            if (item.total_attendance == 0)
                return 'danger'
            if (item.total_checkin == item.total_attendance)
                return 'success'
            return 'default'
        }
        const {
            fetchUsers,
            tableColumns,
            perPage,
            currentPage,
            totalUsers,
            dataMeta,
            perPageOptions,
            searchQuery,
            sortBy,
            eventId,
            isSortDirDesc,
            refUserListTable,
            permissionList,
            refetchData,
            resolveGender,

        } = useAttendeeList()
        return {
            resolveUserRoleVariant,
            resolveCheckIn,
            fetchUsers,
            tableColumns,
            perPage,
            currentPage,
            totalUsers,
            dataMeta,
            perPageOptions,
            searchQuery,
            sortBy,
            isSortDirDesc,
            refUserListTable,
            permissionList,
            refetchData,
            resolveGender,
            avatarText, formatDateTime, formatDate,
            eventId,
            hasPermission,
            isAddAttendeeSidebarActive,
            isBarcodeSidebarActive
        }
    },
    data() {
        const event = {}
        const uuid = this.$route.params.id
        this.eventId = uuid
        getDetail(uuid).then(response => { this.event = response.data }).catch(error => { })
        const refreshDataPage = () => {
            this.refetchData()
            getDetail(this.eventId).then(response => { this.event = response.data }).catch(error => { })
        }
        return {
            refreshDataPage,
            password: '',
            passwordFieldTypeNew: 'password',
            isButtonLoading: false,
            required,
            min,
            numeric,
            email,
            event,
            formData: {},
            actionCIButton: false,
            addAttendeeModal: false,
            isSubmitModal: false,
            viewSeatModal: false,
            attendeeSeat: { seats:[] },
            checkinModal: false,
            paxInAccountOption: [],
            formCheckin: {}
        }
    },
    methods: {
        rowVariant(item) {
            if(item) {
                if (item.actual_pax == item.pax) {
                    return 'table-success'
                }
            }
        },
        generateOpenRegistrationLink() {
            this.$swal({
                title: `Apakah anda yakin akan membuat link registrasi?`,
                text: "Link akan di buat QR Code dan di download",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, buat sekarang!',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    generateOpenRegistrationLink(this.eventId).then(response => {
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
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                    })
                }
            })
        },
        deleteAttendee(item) {
            this.$swal({
                title: `Delete Attendee ${item.name}?`,
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
                    deleteAttendeeNetwork(item.id).then(response => {
                        this.refreshDataPage()
                    })
                }
            })
        },
        downloadBarcode(attendee) {
            getParticipantBarcode(attendee.participant_id).then(response => {
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
            }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
        },
        attendeeAction(attendee) {
            this.paxInAccountOption = []
            this.checkinModal = true
            for (let index = 1; index <= attendee.pax; index++) {
                this.paxInAccountOption.push({
                    label: index,
                    value: index
                })
            }
            this.formCheckin.id = attendee.id
            // this.actionCIButton = true
            // const vForm = {}
            // vForm['id'] = attendee.id
            // vForm['act'] = 'checkin'
            // postAttendee(vForm).then(response => {
            //     this.actionCIButton = false
            //     this.refreshDataPage()
            // })
            // .catch(error => {
            //     console.log(error)
            //     this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            //     this.actionCIButton = false
            // })
        },
        sendBarcode(attendee) {
            this.isButtonLoading = true
            const vForm = {}
            vForm['id'] = attendee.id
            sendBarcode(vForm).then(response => {
                this.$bvToast.toast(`Success: Barcode sudah dikirim`, { title: `Success`, variant: 'success', toaster: 'b-toaster-top-center', solid: true })
                this.isButtonLoading = false
            })
            .catch(error => {
                this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                this.isButtonLoading = false
            })
        },
        sendAllBarcode() {
            this.$swal({
                title: `Apakah anda yakin akan mengirim barcode ke seluruh participant?`,
                text: "Barcode akan di kirim ke seluruh participant",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim sekarang!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.isButtonLoading = true
                    const vForm = {}
                    vForm['event_id'] = this.eventId
                    vForm['send_to_all'] = true
                    sendBarcode(vForm).then(response => {
                        this.$bvToast.toast(`Success: Barcode sudah dikirim`, { title: `Success`, variant: 'success', toaster: 'b-toaster-top-center', solid: true })
                        this.isButtonLoading = false
                    })
                    .catch(error => {
                        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        this.isButtonLoading = false
                    })
                }
            })
        },
        resetModal() {
            this.refetchData()
            this.formCheckin = {}
        },
        addAttendee() {
            this.addAttendeeModal = true
        },
        editAttendee(item) {
            this.addAttendeeModal = true
            this.formData = item
            this.formData['event_id'] = this.eventId
            this.formData['update'] = true
        },
        handleAddAttendee(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitAddAttendance()
        },
        onSubmitAddAttendance() {
            this.$refs.refObsForm.validate().then(success => {
                if (!success) return
                this.isSubmitModal = true
                this.formData['event_id'] = this.eventId
                postAddAttendee(this.formData).then(response => {
                    this.addAttendeeModal = false
                    this.isSubmitModal = false
                    this.$bvToast.toast('Data has been added successfully', {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                    this.refreshDataPage()
                })
                .catch(error => {
                    this.isSubmitModal = false
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors)
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data)
                    }
                })
            })
        },
        viewSeats(item) {
            this.viewSeatModal = true
            this.attendeeSeat = item
        },
        downloadAllBarcode() {
            this.$swal({
                title: `Apakah anda yakin akan mendownload semua barcode?`,
                text: "Barcode akan digenerate, mohon menunggu",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, download semua!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.isButtonLoading = true
                    const vForm = {}
                    vForm['event_id'] = this.eventId
                    vForm['download_all'] = true
                    downloadBarcode(vForm).then(response => {
                        this.isButtonLoading = false
                        window.location = response.data.downloadLink
                    })
                    .catch(error => {
                        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        this.isButtonLoading = false
                    })
                }
            })
        },
        handleCheckinAttendee(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitCheckinAttendance()
        },
        onSubmitCheckinAttendance() {
            this.isSubmitModal = true
            this.formData['event_id'] = this.eventId
            const vForm = {}
            vForm['id'] = this.formCheckin.id
            vForm['pax'] = this.formCheckin.pax
            vForm['act'] = 'checkin'
            postAttendee(vForm).then(response => {
                this.checkinModal = false
                this.isSubmitModal = false
                this.$bvToast.toast('Data has been added successfully', {
                    title: `Success`,
                    variant: 'primary',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
                this.refreshDataPage()
            })
            .catch(error => {
                this.isSubmitModal = false
                this.$bvToast.toast(error, {
                    title: `Error`,
                    variant: 'danger',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
            })
        },
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
