<template>
    <div>
        <FullCalendar :options="calendarOptions" ref="fullCalendar" />
    </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import interactionPlugin from '@fullcalendar/interaction'
import roLocale from '@fullcalendar/core/locales/ro'

export default {
    components: {
        FullCalendar,
    },
    props: {
        teachers: {
            type: Array,
            required: true,
        },
        events: {
            type: Array,
            default: () => [],
        },
    },
    data () {
        return {
            calendarOptions: {
                plugins: [resourceTimelinePlugin, interactionPlugin],
                initialView: 'resourceTimelineDay',
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth',
                },

                locales: [roLocale],
                locale: 'ro',
                resourceAreaColumns: [
                    {
                        field: 'title',
                        headerContent: 'Profesor',
                    },
                ],
                resourceOrder: 'title',
                resourceAreaWidth: '25%',
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                resources: [], // Will be populated in mounted
                events: [], // Will be populated in mounted
                eventClick: this.handleEventClick,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                },
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    meridiem: 'short',
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '19:00:00',
                timeZone: 'local',
                expandRows: true,
                eventDisplay: 'block',
                eventOverlap: false,
                eventDidMount: function (info) {
                    const eventEl = info.el
                    const event = info.event

                    // Set tooltip content
                    const title = event.themeCode + '-' + event.moduleCode

                    eventEl.setAttribute('title', `${title}`)
                },
            },
        }
    },
    mounted () {
        console.log('Calendar mounted')
        this.initializeCalendar()
    },
    methods: {
        initializeCalendar () {
            this.loadTeachers()
            this.$nextTick(() => {
                this.loadEvents()

                // Force a render after everything is loaded
                setTimeout(() => {
                    if (this.$refs.fullCalendar && this.$refs.fullCalendar.getApi) {
                        this.$refs.fullCalendar.getApi().render()
                    }
                }, 100)
            })
        },
        handleEventClick (info) {
            console.log('Event clicked:', info.event)
        },
        loadTeachers () {
            const resources = this.teachers.map(teacher => ({
                id: String(teacher.id),
                title: teacher.full_name + ' (' + teacher.total_hours + ' ore)' || 'Unnamed Teacher',
            }))

            console.log('Resources', resources)

            console.log('Setting up resources:', resources)
            this.calendarOptions.resources = resources
        },
        loadEvents () {
            if (!this.events || this.events.length === 0) {
                console.log('No events to load')
                return
            }

            console.log('Original events:', this.events)

            // Format events for the calendar
            const formattedEvents = this.events.map(event => {
                // Ensure start and end are valid dates
                const start = new Date(event.start || event.date_start)
                const end = new Date(event.end || event.date_finish || start)

                // If start and end are the same, add 1 hour to end
                if (start.getTime() === end.getTime()) {
                    end.setHours(end.getHours() + 1)
                }

                return {
                    id: String(event.id),
                    title: event.themeCode + '-' + event.moduleCode,
                    start: start.toISOString(),
                    end: end.toISOString(),
                    resourceId: String(event.teacherId || event.teacher_id),
                    backgroundColor: event.backgroundColor || this.getActivityColor(event),
                    borderColor: event.borderColor || this.getActivityColor(event),
                    extendedProps: {
                        module: event.module?.name || '',
                        ability: event.ability?.name || '',
                    },
                }
            })

            console.log('Formatted events:', formattedEvents)

            // Add events to the calendar
            this.calendarOptions.events = formattedEvents

            // If we have a ref to the calendar, refresh it
            if (this.$refs.fullCalendar && this.$refs.fullCalendar.getApi) {
                console.log('Refreshing calendar events')
                this.$refs.fullCalendar.getApi().refetchEvents()
            }
        },
        getActivityColor (event) {
            // Assign different colors based on activity type
            if (!event.learning_activity_type) return '#4f46e5'

            const colorMap = {
                1: '#3b82f6', // blue-500
                2: '#8b5cf6', // violet-500
                3: '#f97316', // orange-500
                4: '#ef4444', // red-500
                5: '#10b981', // emerald-500
            }

            return colorMap[event.learning_activity_type.id] || '#4f46e5'
        },
        refresh () {
            this.initializeCalendar()
        },
    },
    watch: {
        teachers: {
            handler () {
                this.loadTeachers()
            },
            deep: true,
        },
        events: {
            handler () {
                this.loadEvents()
            },
            deep: true,
        },
    },
}
</script>

<style>
/* Add some basic styling to make events more visible */
.fc-timeline-event {
    margin: 2px 0;
    padding: 2px;
    border-radius: 3px;
}

.fc-timeline-event .fc-event-title {
    font-weight: 500;
}

</style>
