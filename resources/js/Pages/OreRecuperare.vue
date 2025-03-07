<template>
    <div>
        <!-- PAGE TITLE -->

        <Head title="Ore recuperare" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col">

                    <div
                        class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24 border-b border-line">

                        <!-- PAGE HEADER -->
                        <Header pageTitle="Ore recuperare" totalText="Total ore disponibile"
                                :totalCount="totalAvailableHours" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">

                            <!-- Select Business Unit Group -->
                            <Select v-model="selectedBusinessUnitGroup" :options="businessUnitGroups" filter
                                    optionLabel="name" placeholder="Selectează structura"
                                    @change="loadEmployeesByBusinessUnitGroup" class="w-full md:w-60">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex align-items-center">
                                        <div>{{ slotProps.value.name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex align-items-center">
                                        <div>{{ slotProps.option.name }}</div>
                                    </div>
                                </template>
                            </Select>

                            <!-- Select Employee -->
                            <Select v-model="selectedEmployee" :options="filteredEmployees" filter
                                    optionLabel="full_name" placeholder="Selectează persoană" @change="loadExtraHours"
                                    class="w-full md:w-60" :disabled="!selectedBusinessUnitGroup">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex align-items-center">
                                        <div>{{ slotProps.value.full_name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex align-items-center">
                                        <div>{{ slotProps.option.full_name }}</div>
                                    </div>
                                </template>
                            </Select>
                        </div>
                    </div>

                    <!-- Add No Filter Selected State -->
                    <div v-if="!selectedEmployee" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <img :src="'/images/ore-recuperare.png'" class="w-64 mx-auto">
                            <div class="flex flex-col mt-3">
                                <h3 class="text-lg font-medium text-brand">Selectează structura și persoana</h3>
                                <p>Pentru a vizualiza datele, te rugăm să selectezi o structură și o persoană.</p>
                            </div>
                        </div>
                    </div>

                    <!-- MAIN CONTENT -->
                    <div v-else class="pt-8">
                        <div v-if="selectedEmployee" class="space-y-8">
                            <!-- Summary Card -->
                            <div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="bg-blue-100 rounded-md px-5 py-7">
                                        <div class="text-sm font-medium text-gray-600">Ore câștigate</div>
                                        <div class="text-2xl font-semibold">{{ formatMinutesToHours(summary.earned_minutes) || '0' }}</div>
                                    </div>
                                    <div class="bg-green-100 rounded-md px-5 py-7">
                                        <div class="text-sm font-medium text-gray-600">Ore disponibile</div>
                                        <div class="text-2xl font-semibold">{{ formatMinutesToHours(summary.available_minutes) || '0' }}</div>
                                    </div>
                                    <div class="bg-orange-100 rounded-md px-5 py-7">
                                        <div class="text-sm font-medium text-gray-600">Ore utilizate</div>
                                        <div class="text-2xl font-semibold">{{ formatMinutesToHours(summary.reconciled_minutes) || '0' }}</div>
                                    </div>
                                    <div class="bg-red-100 rounded-md px-5 py-7">
                                        <div class="text-sm font-medium text-gray-600">Ore expirate</div>
                                        <div class="text-2xl font-semibold">{{ formatMinutesToHours(summary.expired_minutes) || '0' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group By Toggle -->
                            <!-- <div class="flex items-center space-x-2">
                                <InputSwitch v-model="groupBySchedule" @change="onGroupByChange" />
                                <label class="text-sm text-gray-700">Grupare ore după dată</label>
                            </div> -->

                            <!-- Available Extra Hours Table -->
                            <div v-if="!groupBySchedule" class="space-y-3">
                                <div class="flex justify-between items-center uppercase h-10">
                                    <h3 class="font-semibold">Ore recuperare disponibile</h3>
                                    <button v-if="selectedHours.length > 0" @click="openReconciliationDrawer"
                                            class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-2">
                                        Recuperează {{ formatMinutesToHours(totalSelectedMinutes) }} ore
                                    </button>
                                </div>

                                <div class="rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        <input type="checkbox" v-model="selectAll"
                                                               @change="toggleAllExtraHours"
                                                               class="h-4 w-4 rounded border-gray-300">
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Data
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Interval orar
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Descriere
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-center text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Total ore
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-center text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Ore disponibile
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                                        Expiră la
                                                    </th>
                                                    <th scope="col"
                                                        class="p-5 text-center text-xs font-medium text-gray-800 uppercase tracking-wider w-40">
                                                        {{ selectedExtraHoursIds.length > 0 ? 'Ore rămase' : 'Ore utilizate' }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="extraHour in availableExtraHours" :key="extraHour.id"
                                                    :class="{ 'bg-brand/5': isSelected(extraHour.id) }">
                                                    <td class="p-5 whitespace-nowrap">
                                                        <input type="checkbox" :value="extraHour.id"
                                                               v-model="selectedExtraHoursIds"
                                                               @change="updateSelectedHours(extraHour)"
                                                               class="h-4 w-4 rounded border-gray-300">
                                                    </td>
                                                    <td class="p-5 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatDate(extraHour.date) }}
                                                    </td>
                                                    <td class="p-5 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatTime(extraHour.start_time) }} - {{
                                                            formatTime(extraHour.end_time) }}
                                                    </td>
                                                    <td class="p-5 whitespace-nowrap text-sm text-gray-500">
                                                        {{ extraHour.description || '-' }}
                                                    </td>
                                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ formatMinutesToHours(extraHour.total_minutes) }}
                                                    </td>
                                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ formatMinutesToHours(extraHour.remaining_minutes) }}
                                                    </td>
                                                    <td class="p-5 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatDate(extraHour.expiry_date) }}
                                                    </td>
                                                    <td class="p-5 whitespace-nowrap text-sm text-center">
                                                        <input v-if="isSelected(extraHour.id)" type="number"
                                                               v-model="getSelectedHour(extraHour.id).hours_to_use"
                                                               @input="updateMinutesFromHours(extraHour.id)" min="1"
                                                               :max="minutesToHoursForInput(extraHour.remaining_minutes)"
                                                               class="w-20 px-2  border border-gray-300 rounded">
                                                        <span v-else>{{ formatMinutesToHours(extraHour.total_minutes - extraHour.remaining_minutes) }}</span>
                                                    </td>
                                                </tr>
                                                <tr v-if="availableExtraHours.length === 0">
                                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        Nu există ore suplimentare disponibile pentru acest angajat.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Grouped Extra Hours by Schedule -->
                            <div v-else class="space-y-6">
                                <div v-for="(group, index) in groupedExtraHours" :key="index"
                                     class="bg-white rounded-lg shadow overflow-hidden">
                                    <div
                                        class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">Program: {{
                                                formatDate(group.schedule_date_start) }} - {{
                                                formatTime(group.schedule_date_start) }} până
                                                la {{ formatDate(group.schedule_date_finish) }} - {{
                                                    formatTime(group.schedule_date_finish)
                                                }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">Total ore suplimentare: {{
                                                formatMinutesToHours(group.total_minutes) }} | Disponibile: {{
                                                formatMinutesToHours(group.remaining_minutes) }}</p>
                                        </div>
                                        <button v-if="hasSelectedHoursInGroup(group.schedule_id)"
                                                @click="openReconciliationDrawer"
                                                class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-2">
                                            Reconciliază ore selectate
                                        </button>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        <input type="checkbox"
                                                               :checked="isGroupAllSelected(group.schedule_id)"
                                                               @change="toggleGroupExtraHours(group.schedule_id, $event.target.checked)"
                                                               class="h-4 w-4 rounded border-gray-300">
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Data
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Interval orar
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Descriere
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Total
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Disponibil
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Expiră la
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Utilizat
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="extraHour in group.extra_hours" :key="extraHour.id"
                                                    :class="{ 'bg-brand/5': isSelected(extraHour.id) }">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="checkbox" :value="extraHour.id"
                                                               v-model="selectedExtraHoursIds"
                                                               @change="updateSelectedHours(extraHour)"
                                                               class="h-4 w-4 rounded border-gray-300">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatDate(extraHour.date) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatTime(extraHour.start_time) }} - {{
                                                            formatTime(extraHour.end_time) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ extraHour.description || '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatMinutesToHours(extraHour.total_minutes) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatMinutesToHours(extraHour.remaining_minutes) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatDate(extraHour.expiry_date) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <input v-if="isSelected(extraHour.id)"
                                                               type="number"
                                                               v-model="getSelectedHour(extraHour.id).hours_to_use"
                                                               @input="updateMinutesFromHours(extraHour.id)"
                                                               min="1"
                                                               :max="minutesToHoursForInput(extraHour.remaining_minutes)"
                                                               class="w-20 px-2 py-1 border border-gray-300 rounded">
                                                        <span v-else>-</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-if="groupedExtraHours.length === 0"
                                     class="bg-white rounded-lg shadow p-5 text-center text-sm text-gray-500">
                                    Nu există ore suplimentare disponibile pentru acest angajat.
                                </div>
                            </div>

                            <!-- HISTORY TABLE -->
                            <div v-if="reconciledExtraHours.length > 0" class="space-y-3">
                                <div class="flex justify-between items-center uppercase h-10">
                                    <h3 class="uppercase font-semibold">Istoric ore recuperate</h3>
                                </div>
                                <div class="rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Data
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Interval orar
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Descriere
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Total
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Reconciliat
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Data reconcilierii
                                                    </th>
                                                    <th scope="col" class="p-5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Status
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="extraHour in reconciledExtraHours" :key="extraHour.id">
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatDate(extraHour.date) }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatTime(extraHour.start_time) }} - {{ formatTime(extraHour.end_time) }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ extraHour.description || '-' }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatMinutesToHours(extraHour.total_minutes) }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ formatMinutesToHours(extraHour.reconciled_minutes) }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ extraHour.last_reconciled_date ? formatDate(extraHour.last_reconciled_date) : '-' }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                                        <span v-if="extraHour.status === 'expired'"
                                                              class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            Expirat
                                                        </span>
                                                        <span v-else-if="extraHour.is_fully_reconciled"
                                                              class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            Reconciliat
                                                        </span>
                                                        <span v-else-if="extraHour.reconciled_minutes > 0"
                                                              class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            Parțial reconciliat
                                                        </span>
                                                        <span v-else
                                                              class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            -
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr v-if="reconciledExtraHours.length === 0">
                                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        Nu există ore reconciliate în istoric.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="relative">
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <div
                                    class="flex items-center space-x-2 border-2 border-brand text-brand rounded-md p-5">
                                    <ExclamationCircleIcon class="h-7 w-7 flex-shrink-0 text-brand" />
                                    <p>Te rugăm să selectezi structura și persoana!</p>
                                </div>
                            </div>
                            <div class="blur-sm">
                                <div class="bg-white rounded-lg shadow p-5 h-64"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Reconciliation Drawer -->
                    <Drawer v-model:visible="reconciliationDrawerVisible" header="Reconciliere" position="right"
                            style="width:100%; max-width: 32rem">
                        <template #header>
                            <div class="flex align-items-center gap-2 mr-auto">
                                <h2 class="font-semibold text-lg text-brand uppercase">Recuperare ore suplimentare
                                </h2>
                            </div>
                        </template>

                        <div class="border-t border-line py-6">
                            <p class="text-base">Completează detaliile pentru a recupera orele selectate.</p>

                            <form @submit.prevent="submitReconciliation"
                                  class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 mt-5">
                                <div class="space-y-2">
                                    <InputLabel value="Data recuperării" />
                                    <DatePicker v-model="reconciliationForm.reconciliation_date" dateFormat="dd.mm.yy"
                                                :stepMinute="1" placeholder="Alege data" class="w-full" />
                                    <div v-if="errors.reconciliation_date" class="text-red-500 !mt-1">
                                        {{ errors.reconciliation_date }}
                                    </div>
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <InputLabel value="Observații" />
                                    <Textarea v-model="reconciliationForm.notes" rows="4"
                                              placeholder="Adaugă detalii sau explicații despre reconciliere..."
                                              class="w-full" />
                                </div>

                                <div class="sm:col-span-2">
                                    <div class="bg-gray-50 rounded-md p-5">
                                        <h4 class="font-medium text-brand mb-2">Ore selectate pentru recuperare</h4>
                                        <div class="space-y-2.5 mt-5">
                                            <div v-for="item in selectedHours" :key="item.id"
                                                 class="flex justify-between text-sm">
                                                <div>
                                                    <span class="font-medium">{{ formatDate(item.date) }}</span> -
                                                    {{ formatTime(item.start_time) }} până la {{
                                                        formatTime(item.end_time) }}
                                                </div>
                                                <div class="font-semibold">{{formatMinutesToHours(item.minutes_to_use)}} ore</div>

                                            </div>
                                        </div>
                                        <div class="mt-5 pt-5 border-t border-gray-200 flex justify-between">
                                            <div class="font-medium">Total:</div>
                                            <div class="font-bold">{{formatMinutesToHours(totalSelectedMinutes)}} ore</div>

                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 bg-brand/10 rounded-md p-4 my-2">
                                    <ClockIcon class="h-7 w-7 text-indigo-600 hidden sm:block" />
                                    <div class="flex flex-col">
                                        <div class="text-sm">Total ore recuperate:</div>
                                        <div class="text-lg font-semibold">{{ formatMinutesToHours(totalSelectedMinutes)}}</div>
                                    </div>
                                </div>

                                <div></div>

                                <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                    <PrimaryButton type="submit" :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting || totalSelectedMinutes === 0">
                                        Confirmă recuperarea
                                    </PrimaryButton>

                                    <SecondaryButton @click="reconciliationDrawerVisible = false">
                                        Anulează
                                    </SecondaryButton>
                                </div>
                            </form>
                        </div>
                    </Drawer>
                </div>
            </div>
        </main>
        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { ClockIcon } from '@heroicons/vue/24/solid'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import Select from 'primevue/select'
