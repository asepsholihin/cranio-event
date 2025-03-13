import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/user-platform'

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
    { key: 'email', sortable: true },
    { key: 'departments', sortable: true, label:'Department' },
    { key: 'access_status', sortable: true },
    { key: 'permission_list', sortable: false, label:'Permissions' },
    { key: 'actions', sortable: false },
  ]
  const perPage = ref(10)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const departmentFilter = ref(null)
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

  watch([currentPage, perPage, searchQuery, departmentFilter], () => {
    refetchData()
  })

  const fetchUsers = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        department: departmentFilter.value,
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
            title: 'Error fetching Participant list',
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

  const resolveDepartment = access_status => {
    if (access_status == '1') return 'Directors'
    if (access_status == '2') return 'Management'
    if (access_status == '3') return 'Sales'
    if (access_status == '4') return 'Document'
    if (access_status == '5') return 'Equipment'
    if (access_status == '6') return 'Handling'
    if (access_status == '7') return 'Finance'
    if (access_status == '8') return 'Head Branch'
    if (access_status == '9') return 'Sales Manager'
    return ''
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
    resolveDepartment,

    // Extra Filters
    departmentFilter,
  }
}
