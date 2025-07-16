<template>
    <div>
        <add-attendee-sidebar :event-attendance="event" :is-add-attendee-sidebar-active.sync="isAddAttendeeSidebarActive"
            @refetch-data="refreshDataPage" v-if="hasPermission('event-attendance-add-or-edit')" />

        <check-in-barcode-sidebar :event-attendance="event" :is-barcode-sidebar-active.sync="isBarcodeSidebarActive"
            @refetch-data="refreshDataPage" v-if="hasPermission('event-attendance-add-or-edit')" />

        <b-card>
            <h3 class="text-default">{{ event.event }} - {{ event.name }}</h3>
            <h4>{{ formatDate(event.event_date) }} <b-button variant="link" @click="copyLinkDepatureConfirmation(event.slug)" class="text-small ml-2" v-if="event.slug">Copy Link Konfirmasi Keberangkatan</b-button></h4>
            <h4>Total Check In: <span class="align-text-top">{{ event.total_checkin }} of {{ event.total_attendance
            }}</span></h4>

            <b-row>
                <b-col cols="12">
                    <div class="d-flex align-items-center justify-content-end">
                        <b-button class="mr-1" variant="primary" @click="selectRowAction">
                            <template v-if="!isRowChecked">
                                <span class="text-nowrap">
                                    <feather-icon icon="CheckSquareIcon" size="14" /> Select All
                                </span>
                            </template>
                            <template v-else>
                                <span class="text-nowrap">
                                    <feather-icon icon="SquareIcon" size="14" /> Deselect All
                                </span>
                            </template>
                        </b-button>
                        <b-button variant="primary" @click="sendMultipleBarcode()" class="mr-1"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">Kirim Barcode</span>
                        </b-button>
                        <b-button variant="primary" @click="isBarcodeSidebarActive = true" class="mr-1"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">Check In Barcode</span>
                        </b-button>
                        <b-button variant="primary" @click="isAddAttendeeSidebarActive = true" class="mr-1"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">Add Attendee</span>
                        </b-button>
                        <b-button variant="primary" class="mr-1" :disabled="isLoading" @click="exportRoomList()">
                            <span class="text-nowrap">Export Roomlist</span>
                        </b-button>
                        <div class="d-flex align-items-center justify-content-end">
                            <b-dropdown :disabled="isSubmitModal" right variant="gradient-primary" v-if="hasPermission('event-attendance-add-or-edit')">
                                <template #button-content>
                                    <feather-icon icon="DownloadCloudIcon" /> Export Attendance
                                </template>
                                <b-dropdown-item @click="exportAttendance('excel')">
                                    Excel
                                </b-dropdown-item>
                                <b-dropdown-item @click="exportAttendance('pdf')">
                                    PDF
                                </b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </div>
                </b-col>
            </b-row>
            <!-- Table Top -->
            <div class="my-2">
                <b-row>
                    <!-- Per Page -->
                    <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1">
                        <label>Show</label>
                        <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
                            class="per-page-selector d-inline-block mx-50" />
                        <label>entries</label>
                    </b-col>
                    <!-- Search -->
                    <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1">
                        <b-form-input v-model="searchQuery" debounce="350" class="d-inline-block mr-1" type="search"
                            placeholder="Search..." />
                        <v-select v-model="roomGroupFilter" :options="roomGroupOptions" :reduce="label => label.room_group" label="room_group" :clearable="true" class="w-100" placeholder="Filter by Room Group" />
                    </b-col>
                </b-row>

            </div>

            <b-table ref="refUserListTable" select-mode="multi" selected-variant="primary" selectable class="position-relative" :items="fetchUsers" responsive hover
                :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty
                empty-text="No matching records found" :sort-desc.sync="isSortDirDesc" :tbody-tr-class="rowVariant"
                @row-selected="onRowSelected">

                <template #cell(select)="{ rowSelected }">
                    <template v-if="rowSelected">
                        <feather-icon icon="CheckSquareIcon" size="20" class="align-end" />
                    </template>
                    <template v-else>
                        <feather-icon icon="SquareIcon" size="20" class="align-end" />
                    </template>
                </template>

                <!-- Column: Name -->
                <template #cell(name)="data">
                    <b-media vertical-align="center" class="text-nowrap">
                        <template #aside>
                            <b-avatar size="40" :src="data.item.profile_thumbnail" :text="avatarText(data.item.name)"
                                :variant="`light-primary`" />
                        </template>
                        <template v-if="data.item.departure_from">
                            <span class="badge badge-pill badge-success">Confirmed</span><br>
                        </template>
                        {{ (data.item.name_in_passport) ? data.item.name_in_passport.toUpperCase() : data.item.name.toUpperCase() }}<br />
                        <div class="text-warning" v-if="data.item.umroh_trip_id!=event.umroh_trip_id">{{ data.item.trip }}</div>
                        <div v-if="data.item.package_name">{{ data.item.package_name }}</div>
                        <small>Gender: {{ resolveGender(data.item.gender) }}</small><br>
                        <small>{{ data.item.whatsapp }}</small>
                    </b-media>
                </template>

                <!-- Column: Room Info -->
                <template #cell(room_number)="data">
                    <div v-if="data.item.room_group != null">
                        <p class="mb-50 text-nowrap">Room Group: <strong>{{ data.item.room_group }}</strong></p>
                    </div>
                    <div v-if="data.item.room_number != null">
                        <p class="mb-50 text-nowrap">Room Number: <strong>{{ data.item.room_number }}</strong></p>
                    </div>
                    <b-button variant="primary" size="sm" v-if="data.item.room_number == null" @click="setRoom(data.item)">Set Room</b-button>
                    <b-button variant="link" size="sm" v-if="data.item.room_number != null" @click="setRoom(data.item)">Change Room</b-button>
                </template>

                <!-- Column: Manasik Table -->
                <template #cell(manasik_table)="data">
                    <div class="text-center">
                        <span v-if="data.item.manasik_table">{{ data.item.manasik_table }}<br></span>
                        <b-button size="sm" variant="primary" @click="showManasikTableForm(data.item)"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <feather-icon icon="EditIcon" class="d-inline" />
                        </b-button>
                    </div>
                </template>

                <!-- Column: Manasik Table -->
                <template #cell(departure_from)="data">
                    <div class="text-center">
                        {{ data.item.departure_from }}
                    </div>
                    <div class="text-center mt-25" v-if="data.item.departure_from">
                        <b-button variant="outline-danger" size="sm" @click="resetDepartureConfirmation(data.item)">Reset</b-button>
                    </div>
                </template>

                <!-- Column: Manasik Table -->
                <template #cell(departure_from_update)="data">
                    <div class="text-center">
                        {{ data.item.departure_from_update }}
                    </div>
                </template>

                <!-- Column: Actions -->
                <template #cell(actions)="data">
                    <div class="text-nowrap">
                        <b-button size="sm" :variant="(!data.item.check_in_at) ? 'primary' : 'secondary'"
                            :disabled="actionCIButton" @click="attendeeAction(data.item)"
                            v-if="hasPermission('event-attendance-add-or-edit')">
                            <span class="text-nowrap">{{ resolveCheckIn(data.item.check_in_at) }}</span>
                        </b-button>
                        <b-dropdown variant="link" no-caret>
                            <template #button-content>
                                <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
                            </template>
                            <b-dropdown-item @click="sendBarcode(data.item)"
                                v-if="hasPermission('event-attendance-add-or-edit')">
                                <feather-icon icon="SendIcon" />
                                <span class="align-middle ml-50">Kirim Ulang QR Code</span>
                            </b-dropdown-item>
                            <b-dropdown-item variant="danger" @click="deleteAttendee(data.item)"
                                v-if="hasPermission('event-attendance-add-or-edit')">
                                <feather-icon icon="Trash2Icon" />
                                <span class="align-middle ml-50">Delete Attendee</span>
                            </b-dropdown-item>
                        </b-dropdown>
                    </div>
                </template>

                <!-- Column: Manasik Online -->
                <template #cell(manasik_online)="data">
                    <template v-for="(row, index) in data.item.manasik_online">
                        <div v-if="row.session" class="text-nowrap text-uppercase">
                            {{ row.session }} : {{ formatDateTimeShort(row.check_in_at_online) }}
                        </div>
                    </template>
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
                    <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-end">

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

        <b-modal v-model="formManasikTableModal" ok-title="Save" @hidden="resetModal" @ok="handleSubmitManasikTable"
            :busy="isSubmitModal" centered no-close-on-backdrop ok-only>
            <template #modal-title>
                <h3>Nomor Table Manasik</h3>
            </template>
            <validation-observer ref="refManasikForm">
                <b-form class="p-2" @submit.prevent="onSubmitManasikTable">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>

                    <b-row>
                        <b-col cols="12">
                            <label for="">Nama Participant</label>
                            <p class="font-weight-bold">{{ formManasikTable.name }}</p>
                        </b-col>
                        <b-col cols="12">
                            <validation-provider #default="{ errors }" name="Table Number" vid="manasik_table">
                                <b-form-group label="Table Number">
                                    <b-form-input type="text" v-model="formManasikTable.manasik_table"
                                        :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                </b-form>
            </validation-observer>
        </b-modal>

        <b-modal v-model="setMultipleManasikTableModal" ok-title="Save" @hidden="resetModal" @ok="handleSubmitMultipleManasikTable"
            :busy="isSubmitModal" centered no-close-on-backdrop ok-only>
            <template #modal-title>
                <h3>Nomor Table Manasik</h3>
            </template>
            <validation-observer ref="refMultipleManasikForm">
                <b-form class="p-2" @submit.prevent="onSubmitMultipleManasikTable">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>

                    <b-row>
                        <b-col cols="12">
                            <label for="">Nama Participant</label>
                            <template v-if="selectedNames.length > 0">
                                <ol class="pl-2">
                                    <li v-for="name in selectedNames">{{ name }}</li>
                                </ol>
                            </template>
                        </b-col>
                        <b-col cols="12">
                            <validation-provider #default="{ errors }" name="Table Number" vid="manasik_table">
                                <b-form-group label="Table Number">
                                    <b-form-input type="text" v-model="formManasikTable.manasik_table"
                                        :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                </b-form>
            </validation-observer>
        </b-modal>

        <b-modal v-model="formDepartureConfirmationModal" ok-title="Save" @hidden="resetModal"
            @ok="handleSubmitDepartureConfirmation" :busy="isSubmitModal" centered no-close-on-backdrop ok-only>
            <template #modal-title>
                <h3>Konfirmasi Keberangkatan</h3>
            </template>
            <validation-observer ref="refManasikForm">
                <b-form class="p-2" @submit.prevent="onSubmitManasikTable">
                    <validation-provider #default="{ errors }" vid="message">
                        <b-alert variant="danger" show v-if="errors[0]">
                            <div class="alert-body">
                                {{ errors[0] }}
                            </div>
                        </b-alert>
                    </validation-provider>

                    <b-row>
                        <b-col cols="12">
                            <label for="">Nama Participant</label>
                            <p class="font-weight-bold">{{ formDepartureConfirmation.name }}</p>
                        </b-col>
                        <b-col cols="12">
                            <validation-provider #default="{ errors }" name="Berangkat Dari" vid="departure_from_update">
                                <b-form-group label="Berangkat Dari:">
                                    <v-select v-model="formDepartureConfirmation.departure_from_update"
                                        :options="hotelOptions" :clearable="false" :reduce="label => label.value"
                                        :state="errors.length > 0 ? false : null" trim />
                                    <b-form-invalid-feedback>
                                        {{ errors[0] }}
                                    </b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>
                    </b-row>
                </b-form>
            </validation-observer>
        </b-modal>

        <b-modal v-model="setRoomModal" :busy="isSubmitModal" @hidden="resetModal" no-close-on-backdrop @ok="handleOkSetRoom" ok-title="Submit">
            <template #modal-title>
                <h4>Set Room Number</h4>
            </template>
            <validation-observer ref="refObsForm">
                <!-- Form -->
                <b-form @submit.prevent="onSubmitSetRoom" @reset.prevent="resetModal">
                <validation-provider #default="{ errors }" vid="message">
                    <b-alert variant="danger" show v-if="errors[0]">
                    <div class="alert-body">
                        {{ errors[0] }}
                    </div>
                    </b-alert>
                </validation-provider>

                <validation-provider #default="{ errors }" name="Room Number" vid="room_number">
                    <b-form-group label="Room Number">
                    <b-form-input v-model="formData.room_number" name="room_number" :state="errors.length > 0 ? false : null" trim />
                    <b-form-invalid-feedback>
                        {{ errors[0] }}
                    </b-form-invalid-feedback>
                    </b-form-group>
                </validation-provider>

                <validation-provider #default="{ errors }" name="Received By" vid="received_by">
                    <b-form-group label="Received By">
                    <b-form-input v-model="formData.received_by" name="received_by" :state="errors.length > 0 ? false : null" trim />
                    <b-form-invalid-feedback>
                        {{ errors[0] }}
                    </b-form-invalid-feedback>
                    </b-form-group>
                </validation-provider>

                <b-media class="mb-2">
                    <template #aside>
                    <b-avatar :src="formData.evidence" :text="avatarText('NA')" size="90px" rounded />
                    </template>
                    <div class="d-flex flex-wrap">
                    <b-button v-if="hasPermission('booking-add-or-edit')" variant="primary" size="sm" @click="$refs.refInputEl.click()">
                        <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                        <span class="d-none d-sm-inline">Upload Evidence</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>
                    </div>
                    <div class="mt-1 text-muted">Max Size: 5MB</div>
                </b-media>
                </b-form>
            </validation-observer>
        </b-modal>
    </div>
