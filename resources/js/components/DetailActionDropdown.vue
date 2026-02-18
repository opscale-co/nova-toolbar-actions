<template>
  <ActionDropdown
    v-if="resource"
    :resource="resource"
    :actions="actions"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :via-relationship="viaRelationship"
    :resource-name="resourceName"
    :selected-resources="[resource.id.value]"
    :trigger-dusk-attribute="`${resource.id.value}-control-selector`"
    :show-headings="true"
    context="detail"
    @action-executed="$emit('actionExecuted')"
  >
    <template #menu>
      <div
        v-if="
          resource.authorizedToReplicate ||
            (currentUser.canImpersonate && resource.authorizedToImpersonate) ||
            (resource.authorizedToDelete && !resource.softDeleted) ||
            (resource.authorizedToRestore && resource.softDeleted) ||
            resource.authorizedToForceDelete
        "
      >
        <DropdownMenuHeading>{{ __('Actions') }}</DropdownMenuHeading>
        <div class="py-1">
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

          <DropdownMenuItem
            v-if="resource.authorizedToDelete && !resource.softDeleted"
            dusk="open-delete-modal-button"
            @click.prevent="openDeleteModal"
          >
            {{
              __('Delete :resource', {
                resource: resourceInformation.singularLabel,
              })
            }}
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="resource.authorizedToRestore && resource.softDeleted"
            as="button"
            dusk="open-restore-modal-button"
            @click.prevent="openRestoreModal"
          >
            {{
              __('Restore :resource', {
                resource: resourceInformation.singularLabel,
              })
            }}
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="resource.authorizedToForceDelete"
            as="button"
            dusk="open-force-delete-modal-button"
            @click.prevent="openForceDeleteModal"
          >
            {{
              __('Force Delete :resource', {
                resource: resourceInformation.singularLabel,
              })
            }}
          </DropdownMenuItem>
        </div>
      </div>
    </template>
  </ActionDropdown>

  <DeleteResourceModal
    mode="delete"
    :resource-name="resourceName"
    :show="deleteModalOpen"
    @close="closeDeleteModal"
    @confirm="confirmDelete"
  />

  <RestoreResourceModal
    :resource-name="resourceName"
    :show="restoreModalOpen"
    @close="closeRestoreModal"
    @confirm="confirmRestore"
  />

  <DeleteResourceModal
    mode="force delete"
    :resource-name="resourceName"
    :show="forceDeleteModalOpen"
    @close="closeForceDeleteModal"
    @confirm="confirmForceDelete"
  />
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { InteractsWithResourceInformation, mapProps } from 'laravel-nova'
import { Deletable } from '@/mixins'
import ActionDropdown from './ActionDropdown.vue'

export default {
  components: {
    ActionDropdown,
  },

  mixins: [Deletable, InteractsWithResourceInformation],

  inheritAttrs: false,

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

  emits: ['actionExecuted', 'resource-deleted', 'resource-restored'],

  data: () => ({
    deleteModalOpen: false,
    restoreModalOpen: false,
    forceDeleteModalOpen: false,
  }),

  computed: mapGetters(['currentUser']),

  methods: {
    ...mapActions(['startImpersonating']),

    async confirmDelete() {
      this.deleteResources([this.resource], response => {
        Nova.success(
          this.__('The :resource was deleted!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        if (response && response.data && response.data.redirect) {
          Nova.visit(response.data.redirect)
          return
        }

        if (!this.resource.softDeletes) {
          Nova.visit(`/resources/${this.resourceName}`)
          return
        }

        this.closeDeleteModal()
        this.$emit('resource-deleted')
      })
    },

    openDeleteModal() {
      this.deleteModalOpen = true
    },

    closeDeleteModal() {
      this.deleteModalOpen = false
    },

    async confirmRestore() {
      this.restoreResources([this.resource], () => {
        Nova.success(
          this.__('The :resource was restored!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        this.closeRestoreModal()
        this.$emit('resource-restored')
      })
    },

    openRestoreModal() {
      this.restoreModalOpen = true
    },

    closeRestoreModal() {
      this.restoreModalOpen = false
    },

    async confirmForceDelete() {
      this.forceDeleteResources([this.resource], response => {
        Nova.success(
          this.__('The :resource was deleted!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        if (response && response.data && response.data.redirect) {
          Nova.visit(response.data.redirect)
          return
        }

        Nova.visit(`/resources/${this.resourceName}`)
      })
    },

    openForceDeleteModal() {
      this.forceDeleteModalOpen = true
    },

    closeForceDeleteModal() {
      this.forceDeleteModalOpen = false
    },
  },
}
</script>
