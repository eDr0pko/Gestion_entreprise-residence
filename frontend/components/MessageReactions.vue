<template>
  <div class="message-reactions">
    <!-- Réactions existantes sous le message -->
    <div v-if="Object.keys(reactions).length > 0" class="flex flex-wrap gap-1 mt-2 mb-1">
      <button
        v-for="(reactionData, emoji) in reactions"
        :key="emoji"
        @click="toggleReaction(emoji)"
        class="inline-flex items-center px-2 py-1 rounded-full text-xs transition-all duration-200 hover:scale-105 border"
        :class="[
          userHasReacted(emoji) 
            ? 'bg-[#0097b2] text-white border-[#0097b2]' 
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200'
        ]"
        :title="getReactionTooltip(reactionData)"
      >
        <span class="mr-1">{{ emoji }}</span>
        <span class="font-medium">{{ reactionData.count }}</span>
      </button>
    </div>

    <!-- Bouton pour ajouter une réaction (toujours visible) -->
    <div class="relative mt-1">
      <button
        @click="showReactionPicker = !showReactionPicker"
        class="p-1 text-gray-400 hover:text-[#0097b2] hover:bg-gray-100 rounded transition-colors text-xs"
        title="Ajouter une réaction"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </button>

      <!-- Picker d'émojis -->
      <Transition name="picker">
        <div
          v-if="showReactionPicker"
          v-click-outside="closeReactionPicker"
          class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-lg p-3 z-50 min-w-max"
        >
          <div class="grid grid-cols-6 gap-2">
            <button
              v-for="emoji in quickEmojis"
              :key="emoji"
              @click="addReaction(emoji)"
              class="p-2 rounded hover:bg-gray-100 transition-colors text-lg flex items-center justify-center"
              :title="`Réagir avec ${emoji}`"
            >
              {{ emoji }}
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup lang="ts">
interface ReactionData {
  count: number
  users: Array<{
    email: string
    nom: string
  }>
}

interface Props {
  messageId: number
  reactions: Record<string, ReactionData>
  currentUserEmail: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  reactionToggled: [emoji: string]
}>()

// État réactif
const showReactionPicker = ref(false)
const processing = ref(false)

// Émojis de réaction rapide
const quickEmojis = ['👍', '❤️', '😂', '😮', '😢', '😡', '👏', '🎉', '🔥', '💯', '✅', '❌']

// Vérifier si l'utilisateur actuel a réagi avec cet emoji
const userHasReacted = (emoji: string): boolean => {
  const reactionData = props.reactions[emoji]
  if (!reactionData) return false
  return reactionData.users.some(user => user.email === props.currentUserEmail)
}

// Générer le tooltip pour une réaction
const getReactionTooltip = (reactionData: ReactionData): string => {
  if (reactionData.count === 1) {
    return reactionData.users[0].nom
  } else if (reactionData.count <= 3) {
    return reactionData.users.map(user => user.nom).join(', ')
  } else {
    return `${reactionData.users.slice(0, 2).map(user => user.nom).join(', ')} et ${reactionData.count - 2} autres`
  }
}

// Ajouter/retirer une réaction
const toggleReaction = async (emoji: string) => {
  if (processing.value) return
  
  processing.value = true
  try {
    emit('reactionToggled', emoji)
  } finally {
    processing.value = false
  }
}

// Ajouter une nouvelle réaction
const addReaction = async (emoji: string) => {
  closeReactionPicker()
  await toggleReaction(emoji)
}

// Fermer le picker
const closeReactionPicker = () => {
  showReactionPicker.value = false
}
</script>

<style scoped>
.picker-enter-active, .picker-leave-active {
  transition: all 0.2s ease;
}

.picker-enter-from, .picker-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.95);
}

.message-reactions {
  margin-top: 0.25rem;
}
</style>