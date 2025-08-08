<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-white to-blue-50 relative overflow-hidden">
    <AppHeader />
    
    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 -right-32 w-64 h-64 bg-gradient-to-br from-blue-400/20 to-cyan-600/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/4 -left-32 w-80 h-80 bg-gradient-to-tr from-indigo-400/20 to-purple-600/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Modern header with stats and controls -->
    <div class="relative z-20 w-full max-w-7xl mx-auto px-4 pt-6">
      <!-- Stats bar -->
      <div class="mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-white/50 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">{{ t('planning.stats.totalVisits') }}</p>
                <p class="text-xl font-bold text-gray-900">{{ visites.length }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-white/50 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">{{ t('planning.stats.todayVisits') }}</p>
                <p class="text-xl font-bold text-gray-900">{{ todayVisitesCount }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-white/50 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">{{ t('planning.stats.pendingVisits') }}</p>
                <p class="text-xl font-bold text-gray-900">{{ pendingVisitesCount }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-white/50 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">{{ t('planning.stats.activeVisitors') }}</p>
                <p class="text-xl font-bold text-gray-900">{{ activeVisitorsCount }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced controls bar -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <!-- Left side: View controls and filters -->
        <div class="flex flex-wrap items-center gap-3">
          <!-- View switcher -->
          <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-1 border border-white/50 shadow-lg">
            <div class="flex">
              <button 
                v-for="view in availableViews" 
                :key="view.key"
                @click="currentView = view.key"
                :class="[
                  'px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200',
                  currentView === view.key 
                    ? 'bg-blue-600 text-white shadow-md' 
                    : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50'
                ]"
              >
                <svg :class="['w-4 h-4 mr-2 inline-block']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="view.icon"></path>
                </svg>
                {{ view.label }}
              </button>
            </div>
          </div>

          <!-- Filters -->
          <div class="flex gap-2">
            <select 
              v-model="selectedStatusFilter" 
              class="bg-white/90 backdrop-blur-sm border border-white/50 rounded-xl px-4 py-2 text-sm shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">{{ t('planning.filters.all') }}</option>
              <option value="en_attente">{{ t('planning.filters.pending') }}</option>
              <option value="programmee">{{ t('planning.filters.scheduled') }}</option>
              <option value="en_cours">{{ t('planning.filters.inProgress') }}</option>
              <option value="terminee">{{ t('planning.filters.completed') }}</option>
              <option value="annulee">{{ t('planning.filters.cancelled') }}</option>
            </select>

            <input 
              v-model="searchQuery"
              type="text" 
              :placeholder="t('planning.filters.search')"
              class="bg-white/90 backdrop-blur-sm border border-white/50 rounded-xl px-4 py-2 text-sm shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Right side: Week navigation and actions -->
        <div class="flex items-center gap-3">
          <!-- Week navigation -->
          <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-1 border border-white/50 shadow-lg">
            <div class="flex items-center">
              <button 
                @click="prevWeek" 
                class="p-2 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                :title="t('planning.previousWeek')"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              
              <div class="px-4 py-2 min-w-[200px] text-center">
                <p class="text-sm font-semibold text-gray-900">
                  {{ t('planning.weekFrom') }} {{ formatDate(weekDates[0]) }} {{ t('planning.to') }} {{ formatDate(weekDates[6]) }}
                </p>
              </div>
              
              <button 
                @click="nextWeek" 
                class="p-2 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                :title="t('planning.nextWeek')"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Quick actions -->
          <div class="flex gap-2">
            <button 
              @click="goToToday"
              class="bg-white/90 backdrop-blur-sm border border-white/50 rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 shadow-lg transition-colors"
            >
              {{ t('planning.quickActions.goToToday') }}
            </button>

            <button 
              @click="fetchVisites"
              class="bg-white/90 backdrop-blur-sm border border-white/50 rounded-xl p-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 shadow-lg transition-colors"
              :title="t('planning.quickActions.refresh')"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>

            <button 
              @click="openDownloadModal"
              class="bg-white/90 backdrop-blur-sm border border-white/50 rounded-xl p-2 text-gray-700 hover:text-green-600 hover:bg-green-50 shadow-lg transition-colors"
              :title="t('planning.download.button')"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </button>

            <button 
              v-if="!notifOpen"
              @click="openCreationPopup()"
              class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl px-6 py-2 font-medium shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200"
            >
              <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              {{ t('planning.addVisit') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main calendar area -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 flex-1">
      <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden">
        <!-- Week view (default) -->
        <div v-if="currentView === 'week'" class="relative overflow-auto h-[150vh]" ref="scrollDiv">
          <!-- Current time indicator -->
          <div
            class="absolute left-2 text-xs text-red-500 font-semibold z-50 pointer-events-none select-none bg-white/90 px-2 py-1 rounded-full border border-red-200"
            :style="{ top: (currentTimePosition - 12) + 'px' }"
          >
            {{ currentTimeLabel }}
          </div>

          <div
            class="absolute left-20 right-0 h-[2px] bg-gradient-to-r from-red-500 to-red-400 z-40 pointer-events-none rounded-full shadow-sm"
            :style="{ top: currentTimePosition + 'px' }"
          />

          <table class="min-w-full table-fixed border-separate border-spacing-0 text-sm text-gray-700" :class="{ 'selecting': isDragging }">
            <thead>
              <tr class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-gray-200">
                <th class="w-20 p-2"></th>
                <th v-for="(date, idx) in weekDates" :key="idx" class="py-4 px-3 w-36 text-center border-r border-gray-100 last:border-r-0">
                  <div class="flex flex-col items-center">
                    <div :class="[
                      'text-sm font-medium mb-1',
                      dateIsToday(date) ? 'text-blue-600' : 'text-gray-600'
                    ]">
                      {{ days[idx] }}
                    </div>
                    <div :class="[
                      'w-8 h-8 rounded-xl flex items-center justify-center text-sm font-semibold transition-colors',
                      dateIsToday(date) 
                        ? 'bg-blue-600 text-white shadow-lg' 
                        : 'text-gray-700 hover:bg-gray-100'
                    ]">
                      {{ date.getDate() }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                      {{ (date.getMonth()+1).toString().padStart(2, '0') }}
                    </div>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="i in 288"
                :key="i"
                :class="[
                  'group transition-colors duration-150',
                  (i % 24 === 0) ? 'bg-blue-50/30' : (Math.floor((i-1)/12) % 2 === 0 ? 'bg-gray-50/30' : 'bg-white/30'),
                  'hover:bg-blue-50/50'
                ]"
              >
                <td
                  v-if="(i-1) % 12 === 0"
                  class="text-right pr-4 py-0.5 text-xs font-medium text-gray-500 w-20 align-top border-r border-gray-100"
                  :rowspan="12"
                >
                  <div class="bg-white/80 rounded-lg px-2 py-1 shadow-sm">
                    {{ String(Math.floor((i-1)/12)).padStart(2, '0') }}:00
                  </div>
                </td>
                <td
                  v-for="(date, idx) in weekDates"
                  :key="idx"
                  :class="[
                    'h-1 w-36 border-r border-gray-100 last:border-r-0 transition-all duration-150 relative cursor-pointer',
                    dateIsToday(date) ? 'bg-blue-50/40' : '',
                    (i) % 12 === 0 ? 'border-b border-gray-200' : 'border-b border-gray-50',
                    'hover:bg-blue-100/40 group-hover:bg-blue-100/40',
                    isCellInDragSelection(date, Math.floor((i-1)/12), ((i-1)%12)*5) ? 'bg-blue-200/60 border-blue-300' : ''
                  ]"
                  @mousedown="handleCellMouseDown(date, Math.floor((i-1)/12), ((i-1)%12)*5, $event)"
                  @mouseenter="handleCellMouseEnter(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                  @mouseup="handleCellMouseUp"
                  @click="handleCellClick(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                >
                  <template v-if="getVisitesForDisplay(date, Math.floor((i-1)/12), ((i-1)%12)*5).length">
                    <div
                      v-for="(visite, vIdx) in getVisitesForDisplay(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                      :key="visite.id_visite || vIdx"
                      :style="getFullVisiteStyle(visite, vIdx, getVisitesForDisplay(date, Math.floor((i-1)/12), ((i-1)%12)*5).length)"
                      class="visite-block absolute rounded-xl px-2 py-1 text-xs font-medium shadow-lg cursor-pointer border border-white/80 transition-all duration-200 hover:shadow-xl hover:scale-105"
                      :class="getVisiteColor(visite)"
                      @click.stop="handleVisiteClick(visite)"
                    >
                      <div class="flex items-center gap-1 mb-0.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-white/80"></div>
                        <span class="block truncate font-semibold text-[10px]">{{ visite.motif_visite }}</span>
                      </div>
                      <span class="block text-[9px] opacity-90 font-medium">{{ formatVisiteTime(visite) }}</span>
                      <div v-if="getVisitorDisplayName(visite)" class="text-[8px] opacity-75 mt-0.5 truncate">
                        {{ getVisitorDisplayName(visite) }}
                      </div>
                    </div>
                  </template>
                  <template v-if="getVisitesForDisplayWithOverlap(date, Math.floor((i-1)/12), ((i-1)%12)*5).length">
                    <div
                      v-for="visiteObj in getVisitesForDisplayWithOverlap(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                      :key="visiteObj.visite.id_visite || visiteObj.idx"
                      :style="getFullVisiteStyle(visiteObj.visite, visiteObj.idx, visiteObj.total)"
                      class="visite-block absolute rounded-xl px-2 py-1 text-xs font-medium shadow-lg cursor-pointer border border-white/80 transition-all duration-200 hover:shadow-xl hover:scale-105"
                      :class="getVisiteColor(visiteObj.visite)"
                      @click.stop="handleVisiteClick(visiteObj.visite)"
                    >
                      <div class="flex items-center gap-1 mb-0.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-white/80"></div>
                        <span class="block truncate font-semibold text-[10px]">{{ visiteObj.visite.motif_visite }}</span>
                      </div>
                      <span class="block text-[9px] opacity-90 font-medium">{{ formatVisiteTime(visiteObj.visite) }}</span>
                      <div v-if="getVisitorDisplayName(visiteObj.visite)" class="text-[8px] opacity-75 mt-0.5 truncate">
                        {{ getVisitorDisplayName(visiteObj.visite) }}
                      </div>
                    </div>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- List view -->
        <div v-else-if="currentView === 'list'" class="p-6">
          <div class="space-y-4 max-h-[70vh] overflow-auto">
            <div 
              v-for="visite in filteredVisites" 
              :key="visite.id_visite"
              class="bg-white/90 rounded-2xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
              @click="handleVisiteClick(visite)"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div :class="[
                    'w-4 h-4 rounded-full',
                    getVisiteColorClass(visite.statut_visite)
                  ]"></div>
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ visite.motif_visite }}</h3>
                    <p class="text-sm text-gray-600">{{ getVisitorDisplayName(visite) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(visite.date_visite_start) }}</p>
                  <p class="text-xs text-gray-500">{{ formatVisiteTime(visite) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Day view -->
        <div v-else-if="currentView === 'day'" class="p-6">
          <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ formatDateLong(selectedDate) }}</h2>
            <div class="flex justify-center gap-4 mt-4">
              <button @click="selectedDate = addDays(selectedDate, -1)" class="btn-secondary">
                {{ t('planning.quickActions.previous') }}
              </button>
              <button @click="selectedDate = new Date()" class="btn-secondary">
                {{ t('planning.today') }}
              </button>
              <button @click="selectedDate = addDays(selectedDate, 1)" class="btn-secondary">
                {{ t('planning.quickActions.next') }}
              </button>
            </div>
          </div>
          <div v-if="dayVisites.length" class="space-y-4 max-h-[70vh] overflow-auto">
            <div 
              v-for="visite in dayVisites" 
              :key="visite.id_visite"
              class="bg-white/90 rounded-2xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
              @click="handleVisiteClick(visite)"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div :class="['w-4 h-4 rounded-full', getVisiteColorClass(visite.statut_visite)]"></div>
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ visite.motif_visite }}</h3>
                    <p class="text-sm text-gray-600">{{ getVisitorDisplayName(visite) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(visite.date_visite_start) }}</p>
                  <p class="text-xs text-gray-500">{{ formatVisiteTime(visite) }}</p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-400 text-lg mt-12">
            {{ t('planning.noVisitsForDay', 'Aucune visite pour ce jour.') }}
          </div>
        </div>
      </div>

      <!-- Notifications sidebar -->
      <div class="fixed top-20 right-4 z-30">
        <Notif @update:open="notifOpen = $event" />
      </div>
    </div>

    <!-- Modals -->
    <VisitePopup 
      v-if="popupOpen" 
      :visite="popupVisite" 
      @close="popupOpen = false" 
      @statusChanged="handleStatusChanged"
    />
    <CreateVisitePopup 
      v-if="popupCreateOpen" 
      :defaultStart="popupCreateStart" 
      :defaultEnd="popupCreateEnd"
      @close="popupCreateOpen = false" 
      @refresh="fetchVisites" 
    />

    <!-- Download Modal -->
    <div 
      v-if="downloadModalOpen" 
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="downloadModalOpen = false"
    >
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
          <h3 class="text-xl font-bold text-gray-900">
            {{ t('planning.download.modalTitle') }}
          </h3>
          <button 
            @click="downloadModalOpen = false"
            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
          >
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Period Selection -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
              {{ t('planning.download.selectPeriod') }}
            </label>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-500 mb-1">{{ t('planning.download.startDate') }}</label>
                <input 
                  v-model="downloadStartDate" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">{{ t('planning.download.endDate') }}</label>
                <input 
                  v-model="downloadEndDate" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
          </div>

          <!-- Format Selection -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
              {{ t('planning.download.format') }}
            </label>
            <div class="grid grid-cols-1 gap-3">
              <!-- CSV Option -->
              <button 
                @click="downloadFormat = 'csv'"
                :class="[
                  'p-3 rounded-xl border-2 transition-all text-left',
                  downloadFormat === 'csv' 
                    ? 'border-blue-500 bg-blue-50 text-blue-700' 
                    : 'border-gray-200 hover:border-gray-300 text-gray-700'
                ]"
              >
                <div class="flex items-center">
                  <span class="text-lg mr-2">📊</span>
                  <div>
                    <div class="font-medium">{{ t('planning.download.csv') }}</div>
                    <div class="text-xs opacity-75">{{ t('planning.download.csvDesc') }}</div>
                  </div>
                </div>
              </button>
              
              <!-- PDF Downloadable Option -->
              <button 
                @click="downloadFormat = 'pdf-download'"
                :class="[
                  'p-3 rounded-xl border-2 transition-all text-left',
                  downloadFormat === 'pdf-download' 
                    ? 'border-green-500 bg-green-50 text-green-700' 
                    : 'border-gray-200 hover:border-gray-300 text-gray-700'
                ]"
              >
                <div class="flex items-center">
                  <span class="text-lg mr-2">💾</span>
                  <div>
                    <div class="font-medium">{{ t('planning.download.pdfDownload') }}</div>
                    <div class="text-xs opacity-75">{{ t('planning.download.pdfDownloadDesc') }}</div>
                  </div>
                </div>
              </button>
              
              <!-- PDF Printable Option -->
              <button 
                @click="downloadFormat = 'pdf-print'"
                :class="[
                  'p-3 rounded-xl border-2 transition-all text-left',
                  downloadFormat === 'pdf-print' 
                    ? 'border-purple-500 bg-purple-50 text-purple-700' 
                    : 'border-gray-200 hover:border-gray-300 text-gray-700'
                ]"
              >
                <div class="flex items-center">
                  <span class="text-lg mr-2">🖨️</span>
                  <div>
                    <div class="font-medium">{{ t('planning.download.pdfPrint') }}</div>
                    <div class="text-xs opacity-75">{{ t('planning.download.pdfPrintDesc') }}</div>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 p-6 border-t border-gray-100">
          <button 
            @click="downloadModalOpen = false"
            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-colors"
          >
            {{ t('planning.download.cancel') }}
          </button>
          <button 
            @click="downloadPlanning"
            :disabled="isDownloading || !downloadStartDate || !downloadEndDate"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2"
          >
            <svg 
              v-if="isDownloading" 
              class="w-4 h-4 animate-spin" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isDownloading ? t('planning.download.downloading') : t('planning.download.download') }}
          </button>
        </div>
      </div>
    </div>
    
    <div class="h-8"></div>
    <AppFooter />
  </div>
