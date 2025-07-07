<template>
    <aside class="dashboard-sidebar bg-white/80 backdrop-blur-md border-r border-gray-200 h-full min-h-screen w-64 flex flex-col py-8 px-4 shadow-xl sticky top-0 z-40">
        <nav class="flex-1 flex flex-col gap-2">
            <template v-for="section in sections" :key="section.key">
                <button
                :class="[
                    'flex items-center w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 text-left',
                    selectedSection === section.key
                    ? 'bg-gradient-to-r from-[#0097b2]/90 to-[#00b4d8]/90 text-white shadow-lg'
                    : 'hover:bg-[#0097b2]/10 text-gray-700 hover:text-[#0097b2]'
                ]"
                @click="$emit('select', section.key)"
                >
                <span class="mr-3">
                    <component :is="section.icon" class="w-5 h-5" />
                </span>
                {{ section.label }}
                </button>
            </template>
        </nav>
    </aside>
</template>

<script setup lang="ts">
const props = defineProps<{
  sections: Array<{ key: string; label: string; icon: any }>
  selectedSection: string
}>()
const emit = defineEmits(['select'])
</script>

<style scoped>
.dashboard-sidebar {
  box-shadow: 0 4px 24px 0 rgba(0,0,0,0.07), 0 1.5px 4px 0 rgba(0,0,0,0.03);
}
</style>
