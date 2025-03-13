<template>
  <div
    class="main-menu menu-fixed menu-accordion menu-shadow"
    :class="[
      { 'expanded': !isVerticalMenuCollapsed || (isVerticalMenuCollapsed && isMouseHovered) },
      skin === 'light'|| skin === 'bordered' ? 'menu-light' : 'menu-dark'
    ]"
    @mouseenter="updateMouseHovered(true)"
    @mouseleave="updateMouseHovered(false)"
  >
    <!-- main menu header-->
    <div class="navbar-header expanded mb-1">
      <slot
        name="header"
        :toggleVerticalMenuActive="toggleVerticalMenuActive"
        :toggleCollapsed="toggleCollapsed"
        :collapseTogglerIcon="collapseTogglerIcon"
      >
        <ul class="nav navbar-nav flex-row align-items-center">

          <!-- Logo & Text -->
          <li class="nav-item mr-auto">
            <b-link
              class="navbar-brand"
              to="/"
            >
              <span class="brand-logo">
                <b-img
                  :src="appLogoImage"
                  alt="logo"
                />
              </span>
              <h2 class="brand-text">
                {{ appName }}
              </h2>
            </b-link>
          </li>

          <!-- Toggler Button -->
          <li class="nav-item nav-toggle">
            <b-link class="nav-link modern-nav-toggle">
              <feather-icon
                icon="XIcon"
                size="20"
                class="d-block d-xl-none"
                @click="toggleVerticalMenuActive"
              />
              <feather-icon
                :icon="collapseTogglerIconFeather"
                size="20"
                class="d-none d-xl-block collapse-toggle-icon"
                @click="toggleCollapsed"
              />
            </b-link>
          </li>
        </ul>
      </slot>
    </div>
    <!-- / main menu header-->

    <!-- Shadow -->
    <div
      :class="{'d-block': shallShadowBottom}"
      class="shadow-bottom"
    />

    <!-- main menu content-->
    <vue-perfect-scrollbar
      :settings="perfectScrollbarSettings"
      class="main-menu-content scroll-area"
      tagname="ul"
      @ps-scroll-y="evt => { shallShadowBottom = evt.srcElement.scrollTop > 0 }"
    >
    <!-- Search Menu -->
    <div class="w-100 p-1">
        <b-form-input class="w-100" v-model="titleMenu" @input="searchMenu" placeholder="search.." autocomplete="off"/>
        <div class="dropdown-search mt-1" id="dropdown-search" v-if="isActiveMenu">
            <ul>
                <li v-for="(item, index) in filteredItems" :key="index" @click="menuTo(item.route)">
                    <span class="text-dark">{{ item.name }}</span><br>
                    <span class="text-link-route">{{ item.route }}</span>
                </li>
            </ul>
        </div>
    </div>
    <vertical-nav-menu-items
        :items="navMenuItems"
        class="navigation navigation-main"
      />
    </vue-perfect-scrollbar>
    <!-- /main menu content-->
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { BLink, BImg, BFormInput} from 'bootstrap-vue'
import { provide, computed, ref } from '@vue/composition-api'
import useAppConfig from '@core/app-config/useAppConfig'
import { $themeConfig } from '@themeConfig'
import VerticalNavMenuItems from './components/vertical-nav-menu-items/VerticalNavMenuItems.vue'
import useVerticalNavMenu from './useVerticalNavMenu'

export default {
  components: {
    VuePerfectScrollbar,
    VerticalNavMenuItems,
    BLink,
    BFormInput,
    BImg,
  },
  props: {
    isVerticalMenuActive: {
      type: Boolean,
      required: true,
    },
    toggleVerticalMenuActive: {
      type: Function,
      required: true,
    },
    navMenuItems: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    const {
      isMouseHovered,
      isVerticalMenuCollapsed,
      collapseTogglerIcon,
      toggleCollapsed,
      updateMouseHovered,
    } = useVerticalNavMenu(props)

    const { skin } = useAppConfig()

    // Shadow bottom is UI specific and can be removed by user => It's not in `useVerticalNavMenu`
    const shallShadowBottom = ref(false)

    provide('isMouseHovered', isMouseHovered)

    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
    }

    const collapseTogglerIconFeather = computed(() => (collapseTogglerIcon.value === 'unpinned' ? 'CircleIcon' : 'DiscIcon'))

    // App Name
    const { appName, appLogoImage } = $themeConfig.app
    return {
      perfectScrollbarSettings,
      isVerticalMenuCollapsed,
      collapseTogglerIcon,
      toggleCollapsed,
      isMouseHovered,
      updateMouseHovered,
      collapseTogglerIconFeather,

      // Shadow Bottom
      shallShadowBottom,

      // Skin
      skin,

      // App Name
      appName,
      appLogoImage,
    }
  },
  data() {
    return {
      titleMenu:'',
      isActiveMenu:false,
      items:[],
      filteredItems: [] // This will store the filtered list
    }
  },
  methods: {
    searchMenu(){
        if(this.titleMenu==""){
            this.isActiveMenu = false
        }
        else{
            this.filterMenu()
            this.isActiveMenu = true
        }
    },
    menuTo(route){
        this.titleMenu = ""
        this.isActiveMenu = false
        this.$router.push({name : route});
    },
    filterMenu(){
       this.filteredItems = this.items.filter(item =>
        item.name.toLowerCase().includes(this.titleMenu.toLowerCase())
      );
    },
    handleClickOutside(event) {
        const element = document.getElementById('dropdown-search');
        // If the click is outside the element, close it
        if (element && !element.contains(event.target)) {
            this.titleMenu = ""
            this.isActiveMenu = false;
        }
    }
  },
  mounted() {
    const json = []
    for(var i=0; i<this.navMenuItems.length; i++){
        var checkHeader = this.navMenuItems[i].name = (this.navMenuItems[i].header) ? this.navMenuItems[i].header : ''
        if(checkHeader==""){
            this.navMenuItems[i].name = (this.navMenuItems[i].header) ? this.navMenuItems[i].header : this.navMenuItems[i].title
            json.push(this.navMenuItems[i])
            if(this.navMenuItems[i].children){
                for(var j=0; j<this.navMenuItems[i].children.length; j++){
                    var checkParentHeader = (this.navMenuItems[i].children[j].header) ? this.navMenuItems[i].children[j].header : ''
                    if(checkParentHeader == ''){
                        this.navMenuItems[i].children[j].name = (this.navMenuItems[i].children[j].header) ? this.navMenuItems[i].children[j].header : this.navMenuItems[i].children[j].title
                        json.push(this.navMenuItems[i].children[j])
                    }
                }
            }
        }
    }
    this.items = json
    this.filteredItems = this.items
    document.body.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
      // Remove the event listener before the component is destroyed
      document.body.removeEventListener('click', this.handleClickOutside);
  }
}
</script>

<style lang="scss">
@import "~@resources/scss/base/core/menu/menu-types/vertical-menu.scss";

.dropdown-search{
    background-color:white;
    border-radius:5px;
    max-height:300px;
    overflow-y:auto;
}

.dropdown-search li{
    padding:10px;
    cursor: pointer;
}
.dropdown-search li:hover{
    background-color: linear-gradient(118deg, #8c0095, rgba(228, 176, 34, 0.7));
    color: white;
}
.text-link-route{
    font-style: italic;
    color: rgba(108, 108, 108, 0.752);
    font-size:12px;
}
</style>
