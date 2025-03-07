<template>
    <div>
        <!-- PAGE TITLE -->
        <Head title="Situație prezență zilnică" />

        <!-- SIDEBAR -->
        <SidebarMenu class="print:hidden" />

        <main class="lg:pl-80 print:hidden">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24">
                        <!-- PAGE HEADER -->
                        <Header pageTitle="Raport situație prezență zilnică" customText="Vezi mai jos situația prezenței zilnice la program a personalului" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <Select
                                v-model="selectedBusinessUnitGroup"
                                :options="businessUnitGroups"
                                filter
                                optionLabel="name"
                                placeholder="Selectează structura"
                                class="w-full md:w-60"
                                @change="fetchDailyData"
                            >
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

                            <DatePicker
                                v-model="selectedDate"
                                dateFormat="dd.mm.yy"
                                :showIcon="true"
                                placeholder="Selectează ziua"
                                class="w-full md:w-44"
                                @date-select="fetchDailyData"
                            />

                            <button
                                class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-3 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!selectedBusinessUnitGroup || loading || (!presentEmployees.length && !absentEmployees.length)"
                                @click="printTable">
                                <PrinterIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Add No Filter Selected State -->
                    <div v-if="!selectedBusinessUnitGroup" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <img :src="'/images/select-structure.png'" class="w-64 mx-auto">
                            <div class="flex flex-col mt-3">
                                <h3 class="text-lg font-medium text-brand">Selectează o structură</h3>
                                <p>Pentru a vizualiza datele, te rugăm să selectezi o structură.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-else-if="loading" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <i class="pi pi-spin pi-spinner text-5xl text-brand"></i>
                            <div class="mt-2">Se încarcă datele...</div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="!presentEmployees.length && !absentEmployees.length" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <img :src="'/images/no-results.png'" class="w-64 mx-auto">
                            <div class="flex flex-col mt-3">
                                <h3 class="text-lg font-medium text-brand">Nu există date disponibile</h3>
                                <p> Nu au fost găsite înregistrări pentru perioada selectată.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div v-else class="pt-8 flow-root">
                        <div class="flex flex-col space-y-1.5">
                            <div class="font-semibold">
                                Situația prezenței la program a personalului în data de
                                {{ formatDisplayDate(selectedDate) }} - {{ selectedBusinessUnitGroup?.name || '...' }}
                            </div>

                            <ul class="list-disc list-inside space-y-1.5 pt-3">
                                <li>Efectiv control - <span class="font-semibold">{{ stats.total || 0 }}</span></li>
                                <li>Efectiv prezent - <span class="font-semibold">{{ stats.present || 0 }}</span></li>
                                <li>Răspândiri - <span class="font-semibold">{{ stats.absent || 0 }}</span></li>
                            </ul>
                        </div>

                        <div class="flex space-x-20 max-w-4xl mt-8">
                            <!-- Present Employees -->
                            <div>
                                <h3 class="font-semibold underline">Prezenți</h3>
                                <ul class="list-decimal list-inside space-y-1.5 mt-3">
                                    <li v-for="(employee, index) in presentEmployees" :key="index">
                                        {{ employee.military_rank.abbreviation }} {{ employee.name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Absent Employees -->
                            <div v-if="absentEmployees.length">
                                <h3 class="font-semibold underline">Răspândiri</h3>
                                <ul class="list-decimal list-inside space-y-1.5 mt-3">
                                    <li v-for="(employee, index) in absentEmployees" :key="index">
                                        {{ employee.military_rank.abbreviation }} {{ employee.name }} -
                                        <span class="font-semibold">{{ employee.status.replace('*', '') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Signature Preview Section -->
                        <div class="flex justify-start items-center mt-8 border-t border-gray-200 pt-5">
                            <!-- APPROVER -->
                            <div  class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                <div class="font-semibold text-sm uppercase">
                                    {{ signature.jobTitle || 'Adaugă semnătură' }}
                                </div>
                                <div v-if="selectedApprover" class="mt-0 text-sm italic">{{ selectedApproverRank?.name || 'grad militar' }}</div>
                                <div v-if="selectedApprover" class="font-semibold uppercase text-sm text-brand mt-3">
                                    {{ selectedApprover?.full_name || 'selectează persoana' }}
                                </div>
                                <div v-else class="font-medium text-sm text-brand mt-1">
                                    Selectează persoana
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Signature Editor Dialog -->
        <Dialog v-model:visible="signatureEditorVisible" header="Editare semnătură" :modal="true" :style="{ width: '32rem' }">
            <div class="p-fluid">
                <div class="mb-3">
                    <h3 class="font-semibold">Editează semnătura de pe document</h3>
                    <p class="text-sm text-gray-500">Modifică persoana care va semna documentul.</p>
                </div>

                <div class="border rounded p-4 bg-white">
                    <!-- APPROVER -->
                    <div class="field mb-4">
                        <Select
                            v-model="tempSignature.approverId"
                            :options="businessUnitEmployees"
                            optionLabel="full_name"
                            optionValue="id"
                            filter
                            placeholder="Selectează persoana"
                            class="w-full"
                        >
                            <template #value="slotProps">
                                <div v-if="slotProps.value" class="flex items-center">
                                    <div>{{ businessUnitEmployees.find(e => e.id === slotProps.value)?.full_name }}</div>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                        </Select>
                    </div>

                    <div>
                        <InputText
                            v-model="tempSignature.jobTitle"
                            placeholder="adaugă funcție (ex: Șef birou)"
                            class="w-full"
                            :class="{'p-invalid': showJobTitleError}"
                        />
                        <small v-if="showJobTitleError" class="p-error block leading-4 mt-2">Funcția este obligatorie!</small>
                        <small class="text-gray-500 leading-4 text-justify block mt-2">Modifică denumirea funcției dacă este necesar (ex: Șef serviciu, Contabil șef, Consilier juridic)</small>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button
                    label="Anulează"
                    size="small"
                    @click="closeSignatureEditor"
                    class="p-button-text"
                />
                <Button
                    label="Salvează"
                    size="small"
                    @click="saveSignatureEdit"
                    class="p-button-primary"
                />
            </template>
        </Dialog>

        <!-- Print version -->
        <div class="hidden print:block font-times">
            <h2 class="text-center text-lg uppercase max-w-3xl mx-auto mt-10">
                <span class="underline font-bold">{{ selectedBusinessUnitGroup?.name || '...' }}</span>
                <br>
                Situația prezenței la program a personalului în data de
                {{ formatDisplayDate(selectedDate) }}
            </h2>

            <div class="flex justify-center">
                <ul class="list-disc list-inside space-y-1.5 pt-14">
                    <li>Efectiv control - <span class="font-semibold">{{ stats.total || 0 }}</span></li>
                    <li>Efectiv prezent - <span class="font-semibold">{{ stats.present || 0 }}</span></li>
                    <li>Răspândiri - <span class="font-semibold">{{ stats.absent || 0 }}</span></li>
                </ul>
            </div>

            <div class="flex justify-center">
                <div class="flex space-x-16 mt-16">
                    <div>
                        <h3 class="font-semibold underline">Prezenți</h3>
                        <ul class="list-decimal list-inside whitespace-nowrap space-y-1.5 mt-3">
                            <li v-for="(employee, index) in presentEmployees" :key="index">
                                {{ employee.military_rank.abbreviation }} {{ employee.name }}
                            </li>
                        </ul>
                    </div>

                    <div v-if="absentEmployees.length">
                        <h3 class="font-semibold underline">Răspândiri</h3>
                        <ul class="list-decimal list-inside whitespace-nowrap space-y-1.5 mt-3">
                            <li v-for="(employee, index) in absentEmployees" :key="index">
                                {{ employee.military_rank.abbreviation }} {{ employee.name }} -
                                <span class="font-semibold">{{ employee.status.replace('*', '') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex justify-center px-10 mt-20">
                <!-- APPROVER SIGNATURE -->
                <div  class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                    <div class="font-semibold text-sm uppercase">
                        {{ signature.jobTitle || 'Funcție' }}
                    </div>
                    <div v-if="selectedApprover" class="mt-0 text-sm italic">{{ selectedApproverRank?.name || 'grad militar' }}</div>
                    <div v-if="selectedApprover" class="font-semibold uppercase text-sm mt-3">
                        {{ selectedApprover?.full_name || 'selectează persoana' }}
                    </div>
                    <div v-else class="font-medium mt-1">
                        Selectează persoana
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { PrinterIcon } from '@heroicons/vue/24/outline'
import moment from 'moment'
import axios from 'axios'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'
import InputText from 'primevue/inputtext'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'

defineProps({
    businessUnitGroups: {
        type: Array,
        required: true,
    },
})

const toast = useToast()

// State
const selectedBusinessUnitGroup = ref(null)
const selectedDate = ref(new Date())
const loading = ref(false)
const stats = ref({})
const presentEmployees = ref([])
const absentEmployees = ref([])

// Signature state
const signatureEditorVisible = ref(false)
const showJobTitleError = ref(false)

const businessUnitEmployees = ref([])

const tempSignature = ref({
    approverId: null,
    jobTitle: 'Adaugă semnătură',
})

const signature = ref({
    approverId: null,
    jobTitle: '',
})

// Computed properties for signature
const selectedApprover = computed(() =>
    signature.value.approverId ? businessUnitEmployees.value.find(e => e.id === signature.value.approverId) : null,
)

const selectedApproverRank = computed(() =>
    selectedApprover.value?.military_rank || null,
)

// Format date for display
const formatDisplayDate = (date) => {
    return moment(date).format('DD.MM.YYYY')
}

// Format date for API
const formatApiDate = (date) => {
    return moment(date).format('YYYY-MM-DD')
}

// Fetch business unit employees
const fetchBusinessUnitEmployees = async () => {
    if (!selectedBusinessUnitGroup.value) {
        businessUnitEmployees.value = []
        return
    }

    try {
        const response = await axios.post('/employees/by-business-unit-group', {
            businessUnitGroupId: selectedBusinessUnitGroup.value.id,
        })

        if (response.data.result === 'RESULT_OK') {
            businessUnitEmployees.value = response.data.employees
        } else {
            toast.error('Eroare la încărcarea angajaților: ' + response.data.error)
        }
    } catch (error) {
        console.error('Error loading employees:', error)
        toast.error('Eroare la încărcarea angajaților')
    }
}

// Open signature editor
const openSignatureEditor = () => {
    // Load employees if needed
    if (businessUnitEmployees.value.length === 0) {
        fetchBusinessUnitEmployees()
    }

    // Load saved signature from localStorage
    loadSignatureFromStorage()

    // Create a deep copy of the current signature for editing
    tempSignature.value = JSON.parse(JSON.stringify(signature.value))

    // Make sure the job title has a default value if empty
    if (!tempSignature.value.jobTitle) {
        tempSignature.value.jobTitle = 'Șef birou'
    }

    signatureEditorVisible.value = true
}

// Close signature editor
const closeSignatureEditor = () => {
    // Reset error state
    showJobTitleError.value = false
    // Just close the dialog without saving changes
    signatureEditorVisible.value = false
    // No need to commit tempSignature to signature
}

// Save signature edit
const saveSignatureEdit = () => {
    // Reset error state
    showJobTitleError.value = false

    // Validate job title when approver is selected
    if (tempSignature.value.approverId &&
        (!tempSignature.value.jobTitle || tempSignature.value.jobTitle.trim() === '')) {
        showJobTitleError.value = true
        return
    }

    // Copy the temporary values to the actual signature object
    signature.value = JSON.parse(JSON.stringify(tempSignature.value))

    // Save to localStorage
    saveSignatureToStorage()
    signatureEditorVisible.value = false

    toast.success('Semnătură salvată cu succes!', {
        timeout: 2000,
        position: 'bottom-right',
    })
}

// Save signature to localStorage
const saveSignatureToStorage = () => {
    try {
        const storageKey = `daily-report-signature-${selectedBusinessUnitGroup.value?.id}-${formatApiDate(selectedDate.value)}`
        localStorage.setItem(storageKey, JSON.stringify(signature.value))
    } catch (error) {
        console.error('Error saving signature to localStorage:', error)
    }
}

// Load signature from localStorage
const loadSignatureFromStorage = () => {
    try {
        if (!selectedBusinessUnitGroup.value || !selectedDate.value) return

        const storageKey = `daily-report-signature-${selectedBusinessUnitGroup.value?.id}-${formatApiDate(selectedDate.value)}`
        const savedSignature = localStorage.getItem(storageKey)

        if (savedSignature) {
            const parsed = JSON.parse(savedSignature)
            signature.value = {
                ...signature.value,
                ...parsed,
            }
        }
    } catch (error) {
        console.error('Error loading signature from localStorage:', error)
    }
}

// Fetch data when filters change
const fetchDailyData = async () => {
    // Don't fetch if no business unit group is selected
    if (!selectedDate.value || !selectedBusinessUnitGroup.value) {
        // Reset all data
        stats.value = { total: 0, present: 0, absent: 0 }
        presentEmployees.value = []
        absentEmployees.value = []
        return
    }

    loading.value = true
    try {
        const response = await axios.get('/rapoarte/prezenta-zilnica/data', {
            params: {
                date: formatApiDate(selectedDate.value),
                business_unit_group_id: selectedBusinessUnitGroup.value.id,
            },
        })

        // Update all the data
        stats.value = response.data.stats || { total: 0, present: 0, absent: 0 }
        presentEmployees.value = response.data.present || []
        absentEmployees.value = response.data.absent || []

        // Load signature after data is fetched
        loadSignatureFromStorage()
    } catch (error) {
        console.error('Error fetching daily data:', error)
        toast.error('A apărut o eroare la încărcarea datelor')

        // Reset data on error
        stats.value = { total: 0, present: 0, absent: 0 }
        presentEmployees.value = []
        absentEmployees.value = []
    } finally {
        loading.value = false
    }
}

// Watch for changes in selectedBusinessUnitGroup or selectedDate
watch([selectedBusinessUnitGroup, selectedDate], () => {
    fetchDailyData()
})

// Watch for business unit group changes to load employees
watch(() => selectedBusinessUnitGroup.value, () => {
    if (selectedBusinessUnitGroup.value) {
        fetchBusinessUnitEmployees()
    }
})

const printTable = () => {
    window.print()
}

// Initial data fetch
onMounted(() => {
    // Initialize with empty state
    stats.value = { total: 0, present: 0, absent: 0 }
})
</script>

<style scoped>
@media print {
    @page {
        size: portrait;
        margin: 2cm;
    }
}

.avoid-break {
    page-break-inside: avoid;
}
</style>
