<template>
  <div>
    <component
      :is="actionModalComponent"
      v-if="actionModalVisible"
      :show="actionModalVisible"
      class="text-left"
      :working="working"
      :selected-resources="selectedResources"
      :resource-name="resourceName"
      :action="selectedAction"
      :errors="errors"
      @confirm="runAction"
      @close="closeConfirmationModal"
    />

    <component
      :is="responseModalComponent"
      v-if="responseModalVisible"
      :show="responseModalVisible"
      :data="responseModalPayload"
      @confirm="handleResponseModalConfirm"
      @close="handleResponseModalClose"
    />

    <div class="flex items-center gap-2">
      <template v-for="action in buttonActions" :key="action.uriKey">
        <button
          :data-action-id="action.uriKey"
          :disabled="action.authorizedToRun === false || working"
          :title="action.name"
          class="shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm"
          @click="() => handleClick(action)"
        >
          {{ action.name }}
        </button>
      </template>

      <Dropdown v-if="dropdownActions.length > 0 || hasMenuSlot">
        <template #default>
          <slot name="trigger">
            <ActionButton
              v-tooltip="__('Actions')"
              :dusk="triggerDuskAttribute"
              variant="ghost"
              icon="ellipsis-horizontal"
              @click.stop
            />
          </slot>
        </template>

        <template #menu>
          <DropdownMenu width="auto">
            <ScrollWrap :height="250">
              <nav
                class="px-1 divide-y divide-gray-100 dark:divide-gray-800 divide-solid"
              >
                <slot name="menu" />

                <div v-if="dropdownActions.length > 0">
                  <DropdownMenuHeading v-if="showHeadings">{{
                    __('User Actions')
                  }}</DropdownMenuHeading>

                  <div class="py-1">
                    <DropdownMenuItem
                      v-for="action in dropdownActions"
                      :key="action.uriKey"
                      :data-action-id="action.uriKey"
                      as="button"
                      class="border-none"
                      :title="action.name"
                      :disabled="action.authorizedToRun === false"
                      @click="() => handleClick(action)"
                    >
                      {{ action.name }}
                    </DropdownMenuItem>
                  </div>
                </div>
              </nav>
            </ScrollWrap>
          </DropdownMenu>
        </template>
      </Dropdown>
    </div>
  </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'
import { Button as ActionButton } from 'laravel-nova-ui'
import { useStore } from 'vuex'
import { useActions } from '@/composables/useActions'
import DropdownMenuHeading from '@/components/Dropdowns/DropdownMenuHeading.vue'

const emitter = defineEmits(['actionExecuted'])

const props = defineProps({
  resource: { type: Object, default: null },
  resourceName: { type: String, default: null },
  viaResource: { type: String, default: null },
  viaResourceId: { type: String, default: null },
  viaRelationship: { type: String, default: null },
  relationshipType: { type: String, default: null },
  actions: { type: Array, default: () => [] },
  selectedResources: { type: [Array, String], default: () => [] },
  endpoint: { type: String, default: null },
  triggerDuskAttribute: { type: String, default: null },
  showHeadings: { type: Boolean, default: false },
  context: { type: String, default: 'index' },
})

const slots = useSlots()
const hasMenuSlot = computed(() => !!slots.menu)
const store = useStore()

const {
  errors,
  actionModalVisible,
  responseModalVisible,
  closeConfirmationModal,
  closeResponseModal,
  handleActionClick,
  selectedAction,
  working,
  executeAction,
  actionModalReponseData,
} = useActions(props, emitter, store)

const actionModalComponent = computed(
  () => selectedAction.value && selectedAction.value.component
)
const responseModalComponent = computed(
  () => actionModalReponseData.value && actionModalReponseData.value.component
)
const responseModalPayload = computed(
  () =>
    (actionModalReponseData.value && actionModalReponseData.value.payload) || {}
)

const runAction = () => executeAction(() => emitter('actionExecuted'))

const handleClick = action => {
  if (action.authorizedToRun !== false) {
    handleActionClick(action.uriKey)
  }
}

const handleResponseModalConfirm = () => {
  closeResponseModal()
  emitter('actionExecuted')
}

const handleResponseModalClose = () => {
  closeResponseModal()
  emitter('actionExecuted')
}

const isToolbarButton = action => {
  if (props.context === 'detail') {
    return action.showAsButton === true || action.showOnDetailToolbar === true
  }
  // On index, only standalone actions can be toolbar buttons
  return (
    action.standalone === true &&
    (action.showAsButton === true || action.showOnIndexToolbar === true)
  )
}

const buttonActions = computed(() => props.actions.filter(isToolbarButton))

const dropdownActions = computed(() =>
  props.actions.filter(a => !isToolbarButton(a))
)
</script>
