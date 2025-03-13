import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/crm-participant'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: false },
    { key: 'ji_code', sortable: false },
    { key: 'participant_level', sortable: false },
    { key: 'no_hp', sortable: false },
    { key: 'birth_date', sortable: false },
    { key: 'total_transaction', sortable: true },
    { key: 'total_trip', sortable: true },
    { key: 'latest_trip_name', sortable: true },
    { key: 'last_booking_order_no', sortable: true },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100, 500]
  const searchQuery = ref('')
  const sortBy = ref('')
  const isSortDirDesc = ref(true)
  const sortDataByFilter = ref(null)
  const genderFilter = ref(null)
  const totalTripFilter = ref(null)
  const trip = ref(null)
  const cityFilter = ref(null)
  const totalTransactionFilter = ref(null)
  const totalAccountFilter = ref(null)
  const parentAccountFilter = ref(null)
  const hasPhoneFilter = ref(null)
  const hasInstagramFilter = ref(null)
  const hasLinkedInFilter = ref(null)
  const needMergeFilter = ref(null)
  const oldDataFilter = ref(null)
  const packageFilter = ref(null)
  const ageFilter = ref(null)
  const jobFilter = ref(null)
  const provinceFilter = ref(null)
  const dateFilter = ref(null)
  const yearFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refUserListTable.value ? refUserListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalUsers.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, trip, cityFilter, genderFilter, sortDataByFilter, totalTripFilter, totalTransactionFilter, totalAccountFilter, parentAccountFilter, hasPhoneFilter, hasInstagramFilter, hasLinkedInFilter, packageFilter, ageFilter, jobFilter, provinceFilter, needMergeFilter, oldDataFilter, dateFilter, yearFilter], () => {
    refetchData()
  })

  const fetchUsers = (ctx, callback) => {
    getList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        sortByData: sortDataByFilter.value,
        gender: genderFilter.value,
        totalTrip: totalTripFilter.value,
        trip: trip.value,
        city: cityFilter.value,
        province: provinceFilter.value,
        totalTransaction: totalTransactionFilter.value,
        totalAccount: totalAccountFilter.value,
        parentAccount: parentAccountFilter.value,
        hasPhone: hasPhoneFilter.value,
        hasInstagram: hasInstagramFilter.value,
        hasLinkedIn: hasLinkedInFilter.value,
        needMerge: needMergeFilter.value,
        oldData: oldDataFilter.value,
        package: packageFilter.value,
        age: ageFilter.value,
        job: jobFilter.value,
        date: dateFilter.value,
        year: yearFilter.value
      },
    })
      .then(response => {
        const { data, total } = response.data

        totalUsers.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Participant list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveGender = gender => {
    if (gender === 1) return 'Man'
    return 'Woman'
  }


  const resolveUserRoleIcon = role => {
    if (role === 'subscriber') return 'UserIcon'
    if (role === 'author') return 'SettingsIcon'
    if (role === 'maintainer') return 'DatabaseIcon'
    if (role === 'editor') return 'Edit2Icon'
    if (role === 'admin') return 'ServerIcon'
    return 'UserIcon'
  }

  const resolveUserStatusVariant = status => {
    if (status === 'pending') return 'warning'
    if (status === 'active') return 'success'
    if (status === 'inactive') return 'secondary'
    return 'primary'
  }

  return {
    fetchUsers,
    tableColumns,
    perPage,
    currentPage,
    totalUsers,
    dataMeta,
    perPageOptions,
    searchQuery,
    trip,
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolveGender,
    resolveUserRoleIcon,
    resolveUserStatusVariant,
    refetchData,

    // Extra Filters
    genderFilter,
    sortDataByFilter,
    totalTripFilter,
    cityFilter,
    totalTransactionFilter,
    totalAccountFilter,
    parentAccountFilter,
    hasPhoneFilter,
    hasInstagramFilter,
    hasLinkedInFilter,
    needMergeFilter,
    oldDataFilter,
    packageFilter,
    ageFilter,
    jobFilter,
    provinceFilter,
    dateFilter,
    yearFilter
  }
}
