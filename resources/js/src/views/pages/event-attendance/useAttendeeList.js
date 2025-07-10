import { ref, watch, computed } from '@vue/composition-api'
import { getListAttendee } from '@/network/event-attendance'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'select', sortable: false },
    { key: 'name', sortable: true },
    { key: 'room_number', sortable: true, label: 'Room Info', thClass: 'text-nowrap' },
    { key: 'check_in_at', sortable: true, label: 'Waktu Absen', thClass: 'text-nowrap' },
    { key: 'account_hospital', sortable: true, label: 'Nama Rumah Sakit', thClass: 'text-nowrap' },
    { key: 'actions' },
  ]
  const perPage = ref(500)
  const totalUsers = ref(0)
  const eventId = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100, 500]
  const searchQuery = ref('')
  const sortBy = ref('')
  const isSortDirDesc = ref(false)
  const statusLinkConfirmFilter = ref(null)
  const bookingFilter = ref(null)
  const packageFilter = ref(null)
  const permissionList = ref([])

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

  watch([currentPage, perPage, searchQuery, statusLinkConfirmFilter, bookingFilter, packageFilter], () => {
    refetchData()
  })
  const resolveGender = gender => {
    if (gender == '1') return 'Man'
    return 'Woman'
  }
  const fetchUsers = (ctx, callback) => {
    getListAttendee({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        eventId: eventId.value,
        sortDesc: isSortDirDesc.value,
        statusLinkConfirm: statusLinkConfirmFilter.value,
        booking: bookingFilter.value,
        package: packageFilter.value,
      }
    })
      .then(response => {
        const { data, total, permissions } = response.data
        permissionList.value = permissions
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

  const resolveAccessStatus = access_status => {
    if (access_status == '1') return 'Active'
    return 'Disabled'
  }

  const resolvePermissions = permissionList => {
    return permissionList.join(", ")
  }

  return {
    fetchUsers,
    tableColumns,
    perPage,
    eventId,
    currentPage,
    totalUsers,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,
    permissionList,
    resolveGender,

    resolveAccessStatus,
    resolvePermissions,
    refetchData,

    // Extra Filters
    statusLinkConfirmFilter,
    bookingFilter,
    packageFilter
  }
}
