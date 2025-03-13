import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/crm-milad'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: true },
    { key: 'no_hp', sortable: false },
    { key: 'birth_date', sortable: false },
    { key: 'latest_trip_name', sortable: false },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100, 500]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)
  const umrohTripFilter = ref(null)
  const packageUmrohTripFilter = ref(null)
  const genderFilter = ref(null)
  const bookingFilter = ref(null)
  const startDateFilter = ref(null)
  const endDateFilter = ref(null)
  const umrohTripId = ref(null)

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

  watch([currentPage, perPage, searchQuery, startDateFilter, endDateFilter, statusFilter, genderFilter, umrohTripId], () => {
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
        status: statusFilter.value,
        gender: genderFilter.value,
        startDate: startDateFilter.value,
        endDate: endDateFilter.value,
        trip: umrohTripId.value,
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
    if (gender == '1') return 'Man'
    return 'Woman'
  }

  const resolveMarriedStatus = gender => {
    if (gender == '1') return 'Belum Menikah'
    return 'Sudah Menikah'
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
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolveGender,
    resolveMarriedStatus,
    resolveUserRoleIcon,
    resolveUserStatusVariant,
    refetchData,
    umrohTripId,

    // Extra Filters
    statusFilter,
    startDateFilter,
    endDateFilter,
    genderFilter,
  }
}