</template>

<script>
import { BCard, BAvatar, BMedia, BRow, BLink, BDropdown, BDropdownItem, BPagination, BTable, BCol, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import vSelect from 'vue-select'
import { getDetail, postAttendee, getParticipantBarcode, deleteAttendee, getParticipantDetail, updateManasikTable, generateEventLink, departureConfirmationByAdmin, exportDepartureUpdate, generateDepartureConfirmationLink, exportData, sendParticipantBarcode, sendMultipleParticipantBarcode, resetDepartureConfirmation } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'
import useAttendeeList from './useAttendeeList'
import { hasPermission } from '@/auth/utils'
import addAttendeeSidebar from './addAttendeeSidebar.vue'
import checkInBarcodeSidebar from './checkInBarcodeSidebar.vue'
import { avatarText, formatDateTimeShort, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import { downloadTableNumber } from '@/network/equipment'
import { postAction, exportParticipantRoomList, getRoomGroups } from '@/network/participant'

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
        vSelect,
        flatPickr,
        addAttendeeSidebar,
        checkInBarcodeSidebar,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    setup() {
        const isAddAttendeeSidebarActive = ref(false)
        const isBarcodeSidebarActive = ref(false)
        const isPhotoBoothBarcodeSidebarActive = ref(false)
        const isImportSidebarActive = ref(false)

        const resolveCheckIn = (checkIn) => {
            if (!checkIn) return "Check In"
            return "Cancel"
        }
        const resolveUserRoleVariant = (item) => {
            if (item.total_attendance == 0)
                return 'danger'
            if (item.total_checkin == item.total_attendance)
                return 'success'
            return 'default'
        }
        const statusLinkConfirmOptions = [
            { label: 'Belum dikirim', value: 'empty' },
            { label: 'Gagal dikirim ke Participant', value: 'failed' },
            { label: 'Berhasil dikirim ke Qontak', value: 'sending' },
            { label: 'Berhasil dikirim ke Participant', value: 'delivered' }
        ]
        const hotelOptions = [
            { label: 'Rumah', value: 'Rumah' },
            { label: 'Penginapan lainnya', value: 'Penginapan lainnya' }
        ]
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
            bookingFilter,
            packageFilter,
            statusLinkConfirmFilter,
            roomGroupFilter
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
            avatarText, formatDateTimeShort, formatDate,
            eventId,
            hasPermission,
            isAddAttendeeSidebarActive,
            isBarcodeSidebarActive,
            isPhotoBoothBarcodeSidebarActive,
            isImportSidebarActive,
            bookingFilter,
            packageFilter,
            statusLinkConfirmFilter,
            roomGroupFilter,
            statusLinkConfirmOptions,
            hotelOptions
        }
    },
    data() {
        const roomGroupOptions = []
        const event = {}
        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        this.eventId = id
        getDetail(id).then(response => {
            this.event = response.data
            if(this.event.hotel_name) {
                this.hotelOptions.push({ label: this.event.hotel_name, value: this.event.hotel_name })
            }
        }).catch(error => { })
        const refreshDataPage = () => {
            this.refetchData()
            getDetail(this.eventId).then(response => {
                this.event = response.data
            }).catch(error => { })
        }
        getRoomGroups().then(response => {
            this.roomGroupOptions = response.data;
        }).catch(error => {  })
        
        return {
            refreshDataPage,
            isSubmitModal: false,
            isButtonLoading: false,
            required,
            min,
            numeric,
            email,
            event,
            formData: {},
            actionCIButton: false,
            selectedParticipant: {},
            formManasikTable: {},
            formManasikTableModal: false,
            formDepartureConfirmation: {},
            formDepartureConfirmationModal: false,
            selectedIds: [], selectedNames: [], selectedData: [],
            isRowChecked: false,
            setMultipleManasikTableModal: false,
            setRoomModal: false,
            isLoading: false,
            roomGroupOptions
        }
    },
    methods: {
        rowVariant(item) {
            if(item) {
                if (item.departure_from && item.departure_from_update) {
                    if (item.departure_from !== item.departure_from_update) {
                        return 'table-danger'
                    }
                }
                if (item.check_in_at) {
                    return 'table-success'
                }
            }
        },
        onRowSelected(items) {
            const selectedIds = []
            const selectedNames = []
            const selectedData = []
            items.forEach(function (item) {
                selectedIds.push(item.participant_id)
                selectedNames.push(item.name)
                selectedData.push(item)
            })

            this.selectedData = selectedData
            this.selectedIds = selectedIds
            this.selectedNames = selectedNames

            if (items.length > 0) {
                this.isRowChecked = true
            } else {
                this.isRowChecked = false
            }
        },
        selectRowAction() {
            if (this.isRowChecked)
                this.$refs.refUserListTable.clearSelected()
            else
                this.$refs.refUserListTable.selectAllRows()
        },
        exportDepartureUpdate() {
            exportDepartureUpdate({ eventId: this.eventId }).then(response => {
                window.location = response.data.downloadLink
            }).catch(error => {
                this.isSubmitModal = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        showManasikTableForm(item) {
            getParticipantDetail(item.participant_id).then(response => {
                this.formManasikTable.id = response.data.id
                this.formManasikTable.name = response.data.name
                this.formManasikTable.umroh_trip_id = this.event.umroh_trip_id
                this.formManasikTable.manasik_table = response.data.manasik_table
                this.formManasikTableModal = true
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        handleSubmitManasikTable(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitManasikTable()
        },
        onSubmitManasikTable() {
            this.$refs.refManasikForm.validate().then(success => {
                if (!success) return
                this.isSubmitModal = true
                updateManasikTable(this.formManasikTable).then(response => {
                    this.formManasikTableModal = false
                    this.$swal({ icon: 'success', title: 'Success', text: `Manasik table has been changed successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                    this.refetchData()
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                    this.isSubmitModal = false
                })
            })
        },
        setRoom(item) {
            this.setRoomModal = true
            this.formData = {
                id: item.participant_id,
                room_number: item.room_number,
                received_by: item.received_by,
            }
        },
        inputImageRenderer() {
            this.formData.file_evidence = this.$refs.refInputEl.files[0]
            const file = this.$refs.refInputEl.files[0]
            const reader = new FileReader()

            reader.addEventListener(
                'load',
                () => {
                this.formData = {
                    ...this.formData,
                    evidence: reader.result
                }
                },
                false,
            )

            if (file) {
                reader.readAsDataURL(file)
            }
        },
        handleOkSetRoom(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitSetRoom()
        },
        onSubmitSetRoom() {
            this.$refs.refObsForm.validate().then((success) => {
                if (!success) return;
                this.isSubmitModal = true
                const vForm = new FormData()
                for (var key in this.formData) {
                if (key == 'evidence')
                    continue
                if (this.formData[key] != null)
                    vForm.append(key, this.formData[key])
                }
                vForm.append('set_room', true)
                postAction(vForm).then(response => {
                    this.$bvToast.toast(`Booking has been updated successfully`, {
                        title: `Success`,
                        variant: 'primary',
                        toaster: 'b-toaster-top-center',
                        solid: true,
                    })
                    this.setRoomModal = false
                    this.isSubmitModal = false
                    this.refetchData()
                })
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors)
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data)
                    }
                    this.isSubmitModal = false
                })
            })
        },
        resetModal() {
            this.selectedParticipant = null
            this.selectedIds = []
            this.selectedNames = []
            this.selectedData = []
            this.formManasikTable = {}
            this.formDepartureConfirmation = {}
            this.isSubmitModal = false
        },
        togglePasswordNew() {
            this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
        },
        resetUserData() {
            for (var key in this.formData) {
                this.formData[key] = null;
            }
            this.$refs.refObsForm.reset()
        },
        deleteAttendee(item) {
            var vForm = {
                event_id: item.event_id,
                participant_id: item.participant_id
            }
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
                    deleteAttendee(vForm).then(response => {
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
        sendBarcode(attendee) {
            this.$swal({
                title: `Apakah anda yakin akan mengirim barcode ke participant ${attendee.name}?`,
                text: "Barcode akan dikirim ke participant melalui email",
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
                    const form = {}
                    form.id = this.eventId
                    form.participantId = attendee.participant_id
                    sendParticipantBarcode(form).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `Barcode sudah dikirim ke participant`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        navigator.clipboard.writeText(response.data)
                    }).catch(error => {
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        this.isSubmitModal = false
                    })
                }
            })
        },
        sendMultipleBarcode() {
            this.$swal({
                title: `Apakah anda yakin akan mengirim barcode ke participant yang ditandai?`,
                text: "Barcode akan dikirim ke participant melalui email",
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
                    const form = {}
                    form.id = this.eventId
                    if(this.selectedIds.length > 0) {
                        form.participantIds = this.selectedIds
                    }
                    sendMultipleParticipantBarcode(form).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `Barcode sudah dikirim ke participant`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        navigator.clipboard.writeText(response.data)
                    }).catch(error => {
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        this.isSubmitModal = false
                    })
                }
            })
        },
        attendeeAction(attendee) {
            this.actionCIButton = true
            const vForm = {}
            vForm['participant_id'] = attendee.participant_id
            vForm['event_id'] = this.event.id
            vForm['act'] = 'checkin'
            postAttendee(vForm).then(response => {
                this.actionCIButton = false
                this.refreshDataPage()
            })
                .catch(error => {
                    console.log(error)
                    this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    this.actionCIButton = false
                })
        },
        showDepartureConfirmationForm(item) {
            getParticipantDetail(item.participant_id).then(response => {
                this.formDepartureConfirmation.id = response.data.id
                this.formDepartureConfirmation.name = response.data.name
                this.formDepartureConfirmation.event_id = this.event.id
                this.formDepartureConfirmation.umroh_trip_id = this.event.umroh_trip_id
                this.formDepartureConfirmation.departure_from = response.data.departure_from
                this.formDepartureConfirmationModal = true
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        handleSubmitDepartureConfirmation(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitDepartureConfirmation()
        },
        onSubmitDepartureConfirmation() {
            this.$refs.refManasikForm.validate().then(success => {
                if (!success) return
                this.isSubmitModal = true
                departureConfirmationByAdmin(this.formDepartureConfirmation).then(response => {
                    this.formDepartureConfirmationModal = false
                    this.$swal({ icon: 'success', title: 'Success', text: `Confirmation has been changed successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                    this.refetchData()
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                    this.isSubmitModal = false
                })
            })
        },
        copyLinkDepatureConfirmation(slug) {
            var link = "https://www.jejakimani.com/departure-confirmation/" + slug
            navigator.clipboard.writeText(link)
            this.$bvToast.toast(`Success: Link sudah dicopy`, {
                title: `Success`,
                variant: "success",
                toaster: "b-toaster-top-center",
                solid: true,
            });
        },
        generateDepartureConfirmationLink() {
            this.$swal({
                title: `Apakah anda yakin akan membuat link konfirmasi keberangkatan?`,
                text: "Link akan di buat QR Code dan di download",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, buat sekarang!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    generateDepartureConfirmationLink(this.eventId).then(response => {
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
                        this.isSubmitModal = false
                    })
                }
            })
        },
        generateEventManasikOnline() {
            var value_session = 1
            if(this.event.session) {
                value_session = parseInt(this.event.session) + 1
            }
            this.$swal({
                title: `Apakah anda yakin akan generate link untuk [SESI `+value_session+`] manasik online?`,
                text: "Link akan otomatis tersalin",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, buat sekarang!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    const form = {}
                    form.id = this.eventId
                    form.manasik_online = true
                    generateEventLink(form).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `Event Link generated, link copied`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        navigator.clipboard.writeText(response.data)
                    }).catch(error => {
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        this.isSubmitModal = false
                    })
                }
            })
        },
        generateEventLink() {
            this.$swal({
                title: `Apakah anda yakin akan mengirim link konfirmasi ke seluruh participant yang ditandai?`,
                text: "Link akan di kirim ke seluruh participant yang ditandai",
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
                    const form = {}
                    form.id = this.eventId
                    if(this.selectedIds.length > 0) {
                        form.participantIds = this.selectedIds
                    }
                    generateEventLink(form).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `Event Link generated, link copied`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        navigator.clipboard.writeText(response.data)
                        this.refetchData()
                    }).catch(error => {
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        this.isSubmitModal = false
                    })
                }
            })
        },
        generateEventLinkPartial(item) {
            this.$swal({
                title: `Apakah anda yakin akan mengirim link konfirmasi?`,
                text: "Link akan di kirim ke " + item.name,
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
                    const form = {}
                    form.id = this.eventId
                    form.participantId = item.participant_id
                    generateEventLink(form).then(response => {
                        this.$swal({ icon: 'success', title: 'Success', text: `Event Link sended, link copied`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                        navigator.clipboard.writeText(response.data)
                    }).catch(error => {
                        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                        this.isSubmitModal = false
                    })
                }
            })
        },
        exportRoomList() {
            this.isLoading = true
            const vForm = {}
            vForm.eventId = this.eventId
            exportParticipantRoomList(vForm).then(response => {
                this.isLoading = false
                const fileURL = window.URL.createObjectURL(new Blob([response.data]))
                const fileLink = document.createElement('a')
                const contentDisposition = response.headers['content-disposition']
                fileLink.href = fileURL;
                let fileName = 'unknown';
                if (contentDisposition) {
                    contentDisposition.replace(/['"]+/g, '')
                    const fileNameMatch = contentDisposition.match(/filename=(.+)/);
                    if (fileNameMatch.length === 2)
                        fileName = fileNameMatch[1];
                }
                fileLink.setAttribute('download', fileName);
                document.body.appendChild(fileLink);
                fileLink.click();
            }).catch(error => {
                this.isLoading = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        exportAttendance(type) {
            this.isSubmitModal = true
            exportData({ eventId: this.eventId, type: type }).then(response => {
                this.isSubmitModal = false
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
                this.isSubmitModal = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        setManasikTable() {
            this.setMultipleManasikTableModal = true
        },
        handleSubmitMultipleManasikTable(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.onSubmitMultipleManasikTable()
        },
        onSubmitMultipleManasikTable() {
            this.$refs.refMultipleManasikForm.validate().then(success => {
                if (!success) return
                this.isSubmitModal = true
                this.formManasikTable.participantIds = this.selectedIds
                this.formManasikTable.event_id = this.event.id
                this.formManasikTable.umroh_trip_id = this.event.umroh_trip_id
                updateManasikTable(this.formManasikTable).then(response => {
                    this.setMultipleManasikTableModal = false
                    this.$swal({ icon: 'success', title: 'Success', text: `Manasik table has been changed successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                    this.refetchData()
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                    this.isSubmitModal = false
                })
            })
        },
        downloadTableNumber() {
            this.isSubmitModal = true
            downloadTableNumber(this.event.umroh_trip_id).then(response => {
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
                this.isSubmitModal = false
            }).catch(error => { 
                this.isSubmitModal = false
                this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) 
            })
        },
        resetDepartureConfirmation(item) {
            var vForm = {
                event_id: item.event_id,
                participant_id: item.participant_id
            }
            this.$swal({
                title: `Reset Konfirmasi Keberangkatan ${item.name}?`,
                text: "It cannot be reverted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset it!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    resetDepartureConfirmation(vForm).then(response => {
                        this.refreshDataPage()
                    })
                }
            })
        }
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
.text-small {
    font-size: 12px;
}
</style>
