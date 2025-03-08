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

                            <!-- Available Extra Hours Table -->
                            <div v-if="!groupBySchedule" class="space-y-3">
                                <div class="flex justify-between items-center uppercase h-10">
                                    <h3 class="font-semibold">Ore recuperare disponibile</h3>
                                    <button v-if="selectedHours.length > 0" @click="openReconciliationDrawer"
                                            class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-2">
                                        Recuperează {{ formatMinutesToHours(totalSelectedMinutes) }} ore
                                    </button>
                                </div>

                                <DataTable
                                    :value="availableExtraHours"
                                    v-model:selection="selectedExtraHoursTable"
                                    @row-select="onRowSelect"
                                    @row-unselect="onRowUnselect"
                                    @selection-change="onSelectionChange"
                                    :paginator="true"
                                    :rows="10"
                                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                    :rowsPerPageOptions="[5, 10, 20, 50]"
                                    v-model:expandedRows="expandedRows"
                                    dataKey="id"
                                    class="p-datatable-sm"
                                    :rowHover="true"
                                    stripedRows
                                    showGridlines
                                    tableStyle="min-width: 100%"
                                >
                                    <template #empty>
                                        <div class="text-center py-4">
                                            Nu există ore suplimentare disponibile pentru acest angajat.
                                        </div>
                                    </template>

                                    <Column selectionMode="multiple" headerStyle="width: 3rem" />
                                    <Column expander style="width: 3rem" />

                                    <Column field="date" header="Data" sortable style="min-width: 8rem">
                                        <template #body="{ data }">
                                            {{ formatDate(data.date) }}
                                        </template>
                                    </Column>

                                    <Column header="Interval orar" style="min-width: 10rem">
                                        <template #body="{ data }">
                                            {{ formatTime(data.start_time) }} - {{ formatTime(data.end_time) }}
                                        </template>
                                    </Column>

                                    <Column field="overtime_justification" header="Justificare" style="min-width: 15rem">
                                        <template #body="{ data }">
                                            {{ data.employeeSchedule?.overtime_justification || data.description || '-' }}
                                        </template>
                                    </Column>

                                    <Column field="total_minutes" header="Total ore" sortable style="min-width: 6rem; text-align: center">
                                        <template #body="{ data }">
                                            <div class="text-center">{{ formatMinutesToHours(data.total_minutes) }}</div>
                                        </template>
                                    </Column>

                                    <Column field="remaining_minutes" header="Ore disponibile" sortable style="min-width: 6rem; text-align: center">
                                        <template #body="{ data }">
                                            <div class="text-center">{{ formatMinutesToHours(data.remaining_minutes) }}</div>
                                        </template>
                                    </Column>

                                    <Column field="expiry_date" header="Expiră la" sortable style="min-width: 8rem">
                                        <template #body="{ data }">
                                            {{ formatDate(data.expiry_date) }}
                                        </template>
                                    </Column>

                                    <Column header="Status" style="min-width: 8rem; text-align: center">
                                        <template #body="{ data }">
                                            <Tag v-if="data.is_fully_reconciled"
                                                 severity="success"
                                                 value="Recuperat complet" />
                                            <Tag v-else-if="data.total_minutes > data.remaining_minutes"
                                                 severity="warning"
                                                 value="Parțial recuperat" />
                                            <Tag v-else
                                                 severity="info"
                                                 value="Disponibil" />
                                        </template>
                                    </Column>

                                    <Column header="Ore de folosit" style="min-width: 8rem; text-align: center">
                                        <template #body="{ data }">
                                            <input v-if="isSelectedInTable(data.id)"
                                                   type="number"
                                                   v-model="getSelectedHour(data.id).hours_to_use"
                                                   @input="updateMinutesFromHours(data.id)"
                                                   min="1"
                                                   :max="minutesToHoursForInput(data.remaining_minutes)"
                                                   class="w-20 px-2 border border-gray-300 rounded">
                                            <span v-else>{{ formatMinutesToHours(data.total_minutes - data.remaining_minutes) }}</span>
                                        </template>
                                    </Column>

                                    <template #expansion="slotProps">
                                        <div class="p-3 bg-gray-50 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold mb-2">Istoric recuperări</h4>
                                            <DataTable
                                                :value="slotProps.data.reconciliations || []"
                                                class="p-datatable-sm"
                                                :rowHover="true"
                                                stripedRows
                                                showGridlines
                                                tableStyle="min-width: 100%"
                                            >
                                                <template #empty>
                                                    <div class="text-center py-2">
                                                        Nu există recuperări pentru aceste ore.
                                                    </div>
                                                </template>

                                                <Column field="reconciliation_date" header="Data recuperării" style="min-width: 8rem">
                                                    <template #body="{ data }">
                                                        {{ formatDate(data.reconciliation_date) }}
                                                    </template>
                                                </Column>

                                                <Column field="minutes_reconciled" header="Ore recuperate" style="min-width: 6rem">
                                                    <template #body="{ data }">
                                                        {{ formatMinutesToHours(data.minutes_reconciled) }}
                                                    </template>
                                                </Column>

                                                <Column field="notes" header="Observații" style="min-width: 15rem" />

                                                <Column field="status" header="Status" style="min-width: 6rem">
                                                    <template #body="{ data }">
                                                        <Tag v-if="data.status === 'approved'" severity="success" value="Aprobat" />
                                                        <Tag v-else-if="data.status === 'rejected'" severity="danger" value="Respins" />
                                                        <Tag v-else severity="info" value="În așteptare" />
                                                    </template>
                                                </Column>
                                            </DataTable>
                                        </div>
                                    </template>
                                </DataTable>
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
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'