import Drawer from 'primevue/drawer'
import InputLabel from '@/Components/elements/InputLabel.vue'
import DatePicker from 'primevue/datepicker'
import Textarea from 'primevue/textarea'
import ConfirmDialog from 'primevue/confirmdialog'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import SecondaryButton from '@/Components/elements/SecondaryButton.vue'
import InputSwitch from 'primevue/inputswitch'

const props = defineProps({
    employees: Array,
    businessUnitGroups: Array,
})

// Component state
const toast = useToast()

const selectedBusinessUnitGroup = ref(null)
const filteredEmployees = ref([])
const selectedEmployee = ref(null)
const availableExtraHours = ref([])
const selectedExtraHoursIds = ref([])
const selectedHours = ref([])
const selectAll = ref(false)
const summary = ref({
    earned_minutes: 0,
    reconciled_minutes: 0,
    expired_minutes: 0,
    available_minutes: 0,
    earned_formatted: '00:00',
    reconciled_formatted: '00:00',
    expired_formatted: '00:00',
    available_formatted: '00:00',
})
const groupBySchedule = ref(false)
const groupedExtraHours = ref([])

const reconciledExtraHours = ref([])
const groupedReconciledHours = ref([])

// Drawer state
const reconciliationDrawerVisible = ref(false)
const reconciliationForm = ref({
    reconciliation_date: new Date(),
    notes: '',
})
const errors = ref({})
const isSubmitting = ref(false)

