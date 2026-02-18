import ActionDropdown from './components/ActionDropdown.vue'
import InlineActionDropdown from './components/InlineActionDropdown.vue'
import DetailActionDropdown from './components/DetailActionDropdown.vue'
import ActionSelector from './components/ActionSelector.vue'

Nova.booting((app) => {
  app.component('ActionDropdown', ActionDropdown)
  app.component('InlineActionDropdown', InlineActionDropdown)
  app.component('DetailActionDropdown', DetailActionDropdown)
  app.component('ActionSelector', ActionSelector)
})
