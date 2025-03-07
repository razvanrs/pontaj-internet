<template>
    <div>

        <!-- PAGE TITLE -->
        <Head title="Planificare activități personal" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24 border-b border-line">

                        <!-- PAGE HEADER -->
                        <Header pageTitle="Planificare activități personal" totalText="Total personal" :totalCount="employees.length" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">

                            <!-- Add the new select box for business unit groups -->
                            <Select v-model="selectedBusinessUnitGroup" :options="businessUnitGroups" filter optionLabel="name" placeholder="Selectează structura" @change="loadEmployeesByBusinessUnitGroup" class="w-full md:w-60">
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

                            <Select
                                v-model="filterEmployee"
                                :options="filteredEmployees"
                                filter
                                optionLabel="full_name"
                                placeholder="Selectează persoană"
                                @change="changeEmployee"
                                class="w-full md:w-60"
                                :disabled="!selectedBusinessUnitGroup"
                            >
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

                            <button class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-3" @click="showPlanifica">
                                Planifică
                            </button>
                        </div>
                    </div>

                    <!-- MAIN CALENDAR -->
                    <div class="pt-8">
                        <div v-if="filterEmployee" class="space-y-5">
                            <CalendarTimeline
                                :events="calendarEvents"
                                :slotDurationProps="'00:30:00'"
                                :slotMinTimeProps="'00:00:00'"
                                :slotMaxTimeProps="'24:00:00'"
                                ref="calendarInteraction"
                                @show-event="showEvent"
                                @dates-set-event="datesSetEvent" />

                            <div class="items-center hidden">
                                <div class="bg-blue-500" />
                                <div class="bg-violet-500" />
                                <div class="bg-orange-500" />
                                <div class="bg-red-500" />
                                <div class="bg-emerald-500" />
                            </div>

                            <div class="flex space-x-5">
                                <div v-for="scheduleStatus in scheduleStatuses" :key="scheduleStatus.id">
                                    <div class="flex space-x-1.5 items-center">
                                        <div class="h-3.5 w-3.5 rounded-full" :style="{ backgroundColor: scheduleStatus.color }"></div>
                                        <p class="text-sm"> {{ scheduleStatus.code }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="relative">
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <div class="flex items-center space-x-2 border-2 border-brand text-brand rounded-md p-5">
                                    <ExclamationCircleIcon class="h-7 w-7 flex-shrink-0 text-brand" />
                                    <p>Te rugăm să selectezi structura și persoana!</p>
                                </div>
                            </div>
                            <div class="blur-sm">
                                <CalendarTimeline :events="calendarEvents" :slotDurationProps="'00:30:00'" :slotMinTimeProps="'00:00:00'" :slotMaxTimeProps="'24:00:00'" ref="calendarInteraction" @show-event="showEvent" />
                            </div>
                        </div>
                    </div>

                    <div class="card flex justify-content-center">
                        <Drawer v-model:visible="visible" header="Drawer" position="right" style="width:100%; max-width: 32rem">
                            <template #header>
                                <div class="flex align-items-center gap-2 mr-auto">
                                    <h2 class="font-semibold text-lg text-brand uppercase">Adaugă activitate personal</h2>
                                </div>
                            </template>

                            <div class="border-t border-line py-6">
                                <p class="text-base">Adaugă mai jos detaliile necesare.</p>

                                <form @submit.prevent="submit" class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 mt-5">

                                    <!-- Conditionally render either single date or range DatePicker -->
                                    <template v-if="!choosePeriod">
                                        <div class="space-y-2">
                                            <InputLabel value="Data începerii activității" />
                                            <DatePicker
                                                v-model="form.dateStart"
                                                dateFormat="dd.mm.yy"
                                                :minDate="new Date(DateTime.now().startOf('year').toFormat('yyyy-MM-dd HH:mm:ss'))"
                                                :stepMinute="1"
                                                :maxDate="new Date(new Date().getFullYear(), 11, 31)"
                                                showTime
                                                hourFormat="24"
                                                placeholder="Alege"
                                                class="w-full md:w-14rem"
                                                @update:modelValue="updateDateEnd"
                                            />
                                            <div v-if="$page.props.errors.dateStart" class="text-red-500 !mt-1">
                                                {{ $page.props.errors.dateStart }}
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <InputLabel value="Data finalizării activității" />
                                            <DatePicker
                                                v-model="form.dateEnd"
                                                dateFormat="dd.mm.yy"
                                                :minDate="form.dateStart"
                                                :maxDate="new Date(new Date().getFullYear(), 11, 31)"
                                                :stepMinute="1"
                                                showTime
                                                hourFormat="24"
                                                placeholder="Alege"
                                                class="w-full md:w-14rem"
                                            />
                                            <div v-if="$page.props.errors.dateEnd" class="text-red-500 !mt-1">
                                                {{ $page.props.errors.dateEnd }}
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Range DatePicker when checkbox is checked -->
                                    <div v-else class="sm:col-span-2 space-y-2">
                                        <InputLabel value="Perioadă selectată" />
                                        <DatePicker
                                            v-model="periodDates"
                                            selectionMode="range"
                                            placeholder="Selectează intervalul"
                                            dateFormat="dd.mm.yy"
                                            class="w-full placeholder:normal-case"
                                        />
                                    </div>

                                    <div class="sm:col-span-2 flex items-center" v-if="!isEditMode">
                                        <Switch
                                            v-model="choosePeriod"
                                            :class="choosePeriod ? 'bg-blue-600' : 'bg-gray-300'"
                                            class="relative inline-flex h-5 w-10 items-center rounded-full transition duration-200"
                                        >
                                            <span
                                                class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition duration-200"
                                                :class="choosePeriod ? 'translate-x-5' : 'translate-x-1'"
                                            />
                                        </Switch>
                                        <label for="choose-period" class="text-sm ml-2">
                                            Activează pentru a adăuga o perioadă
                                        </label>
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <InputLabel value="Situație prezență" />
                                        <Select
                                            v-model="form.scheduleStatus"
                                            :options="scheduleStatuses"
                                            optionLabel="name"
                                            placeholder="Selectează o opțiune"
                                            class="w-full md:w-14rem"
                                        >
                                            <!-- Customize how options are displayed in the dropdown -->
                                            <template #option="slotProps">
                                                {{ slotProps.option.name }} ({{ slotProps.option.code }})
                                            </template>

                                            <!-- Customize how the selected item is displayed -->
                                            <template #selectedItem="slotProps">
                                                {{ slotProps.value.name }} ({{ slotProps.value.code }})
                                            </template>
                                        </Select>
                                        <div v-if="$page.props.errors.scheduleStatus" class="text-red-500 !mt-1">
                                            {{ $page.props.errors.scheduleStatus }}
                                        </div>
                                    </div>

                                    <div v-if="form.scheduleStatus && form.scheduleStatus.id === 1 && hours > 480" class="space-y-2 sm:col-span-2">
                                        <InputLabel value="Denumire serviciu (opțional)" />
                                        <div class="flex items-center space-x-2">
                                            <Select
                                                v-model="form.displayCode"
                                                :options="displayOptions"
                                                optionLabel="name"
                                                optionValue="code"
                                                placeholder="Selectează denumire serviciu"
                                                class="w-full md:w-14rem"
                                            >
                                                <template #value="slotProps">
                                                    <div v-if="slotProps.value" class="flex align-items-center">
                                                        <div>{{ displayOptions.find(opt => opt.code === slotProps.value)?.name }}</div>
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
                                            <button
                                                type="button"
                                                v-if="form.displayCode"
                                                @click="form.displayCode = null"
                                                class="p-2 text-gray-400 hover:text-gray-500"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-row items-center space-x-3 bg-brand/10 rounded-md p-4 my-2">
                                        <ClockIcon class="h-7 w-7 text-indigo-600 hidden sm:block" />
                                        <div class="flex flex-col">
                                            <div class="text-sm">Total minute:</div>
                                            <div class="text-lg font-semibold">{{ hours }}</div>
                                        </div>
                                    </div>

                                    <div></div>

                                    <div v-if="!isEditMode">
                                        <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                                Adaugă
                                            </PrimaryButton>

                                            <SecondaryButton @click="visible = false">
                                                Anulează
                                            </SecondaryButton>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="updateRecord">
                                                Actualizează
                                            </PrimaryButton>
                                            <SecondaryButton type="submit" severity="danger" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteRecord">
                                                Șterge
                                            </SecondaryButton>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </Drawer>
                    </div>

                </div>
            </div>
        </main>
        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

<script setup>

import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { ClockIcon } from '@heroicons/vue/24/solid'
import { ExclamationCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useConfirm } from 'primevue/useconfirm'
import { Switch } from '@headlessui/vue'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import CalendarTimeline from '@/Components/shared/c-calendar-timeline.vue'
import Select from 'primevue/select'
import Drawer from 'primevue/drawer'
import InputLabel from '@/Components/elements/InputLabel.vue'
import DatePicker from 'primevue/datepicker'
import ConfirmDialog from 'primevue/confirmdialog'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import SecondaryButton from '@/Components/elements/SecondaryButton.vue'

import { DateTime } from 'luxon'

const props = defineProps({
    pageTitle: String,
    employees: Array,
    scheduleStatuses: Array,
    events: Array,
    businessUnitGroups: Array, // Add this prop
})

// Get toast interface
const selectedBusinessUnitGroup = ref(null) // Add this ref
const filteredEmployees = ref([]) // Add this ref to store the filtered employees
const toast = useToast()
const confirm = useConfirm()

const filterEmployee = ref(null)
const currentViewDates = ref({
    start: null,
    end: null,
})

const calendarEvents = ref([])
const choosePeriod = ref(false)
const periodDates = ref(null)

const isEditMode = ref(false)
const calendarInteraction = ref(null)
const visible = ref(false)

// a computed ref
const hours = computed(() => {
    if (form.dateEnd && form.dateStart) {
        const dateS = new Date(form.dateStart)
        const dateE = new Date(form.dateEnd)
        let diff = (dateE.getTime() - dateS.getTime()) / 1000
        diff /= 60

        return Math.abs(Math.round(diff))
    } else {
        return 0
    }
})

const displayOptions = ref([
    { name: 'OS', code: 'OS' },
    { name: 'Dispecer', code: 'Disp' },
    { name: 'Continuitate', code: 'Cont' },
    { name: 'PP1 Zi', code: 'PP1 Zi' },
    { name: 'PP1 N', code: 'PP1 N' },
    { name: 'PCA1', code: 'PCA1' },
    { name: 'PCA2', code: 'PCA2' },
    { name: 'Auto', code: 'Auto' },
])

// Load employees by business unit group
const loadEmployeesByBusinessUnitGroup = async () => {
    if (!selectedBusinessUnitGroup.value) {
        filteredEmployees.value = []
        filterEmployee.value = null
        return
    }

    try {
        // Reset the selected employee
        filterEmployee.value = null
        calendarEvents.value = []

        // Make an API call to get employees by business unit group
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

// Initialize with all employees
onMounted(() => {
    filteredEmployees.value = props.employees
    calendarEvents.value = props.events

    console.log(DateTime.now().toFormat('yyyy-MM-dd hh:mm:ss'))

    form.dateStart = new Date(DateTime.now().set({ hour: 8, minute: 0, second: 0, millisecond: 0 }).toFormat('yyyy-MM-dd HH:mm:ss'))
    console.log(form.dateStart)
    form.dateEnd = new Date(DateTime.now().set({ hour: 16, minute: 0, second: 0, millisecond: 0 }).toFormat('yyyy-MM-dd HH:mm:ss'))
})

const form = useForm({
    formAction: 'add',
    recordId: 0,
    scheduleStatus: null,
    employee: null,
    dateStart: null,
    dateEnd: null,
    displayCode: null,
})

function changeEmployee (event) {
    console.log(event.value)
    axios.post('/employee/events', {
        employeeId: event.value.id,
        startString: currentViewDates.value?.start,
        endString: currentViewDates.value?.end,
    }).then(response => {
        console.log(response.data)

        if (response.data.result === 'RESULT_OK') {
            console.log('result ok - show events')
            calendarEvents.value = response.data.events
            calendarInteraction.value.refresh()
        } else {
            console.log('result error adding events')
        }
    })
}

function showPlanifica () {
    if (!filterEmployee.value) {
        toast.error('Selectează o persoană!', {
            timeout: 2000,
            position: 'bottom-right',
        })
        return
    }

    visible.value = true
    form.reset('scheduleStatus')
    form.reset('employee')
    form.reset('displayCode')

    isEditMode.value = false
    visible.value = true
    form.formAction = 'add'
}

function submit () {
    console.log('submit')
    if (choosePeriod.value && periodDates.value) {
        submitBulkSchedule()
    } else {
        const dateS = new Date(form.dateStart)
        const formattedDateS = dateS.toLocaleString('ro-RO', { timeZoneName: 'short' })

        const dateF = new Date(form.dateEnd)
        const formattedDateF = dateF.toLocaleString('ro-RO', { timeZoneName: 'short' })

        form
            .transform((data) => ({
                ...data,
                employee: filterEmployee.value,
                dateStart: formattedDateS,
                dateEnd: formattedDateF,
            }))
            .post('/adauga-activitate-personal', {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset('scheduleStatus')
                    form.reset('employee')
                    form.reset('displayCode')

                    form.dateStart = new Date(DateTime.now().set({
                        hour: 8,
                        minute: 0,
                        second: 0,
                        millisecond: 0,
                    }).toFormat('yyyy-MM-dd HH:mm:ss'))

                    form.dateEnd = new Date(DateTime.now().set({
                        hour: 16,
                        minute: 0,
                        second: 0,
                        millisecond: 0,
                    }).toFormat('yyyy-MM-dd HH:mm:ss'))

                    visible.value = false

                    refresh()

                    if (form.formAction === 'add') {
                        toast.success('Evenimentul a fost creat cu succes!', {
                            timeout: 2000,
                            position: 'bottom-right',
                        })
                    }
                },
            })
    }
}

function submitBulkSchedule () {
    // Validate input
    if (!periodDates.value || !periodDates.value[0] || !periodDates.value[1]) {
        toast.error('Vă rugăm să selectați o perioadă validă!', {
            timeout: 2000,
            position: 'bottom-right',
        })
        return
    }

    if (!form.scheduleStatus) {
        toast.error('Vă rugăm să selectați o situație de prezență!', {
            timeout: 2000,
            position: 'bottom-right',
        })
        return
    }

    // Prepare bulk schedule data
    const bulkScheduleData = {
        formAction: 'bulk',
        scheduleStatus: form.scheduleStatus,
        employee: filterEmployee.value,
        startDate: periodDates.value[0].toLocaleString('ro-RO', { timeZoneName: 'short' }),
        endDate: periodDates.value[1].toLocaleString('ro-RO', { timeZoneName: 'short' }),
        displayCode: form.displayCode,
    }

    // Send request to backend endpoint
    axios.post('/adauga-activitate-personal-bulk', bulkScheduleData)
        .then(response => {
            toast.success(`Programare realizată cu succes pentru ${response.data.created_schedules} zile!`, {
                timeout: 2000,
                position: 'bottom-right',
            })

            // Reset form and UI
            form.reset('scheduleStatus')
            form.reset('employee')
            form.reset('displayCode')
            choosePeriod.value = false
            periodDates.value = null

            visible.value = false
            refresh() // Refresh calendar events
        })
        .catch(error => {
            if (error.response && error.response.data.conflicting_dates) {
                const conflictDates = error.response.data.conflicting_dates
                let errorMessage = ''

                if (conflictDates.length === 1) {
                    // Singular case - one day with conflict
                    errorMessage = `Nu se poate programa: există deja un eveniment în data de: ${conflictDates[0]}`
                } else {
                    // Plural case - multiple days with conflicts
                    errorMessage = `Nu se poate programa: există deja evenimente în zilele: ${conflictDates.join(', ')}`
                }

                toast.error(errorMessage, {
                    timeout: 5000, // Increase timeout for longer messages
                    position: 'bottom-right',
                })
            } else {
                toast.error('Eroare la programarea în masă: ' + (error.response?.data?.message || error.message), {
                    timeout: 2000,
                    position: 'bottom-right',
                })
            }
        })
}

const showEvent = (event) => {
    isEditMode.value = true
    const eventData = event.clickInfo.event
    console.log(eventData)

    axios.post('/employee/events/' + eventData.id, {
        rowId: eventData.id,
    }).then(response => {
        console.log(response.data)
        if (response.data.result === 'RESULT_OK') {
            form.formAction = 'edit'
            form.recordId = response.data.event.id
            form.scheduleStatus = response.data.event.schedule_status
            form.employee = response.data.event.employee
            form.dateStart = new Date(response.data.event.date_start)
            form.dateEnd = new Date(response.data.event.date_finish)
            form.displayCode = response.data.event.display_code // Add this line
            visible.value = true
        } else {
            console.log('result error adding events')
        }
    })
}

function updateRecord () {
    const dateS = new Date(form.dateStart)
    const formattedDateS = dateS.toLocaleString('ro-RO', { timeZoneName: 'short' })

    const dateF = new Date(form.dateEnd)
    const formattedDateF = dateF.toLocaleString('ro-RO', { timeZoneName: 'short' })

    form
        .transform((data) => ({
            ...data,
            dateStart: formattedDateS,
            dateEnd: formattedDateF,
        }))
        .post('/actualizeaza-activitate-personal', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('scheduleStatus')
                form.reset('employee')

                form.dateStart = new Date(DateTime.now().set({
                    hour: 8,
                    minute: 0,
                    second: 0,
                    millisecond: 0,
                }).toFormat('yyyy-MM-dd HH:mm:ss'))

                form.dateEnd = new Date(DateTime.now().set({
                    hour: 16,
                    minute: 0,
                    second: 0,
                    millisecond: 0,
                }).toFormat('yyyy-MM-dd HH:mm:ss'))

                visible.value = false

                refresh()

                if (form.formAction === 'edit') {
                    toast.success('Evenimentul a fost actualizat cu succes!', {
                        timeout: 2000,
                        position: 'bottom-right',
                    })
                }
            },
        })
}

