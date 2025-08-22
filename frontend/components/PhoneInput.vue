<template>
  <div class="phone-input-container">
    <label v-if="label" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
      {{ $t(label) }}
    </label>
    
    <div class="relative">
      <div class="flex rounded-xl border border-gray-300 dark:border-gray-600 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-transparent transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus-within:bg-white dark:bg-gray-800">
        <!-- Sélecteur de pays -->
        <div class="relative">
          <button
            type="button"
            @click="toggleCountrySelector"
            class="flex items-center px-3 py-3 border-r border-gray-300 dark:border-gray-600 rounded-l-xl hover:bg-gray-100 dark:hover:bg-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200"
            :disabled="disabled"
          >
            <span class="text-lg mr-2">{{ selectedCountry.flag }}</span>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ selectedCountry.code }}</span>
            <svg class="w-4 h-4 ml-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          
          <!-- Liste déroulante des pays -->
          <div v-if="showCountrySelector" class="absolute top-full left-0 z-50 w-72 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-80 overflow-hidden">
            <div class="p-2 border-b border-gray-100">
              <input
                v-model="countrySearch"
                type="text"
                :placeholder="$t('components.phoneInput.country') + '...'"
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
            <div class="max-h-72 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
              <button
                v-for="country in filteredCountries"
                :key="country.code + country.name"
                type="button"
                @click="selectCountry(country)"
                class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 flex items-center space-x-3 transition-colors duration-150 border-b border-gray-50 last:border-b-0 country-item"
              >
                <span class="text-lg flex-shrink-0">{{ country.flag }}</span>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $t('components.phoneInput.country') }}: {{ country.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ country.code }}</div>
                </div>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Champ de saisie du numéro -->
        <input
          ref="phoneInput"
          v-model="formattedPhone"
          type="tel"
          :placeholder="$t('components.phoneInput.placeholder')"
          :disabled="disabled"
          class="flex-1 px-4 py-3 bg-transparent border-0 rounded-r-xl focus:outline-none text-gray-900 dark:text-white placeholder-gray-500"
          @input="handlePhoneInput"
          @focus="handleFocus"
          @blur="handleBlur"
        />
      </div>
      
      <!-- Message d'aide -->
      <p v-if="helpText" class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1 ml-1">{{ $t(helpText) }}</p>
      
      <!-- Message d'erreur -->
      <p v-if="error" class="text-xs text-red-600 mt-1 ml-1">{{ $t(error) }}</p>
    </div>
    
    <!-- Overlay pour fermer le sélecteur -->
    <div v-if="showCountrySelector" class="fixed inset-0 z-40" @click="closeCountrySelector"></div>
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref, computed, onUnmounted, nextTick, watch } from 'vue'