// Computed properties
const totalSelectedMinutes = computed(() => {
    return selectedHours.value.reduce((sum, hour) => sum + (hour.minutes_to_use || 0), 0)
})

const totalAvailableHours = computed(() => {
    if (!summary.value.available_minutes) return '0'
    return formatMinutesToHours(summary.value.available_minutes)
})

// Methods
const loadEmployeesByBusinessUnitGroup = async () => {
    if (!selectedBusinessUnitGroup.value) {
        filteredEmployees.value = []
        selectedEmployee.value = null
        return
    }

    try {
        selectedEmployee.value = null

        const response = await axios.post('/employees/by-business-unit-group', {
            businessUnitGroupId: selectedBusinessUnitGroup.value.id,
        })

        if (response.data.result === 'RESULT_OK') {
            filteredEmployees.value = response.data.employees
        } else {
            toast.error('Eroare la încărcarea angajaților: ' + response.data.error)
        }
    } catch (error) {
        console.error('Error loading employees:', error)
        toast.error('Eroare la încărcarea angajaților')
    }
}

const onGroupByChange = () => {
    if (selectedEmployee.value) {
        loadExtraHours()
    }
}

const loadExtraHours = async () => {
    if (!selectedEmployee.value) {
        availableExtraHours.value = []
        groupedExtraHours.value = []
        reconciledExtraHours.value = [] // Initialize the reconciled hours array
        return
    }

    try {
        console.log('Loading extra hours for employee:', selectedEmployee.value.id)

        // Convert the boolean value to a numeric value (1 for true, 0 for false)
        const groupParam = groupBySchedule.value ? 1 : 0

        const response = await axios.get('/api/extra-hours/available', {
            params: {
                employee_id: selectedEmployee.value.id,
                group_by_schedule: groupParam,
                include_reconciled: 1, // Add this parameter to get reconciled hours
                // Add a timestamp to prevent caching
                _t: new Date().getTime(),
            },
        })

        console.log('API response after reload:', response.data)

        // Update the data
        availableExtraHours.value = response.data.extraHours
        summary.value = response.data.summary

        // Store reconciled hours if they exist
        if (response.data.reconciledHours) {
            reconciledExtraHours.value = response.data.reconciledHours
        } else {
            reconciledExtraHours.value = []
        }

        if (groupBySchedule.value && response.data.groupedExtraHours) {
            groupedExtraHours.value = response.data.groupedExtraHours
            console.log('Updated grouped extra hours')

            // Store grouped reconciled hours if they exist
            if (response.data.groupedReconciledHours) {
                groupedReconciledHours.value = response.data.groupedReconciledHours
            } else {
                groupedReconciledHours.value = []
            }
        } else {
            groupedExtraHours.value = []
            groupedReconciledHours.value = []
        }

        // Reset selections
        selectedExtraHoursIds.value = []
        selectedHours.value = []
        selectAll.value = false
    } catch (error) {
        console.error('Error loading extra hours:', error)
        toast.error('Eroare la încărcarea orelor suplimentare')
    }
}

