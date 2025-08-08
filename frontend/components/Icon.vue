<template>
  <span v-if="name" :class="className">
    <!-- Basic icons using unicode/emoji for now -->
    <span v-if="name === 'tabler:clock'">🕐</span>
    <span v-else-if="name === 'tabler:calendar-day'">📅</span>
    <span v-else-if="name === 'tabler:calendar-week'">📆</span>
    <span v-else-if="name === 'tabler:calendar-month'">🗓️</span>
    <span v-else-if="name === 'tabler:sun'">☀️</span>
    <span v-else-if="name === 'tabler:moon'">🌙</span>
    <span v-else-if="name === 'tabler:filter'">🔍</span>
    <span v-else-if="name === 'tabler:download'">⬇️</span>
    <span v-else-if="name === 'tabler:plus'">➕</span>
    <span v-else-if="name === 'tabler:chevron-left'">◀️</span>
    <span v-else-if="name === 'tabler:chevron-right'">▶️</span>
    <span v-else-if="name === 'tabler:edit'">✏️</span>
    <span v-else-if="name === 'tabler:x'">❌</span>
    <span v-else>{{ name.split(':')[1] || '?' }}</span>
  </span>
</template>

<script setup>
defineProps({
  name: {
    type: String,
    required: true
  },
  className: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
span {
  display: inline-block;
  line-height: 1;
}
</style>
