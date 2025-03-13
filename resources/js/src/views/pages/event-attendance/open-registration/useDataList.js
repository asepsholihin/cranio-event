import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/event-open-registration'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'total_check_in', label:'Attendee' },
    { key: 'name', sortable: true },
    { key: 'is_paid_event', label: 'Paid Event', thClass: 'text-center'},
    { key: 'location' },
    { key: 'event_date', sortable: true },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('event_date')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)
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

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter], () => {
    refetchData()
  })

  const fetchUsers = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        role: roleFilter.value,
        plan: planFilter.value,
        status: statusFilter.value,
      }})
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
            title: 'Error fetching Event list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
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
    currentPage,
    totalUsers,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,
    permissionList,

    resolveAccessStatus,
    resolvePermissions,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