const toggleAllExtraHours = () => {
    if (selectAll.value) {
        // Select all
        selectedExtraHoursIds.value = availableExtraHours.value.map(hour => hour.id)
        selectedHours.value = availableExtraHours.value.map(hour => ({
            id: hour.id,
            date: hour.date,
            start_time: hour.start_time,
            end_time: hour.end_time,
            remaining_minutes: hour.remaining_minutes,
            minutes_to_use: hour.remaining_minutes,
            // Add this line to convert minutes to hours for input display
            hours_to_use: minutesToHoursForInput(hour.remaining_minutes),
        }))
    } else {
        // Deselect all
        selectedExtraHoursIds.value = []
        selectedHours.value = []
    }
}

const toggleGroupExtraHours = (scheduleId, isChecked) => {
    const group = groupedExtraHours.value.find(g => g.schedule_id === scheduleId)
    if (!group) return

    if (isChecked) {
        // Select all hours in this group
        group.extra_hours.forEach(hour => {
            if (!selectedExtraHoursIds.value.includes(hour.id)) {
                selectedExtraHoursIds.value.push(hour.id)
                selectedHours.value.push({
                    id: hour.id,
                    date: hour.date,
                    start_time: hour.start_time,
                    end_time: hour.end_time,
                    remaining_minutes: hour.remaining_minutes,
                    minutes_to_use: hour.remaining_minutes,
                    hours_to_use: minutesToHoursForInput(hour.remaining_minutes),
                })
            }
        })
    } else {
        // Deselect all hours in this group
        const hourIds = group.extra_hours.map(hour => hour.id)
        selectedExtraHoursIds.value = selectedExtraHoursIds.value.filter(id => !hourIds.includes(id))
        selectedHours.value = selectedHours.value.filter(hour => !hourIds.includes(hour.id))
    }
}

