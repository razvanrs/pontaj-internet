<template>
    <div>

        <!-- PAGE TITLE -->
        <Head title="Ore recuperare" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24">

                        <!-- PAGE HEADER -->
                        <Header pageTitle="Ore recuperare" totalText="Total personal" :totalCount="employees.length" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <button class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-3" @click="visible = true">
                                Recuperează
                            </button>
                        </div>
                    </div>

                    <!-- MAIN CALENDAR -->
                    <div class="pt-8">
                        <CalendarTimeline
                            :events="calendarEvents"
                            :slot-duration-props="'00:30:00'"
                            :slot-min-time-props="'07:00:00'"
                            :slot-max-time-props="'19:00:00'"
                            @show-event="handleShowEvent"
                            @dates-set-event="handleDatesSet"
                        />
                    </div>

                    <div class="card flex justify-content-center">
                        <Drawer v-model:visible="visible" header="Drawer" position="right" style="width:100%; max-width: 32rem">
                            <template #header>
                                <div class="flex align-items-center gap-2 mr-auto">
                                    <h2 class="font-semibold text-lg text-brand uppercase">Recuperează activitatea selectată</h2>
                                </div>
                            </template>

                            <div class="border-t border-line py-6">
                                <p class="text-base">Aici poți accesa și selecta activitățile pe care le-ai realizat în trecut. Alege timpul orar în care se face această recuperare.</p>

                                <div class="grid sm:grid-cols-3 gap-6 mt-5">
                                    <div class="grid gap-3.5">
                                        <InputText type="text" v-model="value" disabled class="w-full" />
                                        <InputText type="text" v-model="value" disabled class="w-full" />
                                        <InputText type="text" v-model="value" disabled class="w-full" />
                                        <InputText type="text" v-model="value" disabled class="w-full" />
                                    </div>

                                    <!-- TABLE -->
                                    <div class="sm:col-span-2 mt-4 sm:mt-0">
                                        <div class="overflow-hidden ring-1 ring-gray-200 rounded-md">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-3 py-2.5 text-left text-sm font-medium">Data</th>
                                                        <th scope="col" class="px-3 py-2.5 text-left text-sm font-medium">Nr ore</th>
                                                        <th scope="col" class="px-3 py-2.5 text-left text-sm font-medium">Tip</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-white">
                                                    <tr>
                                                        <td class="px-3 py-2 whitespace-nowrap text-xs">12.10.2023</td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">16</td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">R</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-3 py-2.5 whitespace-nowrap text-xs">14.10.2023</td>
                                                        <td class="px-3 py-2.5 whitespace-nowrap text-xs text-gray-500">4</td>
                                                        <td class="px-3 py-2.5 whitespace-nowrap text-xs text-gray-500">COS</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="my-8">
                                    <h2 class="font-semibold uppercase">Informații ore</h2>
                                    <div class="flex flex-row space-x-3.5 mt-3">
                                        <div class="flex flex-row items-center space-x-3 bg-brand/10 rounded-md p-4 w-full">
                                            <ClockIcon class="h-7 w-7 text-indigo-600 hidden sm:block" />
                                            <div class="flex flex-col">
                                                <div class="text-sm">Total ore de recuperat</div>
                                                <div class="text-lg font-semibold">20</div>
                                            </div>
                                        </div>
                                        <div class="flex flex-row items-center space-x-3 bg-brand/10 rounded-md p-4 w-full">
                                            <ClockIcon class="h-7 w-7 text-indigo-300 hidden sm:block" />
                                            <div class="flex flex-col">
                                                <div class="text-sm">Rest ore de recuperat</div>
                                                <div class="text-lg font-semibold">8</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form @submit.prevent="submit" class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 border-t border-line pt-6">
                                    <div class="space-y-2">
                                        <InputLabel value="Data începerii recuperării" />
                                        <DatePicker v-model="form.dateStart" showTime hourFormat="24" placeholder="Alege" class="w-full"/>
                                        <div v-if="$page.props.errors.dateStart" class="text-red-500 !mt-1"> {{ $page.props.errors.dateStart }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Data finalizării recuperării" />
                                        <DatePicker v-model="form.dateEnd" showTime hourFormat="24" placeholder="Alege" class="w-full"/>
                                        <div v-if="$page.props.errors.dateEnd" class="text-red-500 !mt-1"> {{ $page.props.errors.dateEnd }} </div>
                                    </div>

                                    <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Adaugă
                                        </PrimaryButton>

                                        <SecondaryButton @click="visible = false">
                                            Anulează
                                        </SecondaryButton>
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
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { ClockIcon } from '@heroicons/vue/24/solid'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import CalendarTimeline from '@/Components/shared/c-calendar-timeline.vue'
import Drawer from 'primevue/drawer'
import InputLabel from '@/Components/elements/InputLabel.vue'
import InputText from 'primevue/inputtext'
import DatePicker from 'primevue/datepicker'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import SecondaryButton from '@/Components/elements/SecondaryButton.vue'

const props = defineProps({
    pageTitle: String,
    employees: Array,
    persons: Array,
    hourTypes: Array,
})

// Get toast interface
const toast = useToast()

// Sample calendar events - in a real app, these would come from your backend
const calendarEvents = ref([
    {
        id: 1,
        title: 'Recuperare',
        start: '2025-02-25T10:00:00',
        end: '2025-02-25T12:00:00',
        backgroundColor: '#4338ca',
        borderColor: '#4338ca',
    },
    // Add more events as needed
])

const form = useForm({
    dateStart: null,
    dateEnd: null,
})

function submit () {
    router.post('/adauga-ora-recuperare', form, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('dateStart')
            form.reset('dateEnd')
            visible.value = false
            toast.success('Ziua recuperării a fost salvată cu succes!', {
                timeout: 3000,
                position: 'bottom-right',
            })
        },
    })
}

const visible = ref(false)

// Event handlers for calendar
function handleShowEvent (data) {
    console.log('Event clicked:', data.clickInfo.event)
    // You could open a drawer here with event details
}

function handleDatesSet (data) {
    console.log('Dates changed:', data.eventData.view)
    // You could fetch events for the new date range here
}
</script>
