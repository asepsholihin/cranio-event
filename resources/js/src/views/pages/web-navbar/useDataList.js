import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/web-navbar'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refNavbarListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'title', sortable: true },
    { key: 'parent_navbar', sortable: true, label: 'Parent Navbar' },
    { key: 'order', sortable: true },
    { key: 'url', sortable: true },
    { key: 'show', sortable: false, label: 'Show in Menu' },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalItemCategorys = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refNavbarListTable.value ? refNavbarListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalItemCategorys.value,
    }
  })

  const refetchData = () => {
    refNavbarListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchNavbars = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
      }})
      .then(response => {
        const { data, total } = response.data
        
        totalItemCategorys.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching navbar list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveShowVariant = status => {
    if (status) return 'primary'
    return 'warning'
  }

  const resolveShowName = status => {
    if (status) return 'Show'
    return 'Hidden'
  }

  return {
    fetchNavbars,
    tableColumns,
    perPage,
    currentPage,
    totalItemCategorys,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refNavbarListTable,

    resolveShowVariant,
    resolveShowName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