const isGroupAllSelected = (scheduleId) => {
    const group = groupedExtraHours.value.find(g => g.schedule_id === scheduleId)
    if (!group || group.extra_hours.length === 0) return false

    return group.extra_hours.every(hour => selectedExtraHoursIds.value.includes(hour.id))
}

const hasSelectedHoursInGroup = (scheduleId) => {
    const group = groupedExtraHours.value.find(g => g.schedule_id === scheduleId)
    if (!group) return false

    return group.extra_hours.some(hour => selectedExtraHoursIds.value.includes(hour.id))
}

const isSelected = (id) => {
    return selectedExtraHoursIds.value.includes(id)
}

const getSelectedHour = (id) => {
    return selectedHours.value.find(hour => hour.id === id)
}

const updateMinutesFromHours = (id) => {
    const selectedHour = getSelectedHour(id)
    if (!selectedHour) return

    // Convert hours to minutes
    selectedHour.minutes_to_use = hoursToMinutes(selectedHour.hours_to_use)

    // Validate maximum
    const extraHour = availableExtraHours.value.find(hour => hour.id === id) ||
    groupedExtraHours.value.flatMap(group => group.extra_hours).find(hour => hour.id === id)

    if (extraHour && selectedHour.minutes_to_use > extraHour.remaining_minutes) {
        selectedHour.minutes_to_use = extraHour.remaining_minutes
        selectedHour.hours_to_use = minutesToHoursForInput(extraHour.remaining_minutes)
    }
}

