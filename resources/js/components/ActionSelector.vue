<template>
  <div class="flex items-center gap-2">
    <SelectControl
      v-if="actionSelectionOptions.length > 0"
      v-bind="$attrs"
      ref="actionSelectControl"
      v-model="actionSelectionInput"
      :options="actionSelectionOptions"
      size="xs"
      :class="{ 'max-w-[6rem]': width === 'auto', 'w-full': width === 'full' }"
      dusk="action-select"
      :aria-label="__('Select Action')"
    >
      <option value="" disabled selected>{{ __('Actions') }}</option>
    </SelectControl>

    <component
      :is="actionModalComponent"
      v-if="actionModalVisible"
      class="text-left"
      :show="actionModalVisible"
      :working="working"
      :selected-resources="selectedResources"
      :resource-name="resourceName"
      :action="selectedAction"
      :errors="errors"
      @confirm="executeAction"
      @close="closeConfirmationModal"
    />

    <component
      :is="responseModalComponent"
      v-if="responseModalVisible"
      :show="responseModalVisible"
      :data="responseModalPayload"
      @confirm="closeResponseModal"
      @close="closeResponseModal"
    />
  </div>
</template>

<script setup>
import { useActions } from '@/composables/useActions'
import { computed, nextTick, ref, watch } from 'vue'
import { useStore } from 'vuex'

const emitter = defineEmits(['actionExecuted'])

const props = defineProps({
  width: { type: String, default: 'auto' },
  pivotName: { type: String, default: null },
  resourceName: { type: String, default: null },
  viaResource: { type: String, default: null },
  viaResourceId: { type: String, default: null },
  viaRelationship: { type: String, default: null },
  relationshipType: { type: String, default: null },
  pivotActions: {
    type: Object,
    default: () => ({ name: 'Pivot', actions: [] }),
  },
  actions: { type: Array, default: () => [] },
  selectedResources: { type: [Array, String], default: () => [] },
  endpoint: { type: String, default: null },
  triggerDuskAttribute: { type: String, default: null },
})

const actionSelectionInput = ref('')

const store = useStore()

const {
  errors,
  actionModalVisible,
  responseModalVisible,
  closeConfirmationModal,
  closeResponseModal,
  selectedAction,
  setSelectedActionKey,
  determineActionStrategy,
  working,
  executeAction,
  availableActions,
  availablePivotActions,
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

watch(actionSelectionInput, value => {
  if (value == '') {
    return
  }

  setSelectedActionKey(value)
  determineActionStrategy()

  nextTick(() => (actionSelectionInput.value = ''))
})

const actionSelectionOptions = computed(() => [
  ...availableActions.value.map(a => ({
    value: a.uriKey,
    label: a.name,
    disabled: a.authorizedToRun === false,
  })),
  ...availablePivotActions.value.map(a => ({
    group: props.pivotName,
    value: a.uriKey,
    label: a.name,
    disabled: a.authorizedToRun === false,
  })),
])
</script>