interface Country {
    name: string
    code: string
    flag: string
    dialCode: string
    placeholder: string
    format: RegExp
  }

  interface Props {
    modelValue: string
    label?: string
    helpText?: string
    error?: string
    disabled?: boolean
    required?: boolean
  }

  interface Emits {
    (e: 'update:modelValue', value: string): void
    (e: 'country-change', country: Country): void
  }

  // Import du système de thème
  const { initTheme } = useTheme()

  const props = withDefaults(defineProps<Props>(), {
    helpText: 'Format international requis',
    disabled: false,
    required: false
  })

  const emit = defineEmits<Emits>()

  // Pays disponibles avec leurs formats
  const countries: Country[] = [
    {
      name: 'France',
      code: '+33',
      flag: '🇫🇷',
      dialCode: '33',
      placeholder: 'x xx xx xx xx',
      format: /^(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Togo',
      code: '+228',
      flag: '🇹🇬',
      dialCode: '228',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'États-Unis',
      code: '+1',
      flag: '🇺🇸',
      dialCode: '1',
      placeholder: '(xxx) xxx-xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Canada',
      code: '+1',
      flag: '🇨🇦',
      dialCode: '1',
      placeholder: '(xxx) xxx-xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Allemagne',
      code: '+49',
      flag: '🇩🇪',
      dialCode: '49',
      placeholder: 'xxx xxxxxxxx',
      format: /^(\d{2,5})(\d{4,12})$/
    },
    {
      name: 'Royaume-Uni',
      code: '+44',
      flag: '🇬🇧',
      dialCode: '44',
      placeholder: 'xx xxxx xxxx',
      format: /^(\d{2,4})(\d{4})(\d{4})$/
    },
    {
      name: 'Espagne',
      code: '+34',
      flag: '🇪🇸',
      dialCode: '34',
      placeholder: 'xxx xx xx xx',
      format: /^(\d{3})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Italie',
      code: '+39',
      flag: '🇮🇹',
      dialCode: '39',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Belgique',
      code: '+32',
      flag: '🇧🇪',
      dialCode: '32',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Suisse',
      code: '+41',
      flag: '🇨🇭',
      dialCode: '41',
      placeholder: 'xx xxx xx xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Pays-Bas',
      code: '+31',
      flag: '🇳🇱',
      dialCode: '31',
      placeholder: 'x xxxx xxxx',
      format: /^(\d{1})(\d{4})(\d{4})$/
    },
    {
      name: 'Portugal',
      code: '+351',
      flag: '🇵🇹',
      dialCode: '351',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Pologne',
      code: '+48',
      flag: '🇵🇱',
      dialCode: '48',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Maroc',
      code: '+212',
      flag: '🇲🇦',
      dialCode: '212',
      placeholder: 'xxx-xx-xx-xx',
      format: /^(\d{3})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Algérie',
      code: '+213',
      flag: '🇩🇿',
      dialCode: '213',
      placeholder: 'xxx xx xx xx',
      format: /^(\d{3})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Tunisie',
      code: '+216',
      flag: '🇹🇳',
      dialCode: '216',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Sénégal',
      code: '+221',
      flag: '🇸🇳',
      dialCode: '221',
      placeholder: 'xx xxx xx xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Mali',
      code: '+223',
      flag: '🇲🇱',
      dialCode: '223',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Burkina Faso',
      code: '+226',
      flag: '🇧🇫',
      dialCode: '226',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Niger',
      code: '+227',
      flag: '🇳🇪',
      dialCode: '227',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Bénin',
      code: '+229',
      flag: '🇧🇯',
      dialCode: '229',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Mauritanie',
      code: '+222',
      flag: '🇲🇷',
      dialCode: '222',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Côte d\'Ivoire',
      code: '+225',
      flag: '🇨🇮',
      dialCode: '225',
      placeholder: 'xx xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Ghana',
      code: '+233',
      flag: '�🇭',
      dialCode: '233',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Nigeria',
      code: '+234',
      flag: '🇳🇬',
      dialCode: '234',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Cameroun',
      code: '+237',
      flag: '🇨🇲',
      dialCode: '237',
      placeholder: 'x xx xx xx xx',
      format: /^(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Gabon',
      code: '+241',
      flag: '🇬🇦',
      dialCode: '241',
      placeholder: 'x xx xx xx',
      format: /^(\d{1})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'République Démocratique du Congo',
      code: '+243',
      flag: '🇨🇩',
      dialCode: '243',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Madagascar',
      code: '+261',
      flag: '🇲🇬',
      dialCode: '261',
      placeholder: 'xx xx xxx xx',
      format: /^(\d{2})(\d{2})(\d{3})(\d{2})$/
    },
    {
      name: 'Maurice',
      code: '+230',
      flag: '🇲🇺',
      dialCode: '230',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Brésil',
      code: '+55',
      flag: '🇧🇷',
      dialCode: '55',
      placeholder: '(xx) xxxxx-xxxx',
      format: /^(\d{2})(\d{5})(\d{4})$/
    },
    {
      name: 'Argentine',
      code: '+54',
      flag: '🇦🇷',
      dialCode: '54',
      placeholder: 'xx xxxx-xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Mexique',
      code: '+52',
      flag: '🇲🇽',
      dialCode: '52',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Japon',
      code: '+81',
      flag: '🇯🇵',
      dialCode: '81',
      placeholder: 'xxx-xxxx-xxxx',
      format: /^(\d{3})(\d{4})(\d{4})$/
    },
    {
      name: 'Corée du Sud',
      code: '+82',
      flag: '��',
      dialCode: '82',
      placeholder: 'xx-xxxx-xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Chine',
      code: '+86',
      flag: '🇨🇳',
      dialCode: '86',
      placeholder: 'xxx xxxx xxxx',
      format: /^(\d{3})(\d{4})(\d{4})$/
    },
    {
      name: 'Inde',
      code: '+91',
      flag: '🇮🇳',
      dialCode: '91',
      placeholder: 'xxxxx xxxxx',
      format: /^(\d{5})(\d{5})$/
    },
    {
      name: 'Australie',
      code: '+61',
      flag: '🇦🇺',
      dialCode: '61',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Nouvelle-Zélande',
      code: '+64',
      flag: '🇳🇿',
      dialCode: '64',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Afrique du Sud',
      code: '+27',
      flag: '🇿🇦',
      dialCode: '27',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Russie',
      code: '+7',
      flag: '🇷🇺',
      dialCode: '7',
      placeholder: 'xxx xxx-xx-xx',
      format: /^(\d{3})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Turquie',
      code: '+90',
      flag: '🇹🇷',
      dialCode: '90',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Égypte',
      code: '+20',
      flag: '🇪🇬',
      dialCode: '20',
      placeholder: 'xx xxxx xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Kenya',
      code: '+254',
      flag: '🇰🇪',
      dialCode: '254',
      placeholder: 'xxx xxxxxx',
      format: /^(\d{3})(\d{6})$/
    },
    {
      name: 'Éthiopie',
      code: '+251',
      flag: '🇪🇹',
      dialCode: '251',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Tanzanie',
      code: '+255',
      flag: '🇹🇿',
      dialCode: '255',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Ouganda',
      code: '+256',
      flag: '🇺🇬',
      dialCode: '256',
      placeholder: 'xxx xxxxxx',
      format: /^(\d{3})(\d{6})$/
    },
    {
      name: 'Zambie',
      code: '+260',
      flag: '🇿🇲',
      dialCode: '260',
      placeholder: 'xx xxxxxxx',
      format: /^(\d{2})(\d{7})$/
    },
    {
      name: 'Zimbabwe',
      code: '+263',
      flag: '🇿🇼',
      dialCode: '263',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Botswana',
      code: '+267',
      flag: '🇧🇼',
      dialCode: '267',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Madagascar',
      code: '+261',
      flag: '🇲🇬',
      dialCode: '261',
      placeholder: 'xx xx xxx xx',
      format: /^(\d{2})(\d{2})(\d{3})(\d{2})$/
    },
    {
      name: 'Mexique',
      code: '+52',
      flag: '🇲🇽',
      dialCode: '52',
      placeholder: 'xx xxxx xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Argentine',
      code: '+54',
      flag: '🇦🇷',
      dialCode: '54',
      placeholder: 'xx xxxx-xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Chili',
      code: '+56',
      flag: '🇨🇱',
      dialCode: '56',
      placeholder: 'x xxxx xxxx',
      format: /^(\d{1})(\d{4})(\d{4})$/
    },
    {
      name: 'Colombie',
      code: '+57',
      flag: '🇨🇴',
      dialCode: '57',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Pérou',
      code: '+51',
      flag: '🇵🇪',
      dialCode: '51',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Venezuela',
      code: '+58',
      flag: '🇻🇪',
      dialCode: '58',
      placeholder: 'xxx-xxx-xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Uruguay',
      code: '+598',
      flag: '🇺🇾',
      dialCode: '598',
      placeholder: 'x xxx xxxx',
      format: /^(\d{1})(\d{3})(\d{4})$/
    },
    {
      name: 'Paraguay',
      code: '+595',
      flag: '🇵🇾',
      dialCode: '595',
      placeholder: 'xxx xxxxxx',
      format: /^(\d{3})(\d{6})$/
    },
    {
      name: 'Équateur',
      code: '+593',
      flag: '🇪🇨',
      dialCode: '593',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Bolivie',
      code: '+591',
      flag: '🇧🇴',
      dialCode: '591',
      placeholder: 'x xxx xxxx',
      format: /^(\d{1})(\d{3})(\d{4})$/
    },
    {
      name: 'Norvège',
      code: '+47',
      flag: '🇳🇴',
      dialCode: '47',
      placeholder: 'xxx xx xxx',
      format: /^(\d{3})(\d{2})(\d{3})$/
    },
    {
      name: 'Suède',
      code: '+46',
      flag: '🇸🇪',
      dialCode: '46',
      placeholder: 'xx xxx xx xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Finlande',
      code: '+358',
      flag: '🇫🇮',
      dialCode: '358',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Danemark',
      code: '+45',
      flag: '🇩🇰',
      dialCode: '45',
      placeholder: 'xx xx xx xx',
      format: /^(\d{2})(\d{2})(\d{2})(\d{2})$/
    },
    {
      name: 'Islande',
      code: '+354',
      flag: '🇮🇸',
      dialCode: '354',
      placeholder: 'xxx xxxx',
      format: /^(\d{3})(\d{4})$/
    },
    {
      name: 'Irlande',
      code: '+353',
      flag: '🇮🇪',
      dialCode: '353',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Autriche',
      code: '+43',
      flag: '🇦🇹',
      dialCode: '43',
      placeholder: 'xxx xxxxxxx',
      format: /^(\d{3})(\d{7})$/
    },
    {
      name: 'Suisse',
      code: '+41',
      flag: '🇨🇭',
      dialCode: '41',
      placeholder: 'xx xxx xx xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'République tchèque',
      code: '+420',
      flag: '🇨🇿',
      dialCode: '420',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Slovaquie',
      code: '+421',
      flag: '🇸🇰',
      dialCode: '421',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Hongrie',
      code: '+36',
      flag: '🇭🇺',
      dialCode: '36',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Pologne',
      code: '+48',
      flag: '🇵🇱',
      dialCode: '48',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Roumanie',
      code: '+40',
      flag: '🇷🇴',
      dialCode: '40',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Bulgarie',
      code: '+359',
      flag: '🇧🇬',
      dialCode: '359',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Croatie',
      code: '+385',
      flag: '🇭🇷',
      dialCode: '385',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Serbie',
      code: '+381',
      flag: '🇷🇸',
      dialCode: '381',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Slovénie',
      code: '+386',
      flag: '🇸🇮',
      dialCode: '386',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Lituanie',
      code: '+370',
      flag: '🇱🇹',
      dialCode: '370',
      placeholder: 'xxx xxxxx',
      format: /^(\d{3})(\d{5})$/
    },
    {
      name: 'Lettonie',
      code: '+371',
      flag: '🇱🇻',
      dialCode: '371',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Estonie',
      code: '+372',
      flag: '🇪🇪',
      dialCode: '372',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Arabie Saoudite',
      code: '+966',
      flag: '🇸🇦',
      dialCode: '966',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Émirats Arabes Unis',
      code: '+971',
      flag: '🇦🇪',
      dialCode: '971',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Qatar',
      code: '+974',
      flag: '🇶🇦',
      dialCode: '974',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Koweït',
      code: '+965',
      flag: '🇰🇼',
      dialCode: '965',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Bahreïn',
      code: '+973',
      flag: '🇧🇭',
      dialCode: '973',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Oman',
      code: '+968',
      flag: '🇴🇲',
      dialCode: '968',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Jordanie',
      code: '+962',
      flag: '🇯🇴',
      dialCode: '962',
      placeholder: 'x xxxx xxxx',
      format: /^(\d{1})(\d{4})(\d{4})$/
    },
    {
      name: 'Liban',
      code: '+961',
      flag: '🇱🇧',
      dialCode: '961',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Israël',
      code: '+972',
      flag: '🇮🇱',
      dialCode: '972',
      placeholder: 'xx-xxx-xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Iran',
      code: '+98',
      flag: '🇮🇷',
      dialCode: '98',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Irak',
      code: '+964',
      flag: '🇮🇶',
      dialCode: '964',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Afghanistan',
      code: '+93',
      flag: '🇦🇫',
      dialCode: '93',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Pakistan',
      code: '+92',
      flag: '🇵🇰',
      dialCode: '92',
      placeholder: 'xxx xxxxxxx',
      format: /^(\d{3})(\d{7})$/
    },
    {
      name: 'Bangladesh',
      code: '+880',
      flag: '🇧🇩',
      dialCode: '880',
      placeholder: 'xxxx-xxxxxx',
      format: /^(\d{4})(\d{6})$/
    },
    {
      name: 'Sri Lanka',
      code: '+94',
      flag: '🇱🇰',
      dialCode: '94',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Népal',
      code: '+977',
      flag: '🇳🇵',
      dialCode: '977',
      placeholder: 'xxx-xxx-xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Myanmar',
      code: '+95',
      flag: '🇲🇲',
      dialCode: '95',
      placeholder: 'xx xxxx xxxx',
      format: /^(\d{2})(\d{4})(\d{4})$/
    },
    {
      name: 'Cambodge',
      code: '+855',
      flag: '🇰🇭',
      dialCode: '855',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Laos',
      code: '+856',
      flag: '🇱🇦',
      dialCode: '856',
      placeholder: 'xx xxx xxx',
      format: /^(\d{2})(\d{3})(\d{3})$/
    },
    {
      name: 'Mongolie',
      code: '+976',
      flag: '🇲🇳',
      dialCode: '976',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Kazakhstan',
      code: '+7',
      flag: '🇰🇿',
      dialCode: '7',
      placeholder: 'xxx xxx xxxx',
      format: /^(\d{3})(\d{3})(\d{4})$/
    },
    {
      name: 'Ouzbékistan',
      code: '+998',
      flag: '🇺🇿',
      dialCode: '998',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Kirghizistan',
      code: '+996',
      flag: '🇰🇬',
      dialCode: '996',
      placeholder: 'xxx xxxxxx',
      format: /^(\d{3})(\d{6})$/
    },
    {
      name: 'Tadjikistan',
      code: '+992',
      flag: '🇹🇯',
      dialCode: '992',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    },
    {
      name: 'Turkménistan',
      code: '+993',
      flag: '🇹🇲',
      dialCode: '993',
      placeholder: 'xx xxxxxx',
      format: /^(\d{2})(\d{6})$/
    },
    {
      name: 'Géorgie',
      code: '+995',
      flag: '🇬🇪',
      dialCode: '995',
      placeholder: 'xxx xxx xxx',
      format: /^(\d{3})(\d{3})(\d{3})$/
    },
    {
      name: 'Arménie',
      code: '+374',
      flag: '🇦🇲',
      dialCode: '374',
      placeholder: 'xx xxxxxx',
      format: /^(\d{2})(\d{6})$/
    },
    {
      name: 'Azerbaïdjan',
      code: '+994',
      flag: '🇦🇿',
      dialCode: '994',
      placeholder: 'xx xxx xx xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Moldavie',
      code: '+373',
      flag: '🇲🇩',
      dialCode: '373',
      placeholder: 'xxxx xxxx',
      format: /^(\d{4})(\d{4})$/
    },
    {
      name: 'Biélorussie',
      code: '+375',
      flag: '🇧🇾',
      dialCode: '375',
      placeholder: 'xx xxx-xx-xx',
      format: /^(\d{2})(\d{3})(\d{2})(\d{2})$/
    },
    {
      name: 'Ukraine',
      code: '+380',
      flag: '🇺🇦',
      dialCode: '380',
      placeholder: 'xx xxx xxxx',
      format: /^(\d{2})(\d{3})(\d{4})$/
    }
  ]

  // État local
  const selectedCountry = ref<Country>(countries[0]) // France par défaut
  const formattedPhone = ref('')
  const showCountrySelector = ref(false)
  const countrySearch = ref('')
  const phoneInput = ref<HTMLInputElement>()

  // Pays filtrés par recherche
  const filteredCountries = computed(() => {
    if (!countrySearch.value) return countries
    
    const search = countrySearch.value.toLowerCase()
    return countries.filter(country => 
      country.name.toLowerCase().includes(search) ||
      country.code.includes(search)
    )
  })

  // Fonctions utilitaires
  const toggleCountrySelector = () => {
    showCountrySelector.value = !showCountrySelector.value
    if (showCountrySelector.value) {
      countrySearch.value = ''
    }
  }

  const closeCountrySelector = () => {
    showCountrySelector.value = false
    countrySearch.value = ''
  }

  const selectCountry = (country: Country) => {
    selectedCountry.value = country
    closeCountrySelector()
    emit('country-change', country)
    
    // Reformater le numéro avec le nouveau pays
    if (formattedPhone.value) {
      const cleanPhone = formattedPhone.value.replace(/\D/g, '')
      formattedPhone.value = formatPhoneNumber(cleanPhone, country)
      updateModelValue()
    }
    
    // Focus sur le champ
    nextTick(() => {
      phoneInput.value?.focus()
    })
  }

  const formatPhoneNumber = (phone: string, country: Country = selectedCountry.value): string => {
    // Retirer tous les caractères non-numériques
    const cleaned = phone.replace(/\D/g, '')
    
    // Appliquer le format spécifique au pays
    const match = cleaned.match(country.format)
    if (match) {
      // Formater selon le pays
      switch (country.dialCode) {
        case '33': // France
          return match.slice(1).join(' ')
        case '228': // Togo
        case '223': // Mali
        case '226': // Burkina Faso
        case '227': // Niger
        case '229': // Bénin
        case '222': // Mauritanie
          return match.slice(1).join(' ')
        case '1': // USA/Canada
          return `(${match[1]}) ${match[2]}-${match[3]}`
        case '49': // Allemagne
          return `${match[1]} ${match[2]}`
        case '44': // UK
          return `${match[1]} ${match[2]} ${match[3]}`
        case '34': // Espagne
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '39': // Italie
        case '233': // Ghana
        case '234': // Nigeria
        case '243': // RDC
        case '48': // Pologne
        case '351': // Portugal
        case '52': // Mexique
        case '81': // Japon
        case '86': // Chine
        case '61': // Australie
        case '90': // Turquie
          return `${match[1]} ${match[2]} ${match[3]}`
        case '32': // Belgique
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '41': // Suisse
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '31': // Pays-Bas
          return `${match[1]} ${match[2]} ${match[3]}`
        case '212': // Maroc
          return `${match[1]}-${match[2]}-${match[3]}-${match[4]}`
        case '213': // Algérie
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '216': // Tunisie
          return `${match[1]} ${match[2]} ${match[3]}`
        case '221': // Sénégal
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '225': // Côte d'Ivoire
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]} ${match[5]}`
        case '237': // Cameroun
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]} ${match[5]}`
        case '241': // Gabon
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '261': // Madagascar
          return `${match[1]} ${match[2]} ${match[3]} ${match[4]}`
        case '230': // Maurice
          return `${match[1]} ${match[2]}`
        case '55': // Brésil
          return `(${match[1]}) ${match[2]}-${match[3]}`
        case '54': // Argentine
          return `${match[1]} ${match[2]}-${match[3]}`
        case '82': // Corée du Sud
          return `${match[1]}-${match[2]}-${match[3]}`
        case '91': // Inde
          return `${match[1]} ${match[2]}`
        case '64': // Nouvelle-Zélande
        case '27': // Afrique du Sud
          return `${match[1]} ${match[2]} ${match[3]}`
        case '7': // Russie/Kazakhstan
          return `${match[1]} ${match[2]}-${match[3]}-${match[4]}`
        case '20': // Égypte
        case '254': // Kenya
        case '251': // Éthiopie
        case '255': // Tanzanie
        case '256': // Ouganda
        case '260': // Zambie
        case '263': // Zimbabwe
        case '267': // Botswana
        case '56': // Chili
        case '57': // Colombie
        case '51': // Pérou
        case '58': // Venezuela
        case '598': // Uruguay
        case '595': // Paraguay
        case '593': // Équateur
        case '591': // Bolivie
        case '47': // Norvège
        case '46': // Suède
        case '358': // Finlande
        case '45': // Danemark
        case '354': // Islande
        case '353': // Irlande
        case '43': // Autriche
        case '420': // République tchèque
        case '421': // Slovaquie
        case '36': // Hongrie
        case '359': // Bulgarie
        case '385': // Croatie
        case '381': // Serbie
        case '386': // Slovénie
        case '370': // Lituanie
        case '371': // Lettonie
        case '372': // Estonie
        case '966': // Arabie Saoudite
        case '971': // Émirats Arabes Unis
        case '974': // Qatar
        case '965': // Koweït
        case '973': // Bahreïn
        case '968': // Oman
        case '962': // Jordanie
        case '961': // Liban
        case '972': // Israël
        case '98': // Iran
        case '964': // Irak
        case '93': // Afghanistan
        case '92': // Pakistan
        case '880': // Bangladesh
        case '94': // Sri Lanka
        case '977': // Népal
        case '95': // Myanmar
        case '855': // Cambodge
        case '856': // Laos
        case '976': // Mongolie
        case '998': // Ouzbékistan
        case '996': // Kirghizistan
        case '992': // Tadjikistan
        case '993': // Turkménistan
        case '995': // Géorgie
        case '374': // Arménie
        case '994': // Azerbaïdjan
        case '373': // Moldavie
        case '375': // Biélorussie
        case '380': // Ukraine
          return `${match[1]} ${match[2]} ${match[3]}`
        default:
          return cleaned
      }
    }
    
    return cleaned
  }

  const handlePhoneInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    const value = target.value
    
    // Garder seulement les chiffres et formater
    const cleaned = value.replace(/\D/g, '')
    formattedPhone.value = formatPhoneNumber(cleaned)
    
    updateModelValue()
  }

  const updateModelValue = () => {
    // Créer le numéro complet au format international
    const cleanPhone = formattedPhone.value.replace(/\D/g, '')
    const fullNumber = cleanPhone ? `${selectedCountry.value.code}${cleanPhone}` : ''
    emit('update:modelValue', fullNumber)
  }

  const handleFocus = () => {
    // Logique de focus si nécessaire
  }

  const handleBlur = () => {
    closeCountrySelector()
  }

  // Initialisation depuis modelValue
  const initializeFromModelValue = () => {
    if (!props.modelValue) return
    
    const value = props.modelValue
    // Essayer de détecter le pays depuis le numéro
    if (value.startsWith('+')) {
      for (const country of countries) {
        if (value.startsWith(country.code)) {
          selectedCountry.value = country
          const phoneWithoutCountryCode = value.substring(country.code.length)
          formattedPhone.value = formatPhoneNumber(phoneWithoutCountryCode, country)
          break
        }
      }
    } else if (value) {
      // Si le numéro ne commence pas par +, essayer de le formater avec le pays par défaut
      formattedPhone.value = formatPhoneNumber(value, selectedCountry.value)
    }
  }

  // Watcher pour les changements de modelValue externes
  watch(() => props.modelValue, (newValue) => {
    if (newValue !== getFullNumber()) {
      initializeFromModelValue()
    }
  })

  // Fonction pour obtenir le numéro complet
  const getFullNumber = () => {
    const cleanPhone = formattedPhone.value.replace(/\D/g, '')
    return cleanPhone ? `${selectedCountry.value.code}${cleanPhone}` : ''
  }

  // Watchers et lifecycle
  onMounted(() => {
    initTheme()
    initializeFromModelValue()
    
    // Fermer le sélecteur avec Escape
    const handleEscape = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        closeCountrySelector()
      }
    }
    
    document.addEventListener('keydown', handleEscape)
    
    onUnmounted(() => {
      document.removeEventListener('keydown', handleEscape)
    })
  })

  // Exposer les méthodes si nécessaire
  defineExpose({
    focus: () => phoneInput.value?.focus(),
    selectCountry,
    getFullNumber
  })
</script>

<style scoped>
  .phone-input-container {
    position: relative;
  }

  /* Animation pour le dropdown */
  .country-dropdown-enter-active,
  .country-dropdown-leave-active {
    transition: all 0.2s ease;
  }

  .country-dropdown-enter-from,
  .country-dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  /* Scrollbar personnalisée pour WebKit */
  .scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
  }

  .scrollbar-thin::-webkit-scrollbar {
    width: 8px;
  }

  .scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
  }

  .scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
    border: 1px solid #f3f4f6;
  }

  .scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }

  .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
    background: #d1d5db;
  }

  .scrollbar-track-gray-100::-webkit-scrollbar-track {
    background: #f3f4f6;
  }

  /* Amélioration de l'UX pour les éléments de la liste */
  .country-item:hover {
    background-color: #f9fafb;
  }

  .country-item:active {
    background-color: #f3f4f6;
  }
</style>


