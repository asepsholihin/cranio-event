import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/faq-category'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refItemCategoryListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: true, label: 'Category Name' },
    { key: 'slug', sortable: true, label: 'Slug' },
    { key: 'parent_category_name', sortable: true, label: 'Parent Category' },
    { key: 'created_at', sortable: true },
    { key: 'status', sortable: false },
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
    const localItemsCount = refItemCategoryListTable.value ? refItemCategoryListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalItemCategorys.value,
    }
  })

  const refetchData = () => {
    refItemCategoryListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchItemCategorys = (ctx, callback) => {
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
            title: 'Error fetching item category list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveItineraryStatusVariant = status => {
    if (status) return 'primary'
    return 'warning'
  }

  const resolveItineraryStatusName = status => {
    if (status) return 'Active'
    return 'Inactive'
  }

  return {
    fetchItemCategorys,
    tableColumns,
    perPage,
    currentPage,
    totalItemCategorys,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refItemCategoryListTable,

    resolveItineraryStatusVariant,
    resolveItineraryStatusName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
