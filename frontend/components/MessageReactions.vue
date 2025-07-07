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

    <!-- Bouton pour ajouter une réaction -->
    <div class="relative mt-1">
      <button
        ref="emojiButton"
        @click.stop="toggleEmojiPicker"
        class="p-1 text-gray-400 hover:text-[#0097b2] hover:bg-gray-100 rounded transition-colors text-xs"
        :class="{ 'text-[#0097b2] bg-gray-100': showReactionPicker }"
        title="Ajouter une réaction"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </button>

      <!-- Picker d'émojis avec transition -->
      <Transition name="picker">
        <div
          v-if="showReactionPicker"
          ref="emojiPicker"
          v-click-outside="closeReactionPicker"
          class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-xl p-3 z-[1000] min-w-max"
          @click.stop
        >
          <div class="text-xs text-gray-500 mb-2 font-medium">Choisir une réaction</div>
          <div class="grid grid-cols-6 gap-1">
            <button
              v-for="emoji in quickEmojis"
              :key="emoji"
              @click.stop="addReaction(emoji)"
              class="p-2 rounded hover:bg-gray-100 transition-colors text-lg flex items-center justify-center w-10 h-10"
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
import type { ReactionData } from '~/types'

interface Props {
  messageId: number
  reactions: Record<string, ReactionData>
  currentUserEmail: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  reactionToggled: [emoji: string]
}>()

// Références DOM
const emojiButton = ref<HTMLButtonElement>()
const emojiPicker = ref<HTMLDivElement>()

// État réactif
const showReactionPicker = ref(false)
const processing = ref(false)

// Émojis de réaction rapide
const quickEmojis = ['👍', '❤️', '😂', '😮', '😢', '😡', '👏', '🎉', '🔥', '💯', '✅', '❌']

// Toggle du picker d'émojis
const toggleEmojiPicker = () => {
  console.log('Toggle emoji picker clicked, current state:', showReactionPicker.value)
  showReactionPicker.value = !showReactionPicker.value
  console.log('New state:', showReactionPicker.value)
}

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
    console.log('Toggling reaction:', emoji, 'for message:', props.messageId)
    emit('reactionToggled', emoji)
  } finally {
    processing.value = false
  }
}

// Ajouter une nouvelle réaction
const addReaction = async (emoji: string) => {
  console.log('Adding reaction:', emoji)
  closeReactionPicker()
  await toggleReaction(emoji)
}

// Fermer le picker
const closeReactionPicker = () => {
  console.log('Closing reaction picker')
  showReactionPicker.value = false
}

// Debug: Observer les changements d'état
watch(showReactionPicker, (newValue) => {
  console.log('showReactionPicker changed to:', newValue)
})
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
  position: relative;
}

/* S'assurer que le picker est au-dessus de tout */
.message-reactions .relative {
  z-index: 1;
}

/* Améliorer la visibilité du picker */
.absolute {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e7eb;
  background: white;
}
</style>