function deleteRecord () {
    confirm.require({
        header: 'Șterge activitate persoană',
        message: 'Ești sigur că vrei să ștergi această activitate din planificarea persoanei?',
        icon: 'pi pi-info-circle !text-red-500',
        rejectLabel: 'Anulează',
        acceptLabel: 'Șterge',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            form.formAction = 'delete'

            form
                .post('/sterge-activitate-personal', {
                    preserveScroll: true,
                    onSuccess: () => {
                        form.reset('scheduleStatus')
                        form.reset('employee')

                        form.dateStart = new Date(DateTime.now().set({
                            hour: 8,
                            minute: 0,
                            second: 0,
                            millisecond: 0,
                        }).toFormat('yyyy-MM-dd HH:mm:ss'))

                        form.dateEnd = new Date(DateTime.now().set({
                            hour: 16,
                            minute: 0,
                            second: 0,
                            millisecond: 0,
                        }).toFormat('yyyy-MM-dd HH:mm:ss'))

                        visible.value = false

                        refresh()

                        if (form.formAction === 'delete') {
                            toast.success('Evenimentul a fost șters cu succes!', {
                                timeout: 2000,
                                position: 'bottom-right',
                            })
                        }
                    },
                })
        },
        reject: () => {
            console.log('do not delete')
        },
    })
}