</template>

<script setup>
  import { useI18n } from 'vue-i18n'
  const { t } = useI18n()

  useHead({
    title: computed(() => t('planning.pageTitle'))
  })

  import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
  import AppHeader from '~/components/AppHeader.vue'
  import AppFooter from '~/components/AppFooter.vue'
  import Notif from '@/components/notif.vue'
  import VisitePopup from '@/components/VisitePopup.vue'
  import CreateVisitePopup from '@/components/CreateVisitePopup.vue'
  import { useAuthStore } from '@/stores/auth'

  // Fonction de filtrage centralisée
  function applyFilters(visitesArray) {
    let filtered = visitesArray

    if (selectedStatusFilter.value) {
      filtered = filtered.filter(visite => visite.statut_visite === selectedStatusFilter.value)
    }

    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(visite => 
        visite.motif_visite?.toLowerCase().includes(query) ||
        visite.email_visiteur?.toLowerCase().includes(query) ||
        visite.nom_visiteur?.toLowerCase().includes(query) ||
        visite.prenom_visiteur?.toLowerCase().includes(query) ||
        visite.nom_invite?.toLowerCase().includes(query) ||
        visite.prenom_invite?.toLowerCase().includes(query)
      )
    }

    return filtered
  }

  // Visites du jour sélectionné (pour la vue "day")
  const dayVisites = computed(() => {
    if (!selectedDate.value) return []
    const dayStart = new Date(selectedDate.value)
    dayStart.setHours(0, 0, 0, 0)
    const dayEnd = new Date(selectedDate.value)
    dayEnd.setHours(23, 59, 59, 999)
    const dayVisitesUnfiltered = visites.value.filter(visite => {
      if (!visite.date_visite_start) return false
      const start = new Date(visite.date_visite_start)
      const end = visite.date_visite_end ? new Date(visite.date_visite_end) : start
      return (
        (start >= dayStart && start <= dayEnd) ||
        (end >= dayStart && end <= dayEnd) ||
        (start <= dayStart && end >= dayEnd) // chevauchement sur la journée
      )
    })
    return applyFilters(dayVisitesUnfiltered).sort((a, b) => new Date(a.date_visite_start) - new Date(b.date_visite_start))
  })

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  // View management
  const currentView = ref('week')
  const availableViews = [
    { 
      key: 'week', 
      label: t('planning.views.week'),
      icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    },
    { 
      key: 'day', 
      label: t('planning.views.day'),
      icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'
    },
    { 
      key: 'list', 
      label: t('planning.views.list'),
      icon: 'M4 6h16M4 10h16M4 14h16M4 18h16'
    }
  ]

  // Filters and search
  const selectedStatusFilter = ref('')
  const searchQuery = ref('')

  // Calendar data
  const jours = computed(() => [
    t('planning.days.monday', 'Lun'),
    t('planning.days.tuesday', 'Mar'), 
    t('planning.days.wednesday', 'Mer'),
    t('planning.days.thursday', 'Jeu'),
    t('planning.days.friday', 'Ven'),
    t('planning.days.saturday', 'Sam'),
    t('planning.days.sunday', 'Dim')
  ])
  const days = jours
  const today = new Date()
  const weekOffset = ref(0)
  const selectedDate = ref(new Date())

  function getStartOfWeek(date) {
    const d = new Date(date)
    const day = d.getDay() || 7
    d.setHours(0, 0, 0, 0)
    d.setDate(d.getDate() - (day - 1))
    return d
  }

  const weekDates = computed(() => {
    const start = getStartOfWeek(new Date(today.getFullYear(), today.getMonth(), today.getDate()))
    start.setDate(start.getDate() + weekOffset.value * 7)
    return Array.from({ length: 7 }, (_, i) => {
      const d = new Date(start)
      d.setDate(start.getDate() + i)
      return d
    })
  })

  // Stats computed properties
  const todayVisitesCount = computed(() => {
    const today = new Date()
    return visites.value.filter(visite => {
      if (!visite.date_visite_start) return false
      const visiteDate = new Date(visite.date_visite_start)
      if (!(visiteDate instanceof Date) || isNaN(visiteDate.getTime())) return false
      return visiteDate.toDateString() === today.toDateString()
    }).length
  })

  const pendingVisitesCount = computed(() => {
    return visites.value.filter(visite => 
      visite.statut_visite === 'en_attente' || visite.statut_visite === 'programmee'
    ).length
  })

  const activeVisitorsCount = computed(() => {
    return visites.value.filter(visite => visite.statut_visite === 'en_cours').length
  })

  // Filtered visits for list view
  const filteredVisites = computed(() => {
    return applyFilters(visites.value).sort((a, b) => new Date(a.date_visite_start) - new Date(b.date_visite_start))
  })

  function formatDate(date) {
    if (!date || !(date instanceof Date)) {
      return ''
    }
    return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth()+1).toString().padStart(2, '0')}`
  }

  function formatDateLong(date) {
    if (!date || !(date instanceof Date)) {
      return ''
    }
    return date.toLocaleDateString('fr-FR', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    })
  }

  function addDays(date, days) {
    if (!date || !(date instanceof Date)) {
      return new Date()
    }
    const result = new Date(date)
    result.setDate(result.getDate() + days)
    return result
  }

  function dateIsToday(date) {
    if (!date || !(date instanceof Date)) {
      return false
    }
    const now = new Date()
    return date.getDate() === now.getDate() && 
          date.getMonth() === now.getMonth() && 
          date.getFullYear() === now.getFullYear()
  }

  function prevWeek() { weekOffset.value-- }
  function nextWeek() { weekOffset.value++ }

  function goToToday() {
    weekOffset.value = 0
    selectedDate.value = new Date()
  }

  const scrollDiv = ref(null)

  // Visit management
  const visites = ref([])

  async function fetchVisites() {
    try {
      const res = await fetch(`${config.public.apiBase}/visites`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      })
      const data = await res.json()
      if (res.ok && data.success) {
        const user = authStore.user || {}
        const isAdmin = user.role === 'admin' || user.niveau_acces === 'super_admin' || user.niveau_acces === 'admin_standard'
        const isGardien = user.role === 'gardien'
        
        if (isAdmin || isGardien) {
          visites.value = data.visites.filter(v => v.statut_visite !== 'annule')
        } else {
          visites.value = data.visites.filter(v => 
            v.statut_visite !== 'annule' && 
            (v.id_personne === user.id_personne || v.id_invite === user.id_personne)
          )
        }
      }
    } catch (error) {
      console.error('Error fetching visits:', error)
      visites.value = []
    }
  }

  // Current time indicator
  const currentTimeLabel = ref('')
  const currentTimePosition = ref(0)

  function updateCurrentTimePosition() {
    const now = new Date()
    const minutesSinceMidnight = now.getHours() * 60 + now.getMinutes()
    const totalMinutesInDay = 24 * 60
    // Nouvelle réduction de la hauteur du tableau : passage de 28 * 16 à 28 * 10.67 (car h-1 = 4px au lieu de h-1.5 = 6px)
    const tableHeight = 28 * 10.67
    currentTimePosition.value = (minutesSinceMidnight / totalMinutesInDay) * tableHeight
    currentTimeLabel.value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
  }

  // Visit display functions
  function getSlotStart(date, hour, minute) {
    const slotStart = new Date(date)
    slotStart.setHours(hour, minute, 0, 0)
    return slotStart
  }

  function visitesForCell(date, hour, minute) {
    if (!date || !(date instanceof Date)) return []
    const slotStart = getSlotStart(date, hour, minute)
    const slotEnd = new Date(slotStart)
    slotEnd.setMinutes(slotEnd.getMinutes() + 5)
    // On renvoie toutes les visites qui chevauchent ce créneau
    const cellVisites = visites.value.filter(visite => {
      if (!visite.date_visite_start || !visite.date_visite_end) return false
      const visiteStart = new Date(visite.date_visite_start)
      const visiteEnd = new Date(visite.date_visite_end)
      if (isNaN(visiteStart.getTime()) || isNaN(visiteEnd.getTime())) return false
      // La visite doit commencer avant la fin du slot ET finir après le début du slot
      return visiteStart < slotEnd && visiteEnd > slotStart
    })
    // Appliquer les filtres de statut et de recherche
    return applyFilters(cellVisites).sort((a, b) => new Date(a.date_visite_start) - new Date(b.date_visite_start))
  }

  // Renvoie seulement les visites qui doivent être affichées dans ce slot
  function getVisitesForDisplay(date, hour, minute) {
    return visitesForCell(date, hour, minute).filter(visite => 
      isFirstSlotForVisite(visite, date, hour, minute)
    )
  }

  // Renvoie [{visite, idx, total}] pour gérer les chevauchements dans ce slot
  function getVisitesForDisplayWithOverlap(date, hour, minute) {
    const visites = getVisitesForDisplay(date, hour, minute)
    if (visites.length <= 1) {
      return visites.map((v, i) => ({ visite: v, idx: 0, total: 1 }))
    }
    // Pour chaque visite, trouver les autres qui se chevauchent avec elle
    // On construit les groupes de chevauchement
    const overlapGroups = []
    visites.forEach((visite, i) => {
      const visiteStart = new Date(visite.date_visite_start)
      const visiteEnd = new Date(visite.date_visite_end)
      let group = null
      // Chercher un groupe existant qui chevauche
      for (const g of overlapGroups) {
        if (g.some(v2 => {
          const v2Start = new Date(v2.date_visite_start)
          const v2End = new Date(v2.date_visite_end)
          return (visiteStart < v2End && visiteEnd > v2Start)
        })) {
          group = g
          break
        }
      }
      if (group) {
        group.push(visite)
      } else {
        overlapGroups.push([visite])
      }
    })
    // Pour chaque visite, donner son index dans son groupe et la taille du groupe
    const result = []
    overlapGroups.forEach(group => {
      group.forEach((visite, idx) => {
        result.push({ visite, idx, total: group.length })
      })
    })
    // Garder l'ordre d'origine
    return visites.map(v => result.find(r => r.visite === v))
  }

  function getFullVisiteStyle(visite, idx, totalVisitesInSlot) {
    if (!visite || !visite.date_visite_start || !visite.date_visite_end) {
      return {
        height: '0.25rem',
        width: '100%',
        left: '0',
        top: '0',
        zIndex: 10,
      }
    }
    const visiteStart = new Date(visite.date_visite_start)
    const visiteEnd = new Date(visite.date_visite_end)
    if (isNaN(visiteStart.getTime()) || isNaN(visiteEnd.getTime())) {
      return {
        height: '0.25rem',
        width: '100%',
        left: '0',
        top: '0',
        zIndex: 10,
      }
    }
    // Calculer la durée totale de la visite en minutes
    const diffMs = visiteEnd - visiteStart
    const totalMinutes = Math.floor(diffMs / 60000)
    // Calculer la hauteur basée sur la durée totale (chaque slot de 5 min = 0.08rem de hauteur au lieu de 0.12rem)
    const height = Math.max((totalMinutes / 5) * 0.08, 0.25)
    // Gestion des chevauchements horizontaux
    const overlapCount = visite.overlapCount || 1
    const overlapIdx = visite.overlapIdx || 0
    if (overlapCount > 1) {
      const baseWidth = Math.floor(100 / overlapCount)
      const remainingWidth = 100 - (baseWidth * overlapCount)
      const width = `${baseWidth + (overlapIdx < remainingWidth ? 1 : 0)}%`
      const left = `${overlapIdx * baseWidth + Math.min(overlapIdx, remainingWidth)}%`
      return {
        height: height + 'rem',
        width,
        left,
        top: '0',
        zIndex: 10 + overlapIdx,
        marginLeft: overlapIdx > 0 ? '1px' : '0',
      }
    }
    return {
      height: height + 'rem',
      width: '100%',
      left: '0',
      top: '0',
      zIndex: 10,
    }
  }

  function getVisiteStyle(visite, idx, total, currentSlotStart) {
    // Calcule la hauteur visible de la visite dans ce slot (pour multi-jours)
    const visiteStart = new Date(visite.date_visite_start)
    const visiteEnd = new Date(visite.date_visite_end)
    const slotStart = new Date(currentSlotStart)
    const slotEnd = new Date(slotStart)
    slotEnd.setMinutes(slotEnd.getMinutes() + 5)
    // Portion visible dans ce slot
    const effectiveStart = visiteStart > slotStart ? visiteStart : slotStart
    const effectiveEnd = visiteEnd < slotEnd ? visiteEnd : slotEnd
    const diffMs = effectiveEnd - effectiveStart
    const minutes = Math.max(0, Math.floor(diffMs / 60000))
    const height = Math.max((minutes / 5) * 0.12, 0.375)
    // Décalage vertical si la visite commence après le début du slot
    const minutesDiff = Math.max(0, (visiteStart - slotStart) / (1000 * 60))
    const topOffset = (minutesDiff / 5) * 0.12
    // Gestion des chevauchements
    if (total > 1) {
      const baseWidth = Math.floor(100 / total)
      const remainingWidth = 100 - (baseWidth * total)
      const width = `${baseWidth + (idx < remainingWidth ? 1 : 0)}%`
      const left = `${idx * baseWidth + Math.min(idx, remainingWidth)}%`
      return {
        height: height + 'rem',
        width,
        left,
        top: topOffset + 'rem',
        zIndex: 10 + idx,
        marginLeft: idx > 0 ? '1px' : '0',
      }
    }
    return {
      height: height + 'rem',
      width: '100%',
      left: '0',
      top: topOffset + 'rem',
      zIndex: 10,
    }
  }

  function getVisiteHeight(visite) {
    if (!visite || !visite.date_visite_start || !visite.date_visite_end) return 0.375
    
    const start = new Date(visite.date_visite_start)
    const end = new Date(visite.date_visite_end)
    
    if (!(start instanceof Date) || !(end instanceof Date) || isNaN(start.getTime()) || isNaN(end.getTime())) {
      return 0.375
    }
    
    const diffMs = end - start
    const minutes = Math.floor(diffMs / 60000)
    // Nouvelle réduction de la hauteur : passage de 0.15 à 0.11 pour correspondre aux cellules plus petites
    return Math.max((minutes / 5) * 0.11, 0.375)
  }

  function getVisiteColor(visite) {
    if (!visite) return 'bg-gray-400'
    
    const baseColors = {
      'programmee': 'bg-gradient-to-br from-blue-500 to-blue-600 text-white',
      'en_cours': 'bg-gradient-to-br from-green-500 to-green-600 text-white',
      'terminee': 'bg-gradient-to-br from-gray-500 to-gray-600 text-white',
      'en_attente': 'bg-gradient-to-br from-yellow-500 to-yellow-600 text-white',
      'annulee': 'bg-gradient-to-br from-red-500 to-red-600 text-white'
    }
    
    return baseColors[visite.statut_visite] || 'bg-gradient-to-br from-gray-500 to-gray-600 text-white'
  }

  function getVisiteColorClass(status) {
    const colorClasses = {
      'programmee': 'bg-blue-500',
      'en_cours': 'bg-green-500',
      'terminee': 'bg-gray-500',
      'en_attente': 'bg-yellow-500',
      'annulee': 'bg-red-500'
    }
    
    return colorClasses[status] || 'bg-gray-500'
  }

  function formatVisiteTime(visite) {
    const start = new Date(visite.date_visite_start)
    const end = new Date(visite.date_visite_end)
    return `${start.getHours().toString().padStart(2, '0')}:${start.getMinutes().toString().padStart(2, '0')} - ${end.getHours().toString().padStart(2, '0')}:${end.getMinutes().toString().padStart(2, '0')}`
  }

  function getVisitorDisplayName(visite) {
    if (!visite) return ''
    
    // Priorité aux noms/prénoms si disponibles
    if (visite.nom_visiteur && visite.prenom_visiteur) {
      return `${visite.prenom_visiteur} ${visite.nom_visiteur}`
    }
    
    // Sinon afficher l'email comme fallback
    return visite.email_visiteur || ''
  }

  // Popup management
  const popupOpen = ref(false)
  const popupVisite = ref(null)

  function handleVisiteClick(visite) {
    popupVisite.value = visite
    popupOpen.value = true
  }

  // Handle status change from VisitePopup
  function handleStatusChanged(event) {
    console.log('Visit status changed:', event)
    // Refresh visits to get updated data
    fetchVisites()
    // You could also update the local visites array directly:
    // const visite = visites.value.find(v => v.id_visite === event.visitId)
    // if (visite) {
    //   visite.statut_visite = event.newStatus
    // }
  }

  const popupCreateOpen = ref(false)
  const popupCreateStart = ref('')
  const popupCreateEnd = ref('')

  // Drag selection for visit creation
  const isDragging = ref(false)
  const dragStart = ref({ date: null, hour: 0, minute: 0 })
  const dragEnd = ref({ date: null, hour: 0, minute: 0 })

  function openCreationPopup(startTime = '', endTime = '') {
    popupCreateStart.value = startTime
    popupCreateEnd.value = endTime
    popupCreateOpen.value = true
  }

  function handleCellMouseDown(date, hour, minute, event) {
    // Éviter le conflit avec les clics sur les visites existantes
    if (event.target.closest('.visite-block')) {
      return
    }
    
    isDragging.value = true
    dragStart.value = { date: new Date(date), hour, minute }
    dragEnd.value = { date: new Date(date), hour, minute }
    
    // Empêcher la sélection de texte pendant le drag
    event.preventDefault()
  }

  function handleCellMouseEnter(date, hour, minute) {
    if (isDragging.value) {
      dragEnd.value = { date: new Date(date), hour, minute }
    }
  }

  function handleCellMouseUp() {
    if (isDragging.value) {
      isDragging.value = false
      
      // Calculer les heures de début et de fin
      const startDate = new Date(dragStart.value.date)
      startDate.setHours(dragStart.value.hour, dragStart.value.minute, 0, 0)
      
      const endDate = new Date(dragEnd.value.date)
      endDate.setHours(dragEnd.value.hour, dragEnd.value.minute, 0, 0)
      
      // S'assurer que la date de fin est après la date de début
      if (endDate <= startDate) {
        endDate.setMinutes(endDate.getMinutes() + 5) // Minimum 5 minutes
      }
      
      // Ouvrir le popup avec les heures prédéfinies
      openCreationPopup(
        startDate.toISOString().slice(0, 16),
        endDate.toISOString().slice(0, 16)
      )
    }
  }

  function handleCellClick(date, hour, minute) {
    // Ne créer une visite avec clic simple que si on n'était pas en train de faire un drag
    if (!isDragging.value) {
      const clickedDate = new Date(date)
      clickedDate.setHours(hour, minute, 0, 0)
      openCreationPopup(clickedDate.toISOString().slice(0, 16))
    }
  }

  // Gérer le mouseup global pour arrêter le drag même si on sort de la zone
  function handleGlobalMouseUp() {
    if (isDragging.value) {
      handleCellMouseUp()
    }
  }

  // Fonction pour vérifier si une cellule est dans la sélection de drag
  function isCellInDragSelection(date, hour, minute) {
    if (!isDragging.value) return false
    
    const cellDate = new Date(date)
    cellDate.setHours(hour, minute, 0, 0)
    
    const startDate = new Date(dragStart.value.date)
    startDate.setHours(dragStart.value.hour, dragStart.value.minute, 0, 0)
    
    const endDate = new Date(dragEnd.value.date)
    endDate.setHours(dragEnd.value.hour, dragEnd.value.minute, 0, 0)
    
    // S'assurer que start <= end
    const actualStart = startDate <= endDate ? startDate : endDate
    const actualEnd = startDate <= endDate ? endDate : startDate
    
    return cellDate >= actualStart && cellDate <= actualEnd
  }

  const notifOpen = ref(false)

  // Download functionality
  const downloadModalOpen = ref(false)
  const downloadStartDate = ref('')
  const downloadEndDate = ref('')
  const downloadFormat = ref('csv')
  const isDownloading = ref(false)

  function openDownloadModal() {
    // Set default dates to current week
    const startOfWeek = new Date(weekDates.value[0])
    const endOfWeek = new Date(weekDates.value[6])
    downloadStartDate.value = startOfWeek.toISOString().split('T')[0]
    downloadEndDate.value = endOfWeek.toISOString().split('T')[0]
    downloadModalOpen.value = true
  }

  async function downloadPlanning() {
    if (!downloadStartDate.value || !downloadEndDate.value) return
    
    try {
      isDownloading.value = true
      
      // Get filtered visits for the selected period
      const startDate = new Date(downloadStartDate.value)
      startDate.setHours(0, 0, 0, 0)
      const endDate = new Date(downloadEndDate.value)
      endDate.setHours(23, 59, 59, 999)
      
      const visitesForPeriod = visites.value.filter(visite => {
        if (!visite.date_visite_start) return false
        const visiteDate = new Date(visite.date_visite_start)
        return visiteDate >= startDate && visiteDate <= endDate
      })
      
      // Apply current filters to the period visits
      const filteredVisitesForPeriod = applyFilters(visitesForPeriod)
      
      if (downloadFormat.value === 'csv') {
        downloadAsCSV(filteredVisitesForPeriod)
      } else if (downloadFormat.value === 'pdf-download') {
        await downloadAsPDF(filteredVisitesForPeriod)
      } else if (downloadFormat.value === 'pdf-print') {
        downloadAsPDFPrint(filteredVisitesForPeriod)
      }
      
      downloadModalOpen.value = false
      
    } catch (error) {
      console.error('Error downloading planning:', error)
      alert(t('planning.download.error'))
    } finally {
      isDownloading.value = false
    }
  }

  function downloadAsCSV(visitesData) {
    const headers = [
      'Date de début',
      'Heure de début', 
      'Date de fin',
      'Heure de fin',
      'Motif',
      'Statut',
      'Visiteur',
      'Email visiteur',
      'Résident',
      'Durée (minutes)'
    ]
    
    const rows = visitesData.map(visite => [
      formatDate(new Date(visite.date_visite_start)),
      new Date(visite.date_visite_start).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
      formatDate(new Date(visite.date_visite_end)),
      new Date(visite.date_visite_end).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
      visite.motif_visite || '',
      visite.statut_visite || '',
      getVisitorDisplayName(visite),
      visite.email_visiteur || '',
      `${visite.prenom_invite || ''} ${visite.nom_invite || ''}`.trim(),
      Math.round((new Date(visite.date_visite_end) - new Date(visite.date_visite_start)) / 60000)
    ])
    
    const csvContent = [headers, ...rows]
      .map(row => row.map(cell => `"${cell}"`).join(';'))
      .join('\n')
    
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = `planning_visites_${downloadStartDate.value}_${downloadEndDate.value}.csv`
    link.click()
  }

  async function downloadAsPDF(visitesData) {
    try {
      // Import jsPDF dynamically
      const { jsPDF } = await import('jspdf')
      await import('jspdf-autotable')
      
      // Create new PDF document
      const doc = new jsPDF()
      
      // Set document properties
      doc.setProperties({
        title: 'Planning des visites',
        subject: 'Planning des visites de la résidence',
        author: 'Gestion Résidence',
        creator: 'Application de gestion'
      })
      
      // Add title
      doc.setFontSize(18)
      doc.setFont(undefined, 'bold')
      doc.text('Planning des visites', doc.internal.pageSize.getWidth() / 2, 25, { align: 'center' })
      
      // Add period info
      doc.setFontSize(12)
      doc.setFont(undefined, 'normal')
      const periodText = `Période: ${formatDateLong(new Date(downloadStartDate.value))} - ${formatDateLong(new Date(downloadEndDate.value))}`
      doc.text(periodText, doc.internal.pageSize.getWidth() / 2, 40, { align: 'center' })
      
      // Prepare table data
      const tableColumns = [
        { header: 'Date', dataKey: 'date' },
        { header: 'Heure', dataKey: 'heure' },
        { header: 'Motif', dataKey: 'motif' },
        { header: 'Statut', dataKey: 'statut' },
        { header: 'Visiteur', dataKey: 'visiteur' },
        { header: 'Résident', dataKey: 'resident' },
        { header: 'Durée', dataKey: 'duree' }
      ]
      
      const tableRows = visitesData.map(visite => ({
        date: formatDate(new Date(visite.date_visite_start)),
        heure: formatVisiteTime(visite),
        motif: visite.motif_visite || '',
        statut: visite.statut_visite || '',
        visiteur: getVisitorDisplayName(visite),
        resident: `${visite.prenom_invite || ''} ${visite.nom_invite || ''}`.trim(),
        duree: getDuree(visite.date_visite_start, visite.date_visite_end)
      }))
      
      // Add table using autoTable
      doc.autoTable({
        columns: tableColumns,
        body: tableRows,
        startY: 55,
        theme: 'grid',
        headStyles: {
          fillColor: [41, 128, 185],
          textColor: 255,
          fontStyle: 'bold',
          fontSize: 11
        },
        bodyStyles: {
          fontSize: 10,
          textColor: 50
        },
        alternateRowStyles: {
          fillColor: [245, 245, 245]
        },
        columnStyles: {
          0: { cellWidth: 25 }, // Date
          1: { cellWidth: 25 }, // Heure
          2: { cellWidth: 35 }, // Motif
          3: { cellWidth: 25 }, // Statut
          4: { cellWidth: 35 }, // Visiteur
          5: { cellWidth: 35 }, // Résident
          6: { cellWidth: 20 }  // Durée
        },
        margin: { top: 55, left: 10, right: 10 },
        didDrawPage: function (data) {
          // Add page numbers
          const pageCount = doc.internal.getNumberOfPages()
          doc.setFontSize(10)
          doc.text(
            `Page ${data.pageNumber} sur ${pageCount}`,
            doc.internal.pageSize.getWidth() - 30,
            doc.internal.pageSize.getHeight() - 10,
            { align: 'right' }
          )
        }
      })
      
      // Add footer with generation date
      const finalY = doc.lastAutoTable.finalY || 60
      doc.setFontSize(8)
      doc.setTextColor(150)
      doc.text(
        `Généré le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}`,
        10,
        doc.internal.pageSize.getHeight() - 10
      )
      
      // Generate filename
      const filename = `planning_visites_${downloadStartDate.value}_${downloadEndDate.value}.pdf`
      
      // Create blob and URL for the PDF
      const pdfBlob = doc.output('blob')
      const pdfUrl = URL.createObjectURL(pdfBlob)
      
      // Open PDF in new tab instead of downloading directly
      const newWindow = window.open(pdfUrl, '_blank')
      if (newWindow) {
        newWindow.document.title = filename
        // Clean up the URL after a delay to prevent memory leaks
        setTimeout(() => {
          URL.revokeObjectURL(pdfUrl)
        }, 60000) // Clean up after 1 minute
      } else {
        // Fallback: if popup blocked, download directly
        const link = document.createElement('a')
        link.href = pdfUrl
        link.download = filename
        link.click()
        URL.revokeObjectURL(pdfUrl)
      }
      
      console.log('PDF ouvert dans un nouvel onglet:', filename)
      
    } catch (error) {
      console.error('Erreur lors de la génération du PDF:', error)
      // Fallback to print method if PDF generation fails
      downloadAsPDFPrint(visitesData)
    }
  }
  
  // Fallback print method
  function downloadAsPDFPrint(visitesData) {
    let tableRows = ''
    for (const visite of visitesData) {
      tableRows += '<tr>'
      tableRows += '<td>' + formatDate(new Date(visite.date_visite_start)) + '</td>'
      tableRows += '<td>' + formatVisiteTime(visite) + '</td>'
      tableRows += '<td>' + (visite.motif_visite || '') + '</td>'
      tableRows += '<td>' + (visite.statut_visite || '') + '</td>'
      tableRows += '<td>' + getVisitorDisplayName(visite) + '</td>'
      tableRows += '<td>' + (visite.prenom_invite || '') + ' ' + (visite.nom_invite || '') + '</td>'
      tableRows += '<td>' + getDuree(visite.date_visite_start, visite.date_visite_end) + '</td>'
      tableRows += '</tr>'
    }
    
    const printWindow = window.open('', '_blank')
    printWindow.document.write([
      '<!DOCTYPE html>',
      '<html>',
      '<head>',
      '<title>Planning des visites</title>',
      '<style>',
      'body{font-family:Arial,sans-serif;margin:20px}',
      'h1{text-align:center}',
      'table{width:100%;border-collapse:collapse}',
      'th,td{border:1px solid #ddd;padding:8px;text-align:left}',
      'th{background-color:#f2f2f2}',
      '@media print{body{margin:0}}',
      '</style>',
      '</head>',
      '<body>',
      '<h1>Planning des visites</h1>',
      '<p><strong>Période:</strong> ' + formatDateLong(new Date(downloadStartDate.value)) + ' - ' + formatDateLong(new Date(downloadEndDate.value)) + '</p>',
      '<table>',
      '<thead>',
      '<tr><th>Date</th><th>Heure</th><th>Motif</th><th>Statut</th><th>Visiteur</th><th>Résident</th><th>Durée</th></tr>',
      '</thead>',
      '<tbody>',
      tableRows,
      '</tbody>',
      '</table>',
      '<script>window.onload=function(){window.print();setTimeout(function(){window.close()},1000)}<\/script>',
      '</body>',
      '</html>'
    ].join(''))
    printWindow.document.close()
  }

  function getDuree(start, end) {
    if (!start || !end) return '—'
    const s = new Date(start)
    const e = new Date(end)
    const min = Math.round((e - s) / 60000)
    if (min < 60) return min + ' min'
    const h = Math.floor(min / 60)
    const m = min % 60
    return m > 0 ? `${h}h${m.toString().padStart(2, '0')}` : `${h}h`
  }

  // Lifecycle
  onMounted(async () => {
    await fetchVisites()
    
    nextTick(() => {
      if (scrollDiv.value) {
        scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight / 4.5
      }
    })
    
    updateCurrentTimePosition()
    setInterval(updateCurrentTimePosition, 60000)
    
    // Ajouter l'écouteur global pour mouseup
    document.addEventListener('mouseup', handleGlobalMouseUp)
  })

  // Nettoyer l'écouteur lors de la destruction du composant
  onUnmounted(() => {
    document.removeEventListener('mouseup', handleGlobalMouseUp)
  })

  // Auto-refresh visits every 30 seconds
  setInterval(() => {
    fetchVisites()
  }, 30000)

  // Détermine si ce slot est le premier slot où la visite doit être affichée
  function isFirstSlotForVisite(visite, date, hour, minute) {
    if (!visite || !visite.date_visite_start) return false
    const visiteStart = new Date(visite.date_visite_start)
    const visiteDate = new Date(date)
    // Obtenir le début et la fin de la journée courante
    const dayStart = new Date(visiteDate.getFullYear(), visiteDate.getMonth(), visiteDate.getDate(), 0, 0, 0, 0)
    const dayEnd = new Date(visiteDate.getFullYear(), visiteDate.getMonth(), visiteDate.getDate(), 23, 59, 59, 999)
    // Si la visite commence ce jour-là, afficher seulement au premier slot qui correspond à son heure de début
    if (visiteStart >= dayStart && visiteStart <= dayEnd) {
      const startHour = visiteStart.getHours()
      const startMinute = Math.floor(visiteStart.getMinutes() / 5) * 5 // Arrondir aux slots de 5 min
      return hour === startHour && minute === startMinute
    }
    // Si la visite a commencé avant ce jour, afficher seulement au premier slot de la journée (00:00)
    if (visiteStart < dayStart) {
      return hour === 0 && minute === 0
    }
    return false
  }
</script>


<style scoped>
  /* Modern planning styles with glassmorphism and smooth animations */
  .visite-block {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
      0 4px 6px -1px rgba(0, 0, 0, 0.1),
      0 2px 4px -1px rgba(0, 0, 0, 0.06),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .visite-block:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
    z-index: 100 !important;
  }

  /* Smooth scrolling */
  .overflow-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
  }

  .overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }

  .overflow-auto::-webkit-scrollbar-track {
    background: transparent;
  }

  .overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 3px;
  }

  .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
  }

  /* Enhanced table styling */
  table {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.1) 0%,
      rgba(255, 255, 255, 0.05) 100%
    );
  }

  thead tr {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.9) 0%,
      rgba(248, 250, 252, 0.9) 100%
    );
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.5);
  }

  tbody tr:hover {
    background: linear-gradient(
      135deg,
      rgba(59, 130, 246, 0.05) 0%,
      rgba(147, 197, 253, 0.05) 100%
    );
  }

  /* Time slot styling */
  td[rowspan] div {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.9) 0%,
      rgba(248, 250, 252, 0.8) 100%
    );
    backdrop-filter: blur(4px);
    border: 1px solid rgba(226, 232, 240, 0.3);
  }

  /* Modern button styling */
  .btn-secondary {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(4px);
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
  }

  .btn-secondary:hover {
    color: #2563eb;
    background: rgba(239, 246, 255, 0.9);
  }

  /* Current time indicator enhancement */
  .absolute.left-2 {
    background: linear-gradient(
      135deg,
      rgba(239, 68, 68, 0.9) 0%,
      rgba(220, 38, 38, 0.9) 100%
    );
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
  }

  /* Enhanced card hover effects */
  .hover\:shadow-xl:hover {
    box-shadow: 
      0 25px 50px -12px rgba(0, 0, 0, 0.15),
      0 0 0 1px rgba(255, 255, 255, 0.1);
  }

  /* Improved animations */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
  }

  /* Status-specific visit styling */
  .visite-block.bg-gradient-to-br.from-blue-500 {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-color: rgba(59, 130, 246, 0.3);
  }

  .visite-block.bg-gradient-to-br.from-green-500 {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    border-color: rgba(16, 185, 129, 0.3);
  }

  .visite-block.bg-gradient-to-br.from-yellow-500 {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-color: rgba(245, 158, 11, 0.3);
  }

  .visite-block.bg-gradient-to-br.from-red-500 {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-color: rgba(239, 68, 68, 0.3);
  }

  .visite-block.bg-gradient-to-br.from-gray-500 {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    border-color: rgba(107, 114, 128, 0.3);
  }

  /* List view styling */
  .space-y-4 > * + * {
    margin-top: 1rem;
  }

  /* Mobile responsiveness */
  @media (max-width: 768px) {
    .visite-block {
      font-size: 9px;
      padding: 0.125rem;
    }
    
    .w-36 {
      width: 120px;
    }
    
    th, td {
      padding: 0.25rem 0.125rem;
    }
  }

  /* Enhanced focus states */
  button:focus-visible,
  select:focus-visible,
  input:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
  }

  /* Loading states */
  .loading {
    position: relative;
    overflow: hidden;
  }

  .loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
      90deg,
      transparent,
      rgba(255, 255, 255, 0.4),
      transparent
    );
    animation: loading 1.5s infinite;
  }

  @keyframes loading {
    0% {
      left: -100%;
    }
    100% {
      left: 100%;
    }
  }

  /* Modern glass cards */
  .stats-card {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.2) 0%,
      rgba(255, 255, 255, 0.1) 100%
    );
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
      0 8px 32px 0 rgba(31, 38, 135, 0.15),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
  }

  /* Enhanced transitions */
  * {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }

  /* Drag selection styling */
  .cursor-pointer {
    user-select: none;
  }

  td.bg-blue-200\/60 {
    background-color: rgba(147, 197, 253, 0.6) !important;
    border-color: rgb(147, 197, 253) !important;
    box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.5);
  }

  /* Disable text selection during drag */
  .selecting {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
  }
</style>


