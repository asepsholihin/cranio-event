
import { hasPermission } from '@/auth/utils'
export default {
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  render(h) {
    const isHeaderRender = permissions => {
        for (let permission of permissions) {
            if (hasPermission(permission))
                return true
        }

        return false
    }

    if (! isHeaderRender(this.item.permissions)) {
        return
    }
    const span = h('span', {}, this.item.header)
    const icon = h('feather-icon', { props: { icon: 'MoreHorizontalIcon', size: '18' } })
    return h('li', { class: 'navigation-header text-truncate' }, [span, icon])
  },
}
