<template>
    <div>

        <!-- PAGE TITLE -->
        <Head title="Management calendar" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24">

                        <!-- PAGE HEADER -->
                        <div>
                            <Header pageTitle="Management calendar" />
                            <div class="text-sm">
                                Număr zile lucrătoare luna
                                <span class="font-semibold">{{ currentMonthName.toUpperCase() }} = {{ workingDays }} zile</span>
                            </div>
                        </div>

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <button class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-3" @click="addEvent">
                                Adaugă
                            </button>
                        </div>
                    </div>

                    <!-- MAIN CALENDAR -->
                    <div class="pt-8">
                        <CalendarInteraction
                            :events="calendarEvents"
                            ref="calendarInteraction"
                            @show-event="showEvent"
                            @dates-set-event="datesSetEvent"
                        />
                    </div>

                    <div class="card flex justify-content-center">
                        <Drawer v-model:visible="visible" header="Drawer" position="right" style="width:100%; max-width: 32rem">
                            <template #header>
                                <div class="flex align-items-center gap-2 mr-auto">
                                    <h2 class="font-semibold text-base text-brand uppercase">
                                        {{ isEditMode ? 'Editează eveniment' : 'Adaugă limitare dată calendaristică' }}
                                    </h2>
                                </div>
                            </template>

                            <div class="border-t border-line py-6">
                                <p class="text-base">{{ isEditMode ? 'Modificați detaliile evenimentului' : 'Adaugă un titlu și stabilește intervalul de timp pentru a crea un eveniment nou.' }}</p>

                                <form @submit.prevent="submit" class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 mt-5">
                                    <div class="sm:col-span-2 space-y-2">
                                        <InputLabel value="Nume eveniment" />
                                        <InputText v-model="form.eventName" type="text" class="w-full" />
                                        <div v-if="$page.props.errors.eventName" class="text-red-500 !mt-1"> {{ $page.props.errors.eventName }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Dată începere eveniment" />
                                        <DatePicker v-model="form.dateStart" placeholder="Alege" class="w-full" dateFormat="dd.mm.yy" @update:modelValue="updateDatS()" />
                                        <div v-if="$page.props.errors.dateStart" class="text-red-500 !mt-1"> {{ $page.props.errors.dateStart }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Dată finalizare eveniment" />
                                        <DatePicker v-model="form.dateEnd" :minDate="form.dateStart" placeholder="Alege" class="w-full" dateFormat="dd.mm.yy" />
                                        <div v-if="$page.props.errors.dateEnd" class="text-red-500 !mt-1"> {{ $page.props.errors.dateEnd }} </div>
                                    </div>

                                    <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
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
                                        <div v-else class="flex items-center space-x-3.5 w-full">
                                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                                Actualizează
                                            </PrimaryButton>
                                            <SecondaryButton type="submit" severity="danger" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteEvent">
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
    </div>
</template>

<script setup>

import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import CalendarInteraction from '@/Components/shared/c-calendar-interaction.vue'
import Drawer from 'primevue/drawer'
import InputLabel from '@/Components/elements/InputLabel.vue'
import InputText from 'primevue/inputtext'
import DatePicker from 'primevue/datepicker'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import SecondaryButton from '@/Components/elements/SecondaryButton.vue'

const props = defineProps({
    pageTitle: String,
    persons: Array,
    hourTypes: Array,
    dayLimits: Array,
})

// Get toast interface
const toast = useToast()

const isEditMode = ref(false)
const isRefreshing = ref(false)
const calendarEvents = ref([])
const calendarInteraction = ref(null)
const currentMonth = ref(new Date().getMonth() + 1)
const currentYear = ref(new Date().getFullYear())
const monthStats = ref({
    workingDays: 0,
    freeDays: 0,
})

// Computed values for working and free days
const workingDays = computed(() => monthStats.value.workingDays)

const form = useForm({
    formAction: 'add',
    eventId: 0,
    eventName: null,
    dateStart: null,
    dateEnd: null,
})

const currentMonthName = computed(() => {
    const months = [
        'ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie',
        'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie',
    ]
    return months[currentMonth.value - 1]
})

const updateDatS = (value) => {
    // If end date is not set yet, default to the same as start date
    if (form.dateStart && !form.dateEnd) {
        form.dateEnd = new Date(form.dateStart)
    }
}

// Initialize with dayLimits prop and fetch working days info
onMounted(() => {
    if (props.dayLimits && props.dayLimits.length) {
        calendarEvents.value = props.dayLimits
    }

    // Initialize current month and year to the actual current month
    const now = new Date()
    currentMonth.value = now.getMonth() + 1
    currentYear.value = now.getFullYear()

    // Fetch working days information for current month
    fetchWorkingDaysInfo(currentYear.value, currentMonth.value)
})

watch(calendarEvents, () => {
    // Refresh working days when events change
    fetchWorkingDaysInfo(currentYear.value, currentMonth.value)
}, { deep: true })

// Fetch working days information
const fetchWorkingDaysInfo = async (year, month) => {
    try {
        isRefreshing.value = true

        // Use current events to calculate working days
        const response = await axios.get(`/working-days/${year}/${month}`, {
            params: {
                events: JSON.stringify(calendarEvents.value),
            },
        })

        if (response.data) {
            // Update the stats with the server response
            monthStats.value = {
                workingDays: response.data.working_days || 0,
                workingHours: response.data.working_hours || 0,
            }
        }
    } catch (error) {
        console.error('Error fetching working days info:', error)

        // Keep any existing values on error
    } finally {
        isRefreshing.value = false
    }
}

// Handle date change event from calendar
const datesSetEvent = (event) => {
    console.log('Date set event received', event.eventData)

    // Extract view data from the event
    const startDate = new Date(event.eventData.start || event.eventData.view.activeStart)
    const endDate = new Date(event.eventData.end || event.eventData.view.activeEnd)

    // Update current month/year based on calendar view
    // Use the middle of the visible range to determine the current month
    const viewDate = new Date(
        startDate.getTime() + (endDate.getTime() - startDate.getTime()) / 2,
    )

    // Update month and year values
    currentMonth.value = viewDate.getMonth() + 1
    currentYear.value = viewDate.getFullYear()

    console.log(`Current month set to: ${currentMonth.value}, ${currentYear.value}`)

    // Format dates to ISO strings
    const startStr = startDate.toISOString()
    const endStr = endDate.toISOString()

    // Fetch events for the new date range
    fetchEventsForDateRange(startStr, endStr)
}

// Function to fetch events for a specific date range
const fetchEventsForDateRange = async (startDate, endDate) => {
    try {
        const response = await axios.post('/day-limits/range', {
            start_date: startDate,
            end_date: endDate,
        })

        if (response.data) {
            console.log('Received events for date range:', response.data)
            calendarEvents.value = response.data

            // Update working days info after events are fetched
            fetchWorkingDaysInfo(currentYear.value, currentMonth.value)
        }
    } catch (error) {
        console.error('Error fetching events for date range:', error)
        toast.error('A apărut o eroare la încărcarea evenimentelor')
    }
}

const visible = ref(false)

const showEvent = (event) => {
    isEditMode.value = true
    const eventData = event.clickInfo.event
    console.log('Event data for editing:', eventData)

    form.eventName = eventData.title
    form.dateStart = eventData.start

    // Important: The event.end date shown by FullCalendar has already been adjusted (exclusive)
    // We need to subtract a day to get back to our original inclusive end date
    if (eventData.end) {
        const endDate = new Date(eventData.end)
        endDate.setDate(endDate.getDate() - 1) // Subtract one day to get inclusive date
        form.dateEnd = endDate
    } else {
        form.dateEnd = eventData.start
    }

    form.eventId = eventData.id
    form.formAction = 'edit'
    visible.value = true
}

const addEvent = () => {
    form.reset('eventName')
    form.reset('dateStart')
    form.reset('dateEnd')
    isEditMode.value = false
    visible.value = true
    form.formAction = 'add'
}

// New method to delete an event
const deleteEvent = () => {
    if (!form.eventId) {
        toast.error('Nu s-a putut identifica evenimentul pentru ștergere')
        return
    }

    // Switch form action to delete
    form.formAction = 'delete'
    submit()
}

function submit () {
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
        .post('/adauga-eveniment', {
            preserveScroll: true,
            onSuccess: () => {
                // After success, refresh events for the current view
                if (calendarInteraction.value && calendarInteraction.value.$refs.fullCalendar) {
                    const api = calendarInteraction.value.$refs.fullCalendar.getApi()
                    const view = api.view
                    fetchEventsForDateRange(
                        view.activeStart.toISOString(),
                        view.activeEnd.toISOString(),
                    )
                } else {
                    router.reload({ only: ['dayLimits'] })
                }

                form.reset('eventName')
                form.reset('dateStart')
                form.reset('dateEnd')
                visible.value = false

                if (form.formAction === 'add') {
                    toast.success('Evenimentul a fost creat cu succes!', {
                        timeout: 3000,
                        position: 'bottom-right',
                    })
                } else if (form.formAction === 'edit') {
                    toast.success('Evenimentul a fost actualizat cu succes!', {
                        timeout: 3000,
                        position: 'bottom-right',
                    })
                } else if (form.formAction === 'delete') {
                    toast.success('Evenimentul a fost șters cu succes!', {
                        timeout: 3000,
                        position: 'bottom-right',
                    })
                }
            },
        })
}

</script>

<style>
/* Increase padding for calendar events */
.fc-event {
  padding: 4px 6px !important; /* Increase horizontal and vertical padding */
}

/* Add some rounded corners and additional styling */
.fc .fc-daygrid-event {
  border-radius: 4px !important;
  padding: 3px 6px !important;
}

/* Increase font size slightly */
.fc-event-title {
  font-size: 0.9em !important;
  font-weight: 600 !important;
}

</style>
