<template>
    <div>
        <!-- PAGE TITLE -->
        <Head title="Dashboard" />
        <!-- SIDEBAR -->
        <SidebarMenu />
        <main class="lg:pl-80">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-5 lg:space-y-0 py-5 xl:h-24">
                        <!-- PAGE HEADER -->
                        <Header pageTitle="Dashboard" totalText="Total profesori" :totalCount="teachers ? teachers.length : 0" />
                    </div>
                    <!-- MAIN CALENDAR -->
                    <div class="pt-8">
                        <CalendarTimegrid
                            :teachers="teachers"
                            :events="events || []"
                            ref="calendarRef"
                        />

                        <div class="flex space-x-5 mt-5">
                            <div v-for="activityType in activities" :key="activityType.id">
                                <div class="flex space-x-1.5 items-center">
                                    <div :class="`${activityType.background} h-3.5 w-3.5 rounded-full`" />
                                    <p class="text-sm"> {{ activityType.name }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import CalendarTimegrid from '@/Components/shared/c-calendar-timegrid.vue'

const props = defineProps({
    teachers: Array,
    events: Array,
    activities: Array,
})

const calendarRef = ref(null)

onMounted(() => {
    console.log('Dashboard mounted with teachers:', props.teachers?.length)
    console.log('Dashboard mounted with events:', props.events?.length)
    console.log('Dashboard mounted with activities:', props.activities?.length)

    if (props.events && props.events.length > 0) {
        console.log('First event:', props.events[0])
    }
})
</script>
