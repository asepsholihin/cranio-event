import { ref, watch, computed } from '@vue/composition-api'
import { getRawList } from '@/network/participant'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'ji_code', sortable: false, thClass: 'text-center text-nowrap', tdClass: 'text-center text-nowrap' },
    { key: 'title', sortable: false, tdClass: 'text-center text-nowrap' },
    { key: 'front_title', sortable: false, tdClass: 'text-center text-nowrap' },
    { key: 'name', sortable: true, tdClass: 'text-nowrap' },
    { key: 'name_in_passport', sortable: true, tdClass: 'text-nowrap' },
    { key: 'back_title', sortable: false, tdClass: 'text-center text-nowrap' },
    { key: 'nik', sortable: false, tdClass: 'text-nowrap' },
    { key: 'kitas_number', sortable: false, tdClass: 'text-nowrap' },
    { key: 'birth_date', sortable: false, tdClass: 'text-center text-nowrap' },
    { key: 'birth_place', sortable: false, tdClass: 'text-nowrap' },
    { key: 'no_hp', sortable: false, tdClass: 'text-center text-nowrap'},
    { key: 'email', sortable: false, tdClass: 'text-nowrap' },
    { key: 'no_passport', sortable: false, tdClass: 'text-nowrap' },
    { key: 'fathers_name', sortable: false, tdClass: 'text-nowrap' },
    { key: 'married_status', sortable: false, tdClass: 'text-nowrap' },
    { key: 'nationality', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_province', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_city', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_kecamatan', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_kelurahan', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_address', sortable: false, tdClass: 'text-nowrap' },
    { key: 'ktp_postalcode', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_province', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_city', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_kecamatan', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_kelurahan', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_address', sortable: false, tdClass: 'text-nowrap' },
    { key: 'home_postalcode', sortable: false, tdClass: 'text-nowrap' },
    { key: 'education', sortable: false, tdClass: 'text-nowrap' },
    { key: 'is_doctor', sortable: false, tdClass: 'text-nowrap' },
    { key: 'doctor_specialist', sortable: false, tdClass: 'text-nowrap' },
    { key: 'doctor_evidence', sortable: false, tdClass: 'text-nowrap' },
    { key: 'job', sortable: false, tdClass: 'text-nowrap' },
    { key: 'company_name', sortable: false, tdClass: 'text-nowrap' },
    { key: 'blood_type', sortable: false, tdClass: 'text-nowrap' },
    { key: 'chest_size', sortable: false, tdClass: 'text-nowrap' },
    { key: 'body_height', sortable: false, tdClass: 'text-nowrap' },
    { key: 'jacket_size', sortable: false, tdClass: 'text-nowrap' },
    { key: 'medical_record', sortable: false, tdClass: 'text-nowrap' },
    { key: 'emergency_contact', sortable: false, tdClass: 'text-nowrap' },
    { key: 'emergency_contact_name', sortable: false, tdClass: 'text-nowrap' },
    { key: 'emergency_relation', sortable: false, tdClass: 'text-nowrap' },
    { key: 'emergency_address', sortable: false, tdClass: 'text-nowrap' },
    { key: 'passport_issued_at', sortable: false, tdClass: 'text-nowrap' },
    { key: 'passport_published_date', sortable: false, tdClass: 'text-nowrap' },
    { key: 'passport_expired_date', sortable: false, tdClass: 'text-nowrap' },
    { key: 'passport_held_by', sortable: false, tdClass: 'text-nowrap' },
    { key: 'name_in_certificate', sortable: false, tdClass: 'text-nowrap' },
    { key: 'name_in_sandal_bag', sortable: false, tdClass: 'text-nowrap' },
  ]
  const perPage = ref(100)
  const totalUsers = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100, 500]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const genderFilter = ref(null)
  const duplicateFilter = ref(null)

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

  watch([currentPage, perPage, searchQuery, genderFilter, duplicateFilter], () => {
    refetchData()
  })

  const fetchUsers = (ctx, callback) => {
    getRawList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        gender: genderFilter.value,
        duplicate: duplicateFilter.value
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
            title: 'Error fetching Participant list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
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
    
    refetchData,

    // Extra Filters
    genderFilter,
    duplicateFilter
  }
}
