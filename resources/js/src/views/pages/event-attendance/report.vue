<template>
    <div>
        <b-card>
            <h3 class="text-default text-transform: ;">{{ event.event }} {{ capitalize(onlineManasik) }} - {{ event.name }}</h3>
            <h4>{{ formatDate(event.event_date) }}</h4>
            <h4 v-if="onlineManasik == 'offline'">Total Check In: <span class="align-text-top">{{ event.total_checkin }} of {{ event.total_attendance
            }}</span></h4>

            <!-- Table Top -->
            <div class="my-3" v-if="onlineManasik == 'online'">
                <b-row>
                    <template v-for="(row, index) in chartManasik">
                        <b-col cols="12" md="4" v-if="!row.empty">
                            <h5 class="text-center">{{ row.title }}<br>{{ row.date }}</h5>
                            <apexchart type="donut" height="340" :options="row.chartOptions" :series="row.chartSeries" />
                        </b-col>
                    </template>
                </b-row>
            </div>

            <div class="my-3">
                <b-row>
                    <!-- Search -->
                    <b-col cols="12" md="2">
                        <b-button variant="primary" @click="exportData()">
                            <span class="text-nowrap">Export</span>
                        </b-button>
                    </b-col>
                    <b-col cols="12" md="3" v-if="onlineManasik == 'online'">
                        <v-select v-model="sessionFilter" :options="sessionOptions" placeholder="Session"
                            :clearable="true" :reduce="(label) => label.session" label="title" />
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
                    <b-col v-for="(row, index) in participantAttendeeList" :key="index" cols="4" class="mb-2">
                        <h4>Sudah Absen ({{ row.package_name }})</h4>
                        <ul class="list-unstyled box-attendance">
                            <li v-for="(value, ij) in row.participant" :key="ij">
                                {{ ij + 1 }}. {{ value.name }}
                            </li>
                        </ul>
                    </b-col>
                </b-row>
                <b-row class="mt-5">
                    <b-col v-for="(row, index) in participantUnAttendeeList" :key="index" cols="4" class="mb-2">
                        <h4>Belum Absen ({{ row.package_name }})</h4>
                        <ul class="list-unstyled box-attendance">
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
import { getDetail, postAttendee, getParticipantBarcode, deleteAttendeeNetwork, getParticipantDetail, updateManasikTable, getChartAttendance, getChartAttendanceConfirmationByPackage, exportData, getChartManasikOnline } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'
import useAttendeeList from './useAttendeeList'
import { hasPermission } from '@/auth/utils'
import { avatarText, formatDateTime, capitalize, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import { getBookingSearch } from '@/network/umroh-booking-seat'
import { until } from '@vueuse/core'

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
            avatarText, formatDateTime, formatDate, capitalize,
            hasPermission, eventId: 0
        }
    },
    watch: {
        sessionFilter: function (value) {
            this.getChartData()
        },
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
        const onlineManasik = this.$route.params.online || 'offline'
        if (id == 0) this.$router.back()
        this.eventId = id
        getDetail(id, {online: onlineManasik}).then(response => {
            this.event = response.data
            //this.buildBookingOption(this.event.umroh_trip_id)
            this.getChartData()
        }).catch(error => { })
        const refreshDataPage = () => {
            this.refetchData()
            getDetail(this.eventId, {online: onlineManasik}).then(response => {
                this.event = response.data
                //this.buildBookingOption(this.event.umroh_trip_id)
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
            chartManasik: [],
            onlineManasik,
            sessionOptions: [],
            sessionFilter: 1
        }
    },
    computed: {
    },
    methods: {
        getChartData() {
            this.participantAttendeeList = []
            this.participantUnAttendeeList = []
            getChartAttendance({ params: { eventId: this.eventId, booking: this.bookingFilter, online: this.onlineManasik, session: this.sessionFilter } }).then(response => {
                var total = 0;
                for (const row of response.data.data) {
                    total += row
                }

                this.chartOptions = {
                    title: {
                        text: 'Total Absensi',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-attendance',
                        toolbar: {
                            show: true
                        }
                    },
                    labels: response.data.categories,
                    legend: {
                        position: 'bottom',
                        offsetY: 0,
                        formatter: function(seriesName, opts) {
                            const value = opts.w.globals.series[opts.seriesIndex]
                            const percent = (value / total) * 100;
                            return seriesName + ":  " + percent.toFixed(1) +'%'
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function(dataPoint, { series, seriesIndex, dataPointIndex, w }) {
                                const value = dataPoint
                                const percent = (value / total) * 100;
                                return dataPoint + " (" + percent.toFixed(1) + "%)"
                            }
                        }
                    }
                }
                this.chartSeries = response.data.data

                response.data.participantPackages.forEach(element => {
                    const attended = { package_name: element.package_name, participant: [] }
                    const unAttended = { package_name: element.package_name, participant: [] }
                    element.participant.forEach(participant => {
                        if(this.onlineManasik == 'online') {
                            if (participant.check_in_at_online) {
                                let index = attended.participant.findIndex((item) => item.id === participant.id);
                                if (index === -1) {
                                    attended.participant.push(participant)
                                }
                            } else {
                                let index = unAttended.participant.findIndex((item) => item.id === participant.id);
                                if (index === -1) {
                                    unAttended.participant.push(participant)
                                }
                            }
                        } else {
                            if (participant.check_in_at) {
                                let index = attended.participant.findIndex((item) => item.id === participant.id);
                                if (index === -1) {
                                    attended.participant.push(participant)
                                }
                            } else if (participant.check_in_at == null && participant.check_in_at_online == null) {
                                let index = unAttended.participant.findIndex((item) => item.id === participant.id);
                                if (index === -1) {
                                    unAttended.participant.push(participant)
                                }
                            }
                        }
                    });
                    this.participantAttendeeList.push(attended)
                    this.participantUnAttendeeList.push(unAttended)
                });
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            if(this.onlineManasik == 'online') {
                getChartManasikOnline({ params: { eventId: this.eventId } }).then(response => {
                    this.chartManasik = response.data
                    this.sessionOptions = response.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            }
        },
        filterChart() {
            this.getChartData()
        },
        exportData() {
            exportData({ eventId: this.eventId }).then(response => {
                window.location = response.data.downloadLink
            }).catch(error => {
                this.isSubmitModal = false
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
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
.box-attendance {
    height: 500px;
    overflow: auto;
}
</style>
