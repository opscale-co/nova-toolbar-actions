<template>
  <ActionDropdown
    :resource="resource"
    :actions="actions"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :via-relationship="viaRelationship"
    :resource-name="resourceName"
    :selected-resources="[resource.id.value]"
    :show-headings="true"
    context="index"
    @action-executed="$emit('actionExecuted')"
  >
    <template #trigger>
      <ActionButton
        variant="action"
        icon="ellipsis-horizontal"
        :dusk="`${resource.id.value}-control-selector`"
      />
    </template>

    <template #menu>
      <div
        v-if="
          (resource.authorizedToView && resource.previewHasFields) ||
            resource.authorizedToReplicate ||
            (currentUser.canImpersonate && resource.authorizedToImpersonate)
        "
      >
        <DropdownMenuHeading>{{ __('Actions') }}</DropdownMenuHeading>
        <div class="py-1">
          <DropdownMenuItem
            v-if="resource.authorizedToView && resource.previewHasFields"
            :dusk="`${resource.id.value}-preview-button`"
            as="button"
            :title="__('Preview')"
            @click.prevent="$emit('show-preview')"
          >
            {{ __('Preview') }}
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="resource.authorizedToReplicate"
            :dusk="`${resource.id.value}-replicate-button`"
            :href="
              $url(
                `/resources/${resourceName}/${resource.id.value}/replicate`,
                {
                  viaResource,
                  viaResourceId,
                  viaRelationship,
                }
              )
            "
            :title="__('Replicate')"
          >
            {{ __('Replicate') }}
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="
              currentUser.canImpersonate && resource.authorizedToImpersonate
            "
            as="button"
            :dusk="`${resource.id.value}-impersonate-button`"
            :title="__('Impersonate')"
            @click.prevent="
              startImpersonating({
                resource: resourceName,
                resourceId: resource.id.value,
              })
            "
          >
            {{ __('Impersonate') }}
          </DropdownMenuItem>
        </div>
      </div>
    </template>
  </ActionDropdown>
</template>

<script>
import { mapProps } from 'laravel-nova'
import { mapGetters, mapActions } from 'vuex'
import { Button as ActionButton } from 'laravel-nova-ui'
import ActionDropdown from './ActionDropdown.vue'

export default {
  components: {
    ActionDropdown,
    ActionButton,
  },

  props: {
    resource: { type: Object, default: null },
    actions: { type: Array, default: () => [] },
    viaManyToMany: { type: Boolean, default: false },

    ...mapProps([
      'resourceName',
      'viaResource',
      'viaResourceId',
      'viaRelationship',
    ]),
  },

  emits: ['actionExecuted', 'show-preview'],

  computed: mapGetters(['currentUser']),

  methods: mapActions(['startImpersonating']),
}
</script>