const hoursToMinutes = (hours) => {
    if (!hours) return 0
    return hours * 60
}

const minutesToHoursForInput = (minutes) => {
    if (!minutes) return 0
    return Math.floor(minutes / 60)
}

const updateSelectedHours = (extraHour) => {
    if (isSelected(extraHour.id)) {
        // Add to selected hours - convert to hours for display
        selectedHours.value.push({
            id: extraHour.id,
            date: extraHour.date,
            start_time: extraHour.start_time,
            end_time: extraHour.end_time,
            remaining_minutes: extraHour.remaining_minutes,
            // Default to full hours available
            minutes_to_use: extraHour.remaining_minutes,
            hours_to_use: minutesToHoursForInput(extraHour.remaining_minutes),
        })
    } else {
        // Remove from selected hours
        selectedHours.value = selectedHours.value.filter(hour => hour.id !== extraHour.id)
    }
}

const openReconciliationDrawer = () => {
    if (selectedHours.value.length === 0) {
        toast.warning('Selectați cel puțin o perioadă pentru reconciliere')
        return
    }

    // Make sure notes is initialized as an empty string
    reconciliationForm.value = {
        reconciliation_date: new Date(),
        notes: '',
    }

    errors.value = {}
    reconciliationDrawerVisible.value = true
}

const submitReconciliation = async () => {
    if (totalSelectedMinutes.value === 0) {
        toast.warning('Nu ați selectat nicio oră pentru reconciliere')
        return
    }

    errors.value = {}

    if (!reconciliationForm.value.reconciliation_date) {
        errors.value.reconciliation_date = 'Data reconcilierii este obligatorie'
        return
    }

    isSubmitting.value = true

    try {
        // Make sure we have a form object with notes initialized
        if (!reconciliationForm.value.notes) {
            reconciliationForm.value.notes = ''
        }

        const payload = {
            employee_id: selectedEmployee.value.id,
            extra_hour_id: selectedHours.value.map(hour => hour.id),
            minutes_reconciled: selectedHours.value.map(hour => hour.minutes_to_use),
            reconciliation_date: reconciliationForm.value.reconciliation_date,
            notes: reconciliationForm.value.notes, // This will now always be a string (empty if not set)
        }

        console.log('API payload for reconciliation:', payload)
        const response = await axios.post('/api/reconciliations', payload)
        console.log('API response from reconciliation:', response.data)

        toast.success('Reconciliere realizată cu succes!')
        reconciliationDrawerVisible.value = false

        // Reset selected hours
        selectedExtraHoursIds.value = []
        selectedHours.value = []
        selectAll.value = false

        // Force reload data with a delay to ensure backend processing is complete
        setTimeout(() => {
            loadExtraHours()
        }, 500)
    } catch (error) {
        console.error('Error submitting reconciliation:', error)

        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors
        } else {
            toast.error('Eroare la salvarea reconcilierii: ' + (error.response?.data?.message || error.message))
        }
    } finally {
        isSubmitting.value = false
    }
}

// Utility functions
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('ro-RO')
}

const formatTime = (timeString) => {
    if (!timeString) return ''
    const date = new Date(timeString)
    return date.toLocaleTimeString('ro-RO', { hour: '2-digit', minute: '2-digit' })
}

const formatMinutesToHours = (minutes) => {
    if (minutes === undefined || minutes === null) return '0'

    // Convert to number if it's a string
    const mins = typeof minutes === 'string' ? parseInt(minutes, 10) : minutes
    const hours = Math.floor(mins / 60)
    const remainingMinutes = mins % 60

    // Only show decimal places if there are remaining minutes
    if (remainingMinutes === 0) {
        return hours.toString()
    } else {
        return `${hours}.${remainingMinutes.toString().padStart(2, '0')}`
    }
}

// Initialize
onMounted(() => {
    filteredEmployees.value = props.employees || []
})

// Watch for groupBySchedule changes to reload data
watch(groupBySchedule, (newValue) => {
    if (selectedEmployee.value) {
        loadExtraHours()
    }
})

</script>

<style>
input::placeholder {
    text-transform: none;
    font-size: 0.875rem;
}

.p-datepicker.p-component {
    z-index: 1100 !important;
}
</style>