const props = defineProps({
    employees: Array,
    businessUnitGroups: Array,
})

// Component state
const toast = useToast()
const expandedRows = ref({})
const selectedExtraHoursTable = ref([])

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

// Metode pentru gestionarea selecției
const onRowSelect = (event) => {
    const extraHour = event.data
    if (!isSelected(extraHour.id)) {
        // Adaugă ID-ul la array-ul de selecție
        selectedExtraHoursIds.value.push(extraHour.id)

        // Adaugă ora selectată la array-ul de ore selectate
        selectedHours.value.push({
            id: extraHour.id,
            date: extraHour.date,
            start_time: extraHour.start_time,
            end_time: extraHour.end_time,
            remaining_minutes: extraHour.remaining_minutes,
            minutes_to_use: extraHour.remaining_minutes,
            hours_to_use: minutesToHoursForInput(extraHour.remaining_minutes),
        })
    }
}

const onRowUnselect = (event) => {
    const extraHourId = event.data.id
    // Elimină ID-ul din array-ul de selecție
    selectedExtraHoursIds.value = selectedExtraHoursIds.value.filter(id => id !== extraHourId)

    // Elimină ora din array-ul de ore selectate
    selectedHours.value = selectedHours.value.filter(hour => hour.id !== extraHourId)
}

const onSelectionChange = (event) => {
    // Actualizează selectedExtraHoursIds și selectedHours pentru a reflecta selecția curentă
    const newSelectionIds = event.value.map(item => item.id)

    // Găsește ID-urile care au fost eliminate
    const removedIds = selectedExtraHoursIds.value.filter(id => !newSelectionIds.includes(id))

    // Elimină orele care nu mai sunt selectate
    selectedHours.value = selectedHours.value.filter(hour => !removedIds.includes(hour.id))

    // Găsește ID-urile care au fost adăugate
    const addedIds = newSelectionIds.filter(id => !selectedExtraHoursIds.value.includes(id))

    // Adaugă noile ore selectate
    addedIds.forEach(id => {
        const extraHour = availableExtraHours.value.find(hour => hour.id === id)
        if (extraHour) {
            selectedHours.value.push({
                id: extraHour.id,
                date: extraHour.date,
                start_time: extraHour.start_time,
                end_time: extraHour.end_time,
                remaining_minutes: extraHour.remaining_minutes,
                minutes_to_use: extraHour.remaining_minutes,
                hours_to_use: minutesToHoursForInput(extraHour.remaining_minutes),
            })
        }
    })

    // Actualizează array-ul de ID-uri selectate
    selectedExtraHoursIds.value = newSelectionIds
}

// Verifică dacă un ID de oră suplimentară este selectat în tabel
const isSelectedInTable = (id) => {
    return selectedExtraHoursTable.value.some(item => item.id === id)
}

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

const loadExtraHours = async () => {
    if (!selectedEmployee.value) {
        availableExtraHours.value = []
        groupedExtraHours.value = []
        reconciledExtraHours.value = []
        return
    }

    try {
        console.log('Loading extra hours for employee:', selectedEmployee.value.id)

        const groupParam = groupBySchedule.value ? 1 : 0

        const response = await axios.get('/api/extra-hours/available', {
            params: {
                employee_id: selectedEmployee.value.id,
                group_by_schedule: groupParam,
                include_reconciled: 1,
                _t: new Date().getTime(),
            },
        })

        console.log('API response after reload:', response.data)

        // Salvează selecțiile curente pentru a le restabili după reload
        const currentSelectedIds = [...selectedExtraHoursIds.value]

        // Actualizează datele
        availableExtraHours.value = response.data.extraHours
        summary.value = response.data.summary

        if (response.data.reconciledHours) {
            reconciledExtraHours.value = response.data.reconciledHours
        } else {
            reconciledExtraHours.value = []
        }

        if (groupBySchedule.value && response.data.groupedExtraHours) {
            groupedExtraHours.value = response.data.groupedExtraHours

            if (response.data.groupedReconciledHours) {
                groupedReconciledHours.value = response.data.groupedReconciledHours
            } else {
                groupedReconciledHours.value = []
            }
        } else {
            groupedExtraHours.value = []
            groupedReconciledHours.value = []
        }

        // Resetează selecțiile
        selectedExtraHoursIds.value = []
        selectedHours.value = []
        selectedExtraHoursTable.value = []
        selectAll.value = false

        // Restabilește selecțiile anterioare
        if (currentSelectedIds.length > 0) {
            // Găsește orele care există încă în lista actualizată
            const validIds = currentSelectedIds.filter(id =>
                availableExtraHours.value.some(hour => hour.id === id),
            )

            if (validIds.length > 0) {
                // Setează ID-urile valide
                selectedExtraHoursIds.value = validIds

                // Actualizează orele selectate
                validIds.forEach(id => {
                    const extraHour = availableExtraHours.value.find(hour => hour.id === id)
                    if (extraHour) {
                        selectedHours.value.push({
                            id: extraHour.id,
                            date: extraHour.date,
                            start_time: extraHour.start_time,
                            end_time: extraHour.end_time,
                            remaining_minutes: extraHour.remaining_minutes,
                            minutes_to_use: extraHour.remaining_minutes,
                            hours_to_use: minutesToHoursForInput(extraHour.remaining_minutes),
                        })
                    }
                })

                // Setează selecția în tabel
                selectedExtraHoursTable.value = availableExtraHours.value
                    .filter(hour => validIds.includes(hour.id))
            }
        }
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
