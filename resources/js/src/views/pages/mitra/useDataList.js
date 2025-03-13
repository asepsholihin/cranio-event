import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/mitra'

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
    { key: 'join_date', sortable: true },
    { key: 'total_sales_pax', sortable: true, label: 'Total Pax', tdClass: 'text-center' },
    { key: 'total_sales_transaction', sortable: true, label: 'Total Transaction', tdClass: 'text-right' },
    { key: 'total_fee', sortable: true, tdClass: 'text-right' },
    { key: 'total_fee_paid', sortable: true, tdClass: 'text-right' },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)

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
        const { data, total } = response.data
        
        totalUsers.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Mitra list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveGender = gender => {
    if (gender == '1') return 'Man'
    return 'Woman'
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
    resolveUserStatusVariant,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
