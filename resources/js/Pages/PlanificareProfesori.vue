<template>
    <div>

        <!-- PAGE TITLE -->
        <Head title="Planificare activități profesori" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24 border-b border-line">

                        <!-- PAGE HEADER -->
                        <Header pageTitle="Planificare activități profesori" totalText="Total profesori" :totalCount="teachers.length" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <Select v-model="filterTeacher" :options="teachers" filter optionLabel="employee.full_name" placeholder="Selectează profesor" @change="changeTeacher" class="w-full md:w-60">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex align-items-center">
                                        <div>{{ slotProps.value.employee.full_name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex align-items-center">
                                        <div>{{ slotProps.option.employee.full_name }}</div>
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
                        <div v-if="filterTeacher" class="space-y-5">
                            <CalendarTimeline
                                :events="calendarEvents"
                                :slotDurationProps="'00:15:00'"
                                :slotMinTimeProps="'08:00:00'"
                                :slotMaxTimeProps="'22:30:00'"
                                ref="calendarInteraction"
                                @show-event="showEvent"
                                @dates-set-event="datesSetEvent"
                            />

                            <div class="flex space-x-5">
                                <div v-for="activityType in activities" :key="activityType.id">
                                    <div class="flex space-x-1.5 items-center">
                                        <div :class="`${activityType.background} h-3.5 w-3.5 rounded-full`" />
                                        <p class="text-sm"> {{ activityType.name }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="relative">
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <div class="flex items-center space-x-2 border-2 border-brand text-brand rounded-md p-5">
                                    <ExclamationCircleIcon class="h-7 w-7 flex-shrink-0 text-brand" />
                                    <p>Te rugăm să selectezi un profesor!</p>
                                </div>
                            </div>
                            <div class="blur-sm">
                                <CalendarTimeline :events="calendarEvents" :slotDurationProps="'00:15:00'" :slotMinTimeProps="'07:00:00'" :slotMaxTimeProps="'22:30:00'" />
                            </div>
                        </div>
                    </div>

                    <div class="card flex justify-content-center">
                        <Drawer v-model:visible="visible" header="Drawer" position="right" style="width:100%; max-width: 32rem">
                            <template #header>
                                <div class="flex align-items-center gap-2 mr-auto">
                                    <h2 class="font-semibold text-lg text-brand uppercase">{{ filterTeacher.employee.full_name }}</h2>
                                </div>
                            </template>

                            <div class="border-t border-line py-6">
                                <p class="text-base">Adaugă mai jos detaliile necesare.</p>

                                <form @submit.prevent="submit" class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 mt-5">

                                    <div class="space-y-2">
                                        <InputLabel value="Tip activitate" />
                                        <Select v-model="form.activityType" :options="activities" optionLabel="name" placeholder="Selectează tip activitate" class="w-full md:w-14rem" />
                                        <div v-if="$page.props.errors.activityType" class="text-red-500 !mt-1"> {{ $page.props.errors.activityType }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Modul" />
                                        <Select v-model="form.module" :options="modules" optionLabel="name" placeholder="Selectează modul" @change="changeModule" class="w-full md:w-14rem" />
                                        <div v-if="$page.props.errors.module" class="text-red-500 !mt-1"> {{ $page.props.errors.module }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Abilitate" />
                                        <Select v-model="form.ability" :options="filterFrmAbility" :disabled="!form.module" optionLabel="name" placeholder="Selectează abilitate" @change="changeAbility" class="w-full md:w-14rem" />
                                        <div v-if="$page.props.errors.ability" class="text-red-500 !mt-1"> {{ $page.props.errors.ability }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Temă" />
                                        <Select v-model="form.theme" :options="filterFrmTheme" :disabled="!form.ability" optionLabel="name" placeholder="Selectează temă" class="w-full md:w-14rem" />
                                        <div v-if="$page.props.errors.theme" class="text-red-500 !mt-1"> {{ $page.props.errors.theme }} </div>
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <InputLabel value="Locație" />
                                        <Select v-model="form.location" :options="locations" optionLabel="name" placeholder="Selectează locație" class="w-full" />
                                        <div v-if="$page.props.errors.location" class="text-red-500 !mt-1"> {{ $page.props.errors.location }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Data începerii activității" />
                                        <DatePicker v-model="form.dateStart" dateFormat="dd.mm.yy" :minDate="new Date()" :stepMinute="5" :maxDate="new Date(new Date().getFullYear(), 11, 31)" showTime hourFormat="24" placeholder="Alege" class="w-full md:w-14rem"/>
                                        <div v-if="$page.props.errors.dateStart" class="text-red-500 !mt-1"> {{ $page.props.errors.dateStart }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Data finalizării activității" />
                                        <DatePicker v-model="form.dateEnd" dateFormat="dd.mm.yy" :minDate="form.dateStart" :maxDate="new Date(new Date().getFullYear(), 11, 31)" :stepMinute="5" showTime hourFormat="24" placeholder="Alege" class="w-full md:w-14rem"/>
                                        <div v-if="$page.props.errors.dateEnd" class="text-red-500 !mt-1"> {{ $page.props.errors.dateEnd }} </div>
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
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { ClockIcon } from '@heroicons/vue/24/solid'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { useConfirm } from 'primevue/useconfirm'

import _map from 'lodash/map'
import _without from 'lodash/without'

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

import { DateTime, Duration } from 'luxon'

const props = defineProps({
    pageTitle: String,
    teachers: Array,
    modules: Array,
    abilities: Array,
    activities: Array,
    themes: Array,
    locations: Array,
    events: Array,
})

// Get toast interface
const toast = useToast()
const confirm = useConfirm()

const filterTeacher = ref(null)
const filterFrmTheme = ref([])
const filterFrmAbility = ref([])

const calendarEvents = ref([])
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

onMounted(() => {
    calendarEvents.value = props.events

    console.log(DateTime.now().toFormat('yyyy-MM-dd hh:mm:ss'))

    form.dateStart = new Date(DateTime.now().toFormat('yyyy-MM-dd HH:mm:ss'))
    console.log(form.dateStart)
    const dur = Duration.fromObject({ minutes: 30 })
    form.dateEnd = new Date(DateTime.now().plus(dur).toFormat('yyyy-MM-dd HH:mm:ss'))
    console.log(form.dateEnd)
})

const form = useForm({
    formAction: 'add',
    recordId: 0,
    activityType: null,
    module: null,
    ability: null,
    theme: null,
    location: null,
    teacher: null,
    dateStart: null,
    dateEnd: null,
})

function changeTeacher (event) {
    console.log(event.value)
    // eslint-disable-next-line no-undef
    axios.post('/teacher/events', {
        teacherId: event.value.id,
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

function changeModule (event) {
    console.log(event.value)

    let opts = _map(props.abilities, function (obj) {
        if (obj.module_id === event.value.id) return obj
    })

    opts = _without(opts, undefined)
    console.log(opts)

    filterFrmAbility.value = opts
}

function changeAbility (event) {
    console.log(event.value)

    let opts = _map(props.themes, function (obj) {
        if (obj.ability_id === event.value.id) return obj
    })

    opts = _without(opts, undefined)
    console.log(opts)

    filterFrmTheme.value = opts
}

function showPlanifica () {
    if (!filterTeacher.value) {
        toast.error('Selectează un profesor!', {
            timeout: 2000,
            position: 'bottom-right',
        })
        return
    }

    visible.value = true
    form.reset('activityType')
    form.reset('module')
    form.reset('ability')
    form.reset('theme')
    form.reset('location')
    form.reset('teacher')

    isEditMode.value = false
    visible.value = true
    form.formAction = 'add'
}

function submit () {
    console.log('submit')

    const dateS = new Date(form.dateStart)
    const formattedDateS = dateS.toLocaleString('ro-RO', { timeZoneName: 'short' })

    const dateF = new Date(form.dateEnd)
    const formattedDateF = dateF.toLocaleString('ro-RO', { timeZoneName: 'short' })

    form
        .transform((data) => ({
            ...data,
            teacher: filterTeacher.value,
            dateStart: formattedDateS,
            dateEnd: formattedDateF,
        }))
        .post('/adauga-activitate-profesor', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('activityType')
                form.reset('module')
                form.reset('ability')
                form.reset('theme')
                form.reset('location')
                form.reset('teacher')

                form.dateStart = new Date(DateTime.now().toFormat('yyyy-MM-dd HH:mm:ss'))
                console.log(form.dateStart)
                const dur = Duration.fromObject({ minutes: 30 })
                form.dateEnd = new Date(DateTime.now().plus(dur).toFormat('yyyy-MM-dd HH:mm:ss'))
                console.log(form.dateEnd)

                visible.value = false

                refresh()
                calendarInteraction.value.refresh()

                if (form.formAction === 'add') {
                    // or with options
                    toast.success('Evenimentul a fost creat cu succes!', {
                        timeout: 2000,
                        position: 'bottom-right',
                    })
                }
            },
        })
}

const showEvent = (event) => {
    // console.log(event.clickInfo)
    isEditMode.value = true
    const eventData = event.clickInfo.event
    console.log(eventData)

    // eslint-disable-next-line no-undef
    axios.post('/teacher/events/' + eventData.id, {
        rowId: eventData.id,
    }).then(response => {
        console.log(response.data)
        if (response.data.result === 'RESULT_OK') {
            form.formAction = 'edit'
            form.recordId = response.data.event.id
            form.activityType = response.data.event.learning_activity_type
            form.module = response.data.event.module
            form.ability = response.data.event.ability
            form.theme = response.data.event.theme
            form.location = response.data.event.location
            form.teacher = response.data.event.teacher
            form.dateStart = new Date(response.data.event.date_start)
            form.dateEnd = new Date(response.data.event.date_finish)

            // load asociated data
            filterFrmTheme.value = response.data.themes
            filterFrmAbility.value = response.data.abilities

            visible.value = true
        } else {
            console.log('result error adding events')
        }
    })
}

const datesSetEvent = (event) => {
    console.log(event)
    // reload calendar because view is changed
    refresh(event.eventData.startStr, event.eventData.endStr)
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
        .post('/actualizeaza-activitate-profesor', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('activityType')
                form.reset('module')
                form.reset('ability')
                form.reset('theme')
                form.reset('location')
                form.reset('teacher')

                form.dateStart = new Date(DateTime.now().toFormat('yyyy-MM-dd HH:mm:ss'))
                console.log(form.dateStart)
                const dur = Duration.fromObject({ minutes: 30 })
                form.dateEnd = new Date(DateTime.now().plus(dur).toFormat('yyyy-MM-dd HH:mm:ss'))
                console.log(form.dateEnd)

                visible.value = false

                refresh()
                calendarInteraction.value.refresh()

                if (form.formAction === 'edit') {
                    // or with options
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
        header: 'Șterge activitate profesor',
        message: 'Ești sigur că vrei să ștergi această activitate din planificarea profesorului?',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Anulează',
        acceptLabel: 'Șterge',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            form.formAction = 'delete'

            form
                .post('/sterge-activitate-profesor', {
                    preserveScroll: true,
                    onSuccess: () => {
                        form.reset('activityType')
                        form.reset('module')
                        form.reset('ability')
                        form.reset('theme')
                        form.reset('location')
                        form.reset('teacher')

                        form.dateStart = new Date(DateTime.now().toFormat('yyyy-MM-dd HH:mm:ss'))
                        console.log(form.dateStart)
                        const dur = Duration.fromObject({ minutes: 30 })
                        form.dateEnd = new Date(DateTime.now().plus(dur).toFormat('yyyy-MM-dd HH:mm:ss'))
                        console.log(form.dateEnd)

                        visible.value = false

                        refresh()
                        calendarInteraction.value.refresh()

                        if (form.formAction === 'delete') {
                            // or with options
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

/**
 * Refresh all data
 */
function refresh (startDate, endDate) {
    console.log('StartDate: ' + startDate)
    console.log('EndDate: ' + endDate)

    if (filterTeacher.value) {
        // eslint-disable-next-line no-undef
        axios.post('/teacher/events', {
            teacherId: filterTeacher.value.id,
            startString: startDate,
            endString: endDate,
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
}

</script>