function refresh (startDate, endDate) {
    console.log('StartDate: ' + startDate)
    console.log('EndDate: ' + endDate)

    const start = startDate || currentViewDates.value?.start
    const end = endDate || currentViewDates.value?.end

    if (filterEmployee.value) {
        axios.post('/employee/events', {
            employeeId: filterEmployee.value.id,
            startString: start,
            endString: end,
        }).then(response => {
            console.log(response.data)

            if (response.data.result === 'RESULT_OK') {
                console.log('result ok - show events')
                calendarEvents.value = response.data.events
                calendarInteraction.value.refresh()
            } else {
                console.log(response.data)
                console.log('result error adding events')
            }
        })
    }
}

function updateDateEnd (newDateStart) {
    if (newDateStart) {
        const start = new Date(newDateStart)
        form.dateEnd = new Date(start.getTime() + 8 * 60 * 60 * 1000)
    }
}

const datesSetEvent = (event) => {
    console.log(event)
    currentViewDates.value = {
        start: event.eventData.startStr,
        end: event.eventData.endStr,
    }
    refresh(event.eventData.startStr, event.eventData.endStr)
}

watch([() => form.scheduleStatus, hours], ([newStatus, newHours]) => {
    if ((newStatus?.id !== 1 || newHours <= 480) && form.displayCode) {
        form.displayCode = null
    }
})

</script>

<style>
input::placeholder {
    text-transform: none;
    font-size:0.875rem;
}
</style>
