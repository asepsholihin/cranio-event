<template>
    <div>
        <b-card>
            <h3 class="text-default">{{ event.event }} - {{ event.name }}</h3>
            <h4>{{ formatDate(event.event_date) }}</h4>
            <h4>Total Confirmation: <span class="align-text-top">{{ event.total_confirm }} of {{ event.total_attendance
            }}</span></h4>

            <!-- Table Top -->
            <div class="my-3">
                <b-row>
                    <!-- Search -->
                    <b-col cols="12" md="4">
                        <div class="d-flex align-items-center justify-content-end">
                            <v-select v-model="bookingFilter" @input="filterChart" :options="bookingOptions" class="w-100"
                                :reduce="val => val.value" placeholder="Booking Order" />
                        </div>
                    </b-col>
                </b-row>
            </div>

            <div>
                <b-row>
                    <b-col cols="12" md="4">
                        <apexchart type="donut" height="340" :options="chartOptions" :series="chartSeries" />
                    </b-col>
                    <b-col cols="12" md="4">
                        <apexchart type="donut" height="340" :options="chartUnconfirmedOptions"
                            :series="chartUnconfirmedSeries" />
                    </b-col>
                    <b-col cols="12" md="4">
                        <apexchart type="donut" height="340" :options="chartConfirmedOptions"
                            :series="chartConfirmedSeries" />
                    </b-col>
                </b-row>

                <b-row class="mt-5">
                    <b-col v-for="(row, index) in participantAttendeeList" :key="index" cols="4">
                        <h4>Sudah Konfirmasi ({{ row.package_name }})</h4>
                        <ul class="list-unstyled">
                            <li v-for="(value, ij) in row.participant" :key="ij">
                                {{ ij + 1 }}. {{ value.name }}
                            </li>
                        </ul>
                    </b-col>
                </b-row>
                <b-row class="mt-5">
                    <b-col v-for="(row, index) in participantUnAttendeeList" :key="index" cols="4">
                        <h4>Belum Konfirmasi ({{ row.package_name }})</h4>
                        <ul class="list-unstyled">
                            <li v-for="(value, i) in row.participant" :key="i">{{ i + 1 }}. {{ value.name }}</li>
                        </ul>
                    </b-col>
                </b-row>
            </div>

        </b-card>
    </div>
</template>

<script>
import { BCard, BAvatar, BMedia, BRow, BLink, BDropdown, BDropdownItem, BPagination, BTable, BCol, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import vSelect from 'vue-select'
import { getDetail, postAttendee, getParticipantBarcode, deleteAttendeeNetwork, getParticipantDetail, updateManasikTable, getChartAttendanceConfirmation, getChartAttendanceConfirmationByPackage } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'
import useAttendeeList from './useAttendeeList'
import { hasPermission } from '@/auth/utils'
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import { getBookingSearch } from '@/network/umroh-booking-seat'

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

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    setup() {
        return {
            avatarText, formatDateTime, formatDate,
            hasPermission, eventId: 0
        }
    },
    data() {
        const event = {}
        const bookingOptions = []
        const chartOptions = {}
        const chartSeries = []
        const chartUnconfirmedOptions = {}
        const chartUnconfirmedSeries = []
        const chartConfirmedOptions = {}
        const chartConfirmedSeries = []
        const participantAttendeeList = []
        const participantUnAttendeeList = []

        const id = parseInt(this.$route.params.id) || 0
        if (id == 0) this.$router.back()
        this.eventId = id
        getDetail(id).then(response => {
            this.event = response.data
            this.buildBookingOption(this.event.umroh_trip_id)
            this.getChartData()
        }).catch(error => { })
        const refreshDataPage = () => {
            this.refetchData()
            getDetail(this.eventId).then(response => {
                this.event = response.data
                this.buildBookingOption(this.event.umroh_trip_id)
            }).catch(error => { })
        }
        return {
            refreshDataPage,
            required,
            min,
            numeric,
            email,
            event,
            bookingFilter: "",
            bookingOptions,
            chartOptions,
            chartSeries,
            chartUnconfirmedOptions,
            chartUnconfirmedSeries,
            chartConfirmedOptions,
            chartConfirmedSeries,
            participantAttendeeList,
            participantUnAttendeeList,
        }
    },
    computed: {
    },
    methods: {
        getChartData() {
            getChartAttendanceConfirmation({ params: { eventId: this.eventId, booking: this.bookingFilter } }).then(response => {
                this.chartOptions = {
                    title: {
                        text: 'Total Konfirmasi',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-attendance-confirmation',
                        toolbar: {
                            show: true
                        }
                    },
                    labels: response.data.categories,
                }
                this.chartSeries = response.data.data

                response.data.participantPackages.forEach(element => {
                    const attended = { package_name: element.package_name, participant: [] }
                    const unAttended = { package_name: element.package_name, participant: [] }
                    element.participant.forEach(participant => {
                        if (participant.confirm_at) {
                            attended.participant.push(participant)
                        } else {
                            unAttended.participant.push(participant)
                        }
                    });
                    this.participantAttendeeList.push(attended)
                    this.participantUnAttendeeList.push(unAttended)
                });
                console.log(this.participantAttendeeList, this.participantUnAttendeeList)
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartAttendanceConfirmationByPackage({ params: { eventId: this.eventId, booking: this.bookingFilter } }).then(response => {
                this.chartUnconfirmedOptions = {
                    title: {
                        text: 'Total Belum Konfirmasi',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-attendance-unconfirm',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: response.data.colors,
                    labels: response.data.categories,
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                }
                this.chartUnconfirmedSeries = response.data.data_unconfirmed

                this.chartConfirmedOptions = {
                    title: {
                        text: 'Total Sudah Konfirmasi',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-attendance-confirm',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: response.data.colors,
                    labels: response.data.categories,
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                }
                this.chartConfirmedSeries = response.data.data_confirmed
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        filterChart() {
            this.getChartData()
        },
        buildBookingOption(umroh_trip_id) {
            this.bookingOptions = []
            if (umroh_trip_id) {
                getBookingSearch({
                    params: {
                        umrohTripId: umroh_trip_id,
                        // packageUmrohTripId: val
                    }
                }).then(response => {
                    const listOpt = []
                    response.data.forEach(function (item) {
                        listOpt.push({ 'label': item.order_no + ' - ' + item.name, 'value': item.order_no, 'notes': item.notes })
                    })
                    this.bookingOptions = listOpt
                }).catch(error => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors)
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data)
                    }
                })
            }
        },
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
