<template>
    <div>
        <FullCalendar :options="calendarOptions" ref="fullCalendar" />
    </div>
</template>

<script>

import FullCalendar from '@fullcalendar/vue3'
import timeGridPlugin from '@fullcalendar/timegrid'
import dayGridPlugin from '@fullcalendar/daygrid'
import roLocale from '@fullcalendar/core/locales/ro'

export default {
    components: {
        FullCalendar,
    },
    props: ['events', 'calendarOptionsData', 'slotDurationProps', 'slotMinTimeProps', 'slotMaxTimeProps'],
    emits: ['showEvent', 'datesSetEvent'],
    data () {
        return {
            calendarOptions: {
                plugins: [timeGridPlugin, dayGridPlugin],
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'timeGridWeek,dayGridMonth',
                },
                locales: [roLocale],
                locale: 'ro',
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    meridiem: 'short',
                },
                eventClick: this.handleEventClick,
                datesSet: this.handleNextEvent,
                slotMinTime: '07:00:00',
                eventContent: function (arg) {
                    // Format start and end times to 24-hour format (for both views)
                    const start = new Intl.DateTimeFormat('ro-RO', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    }).format(arg.event.start)

                    const end = arg.event.end
                        ? new Intl.DateTimeFormat('ro-RO', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false,
                        }).format(arg.event.end)
                        : start

                    // Check if we're in month view
                    if (arg.view.type === 'dayGridMonth') {
                        return {
                            html: `<div class="calendar-event-content" style="
                    display: flex; 
                    flex-direction: column; 
                    align-items: start;
                    background-color: ${arg.event.backgroundColor};
                    color: white;
                    border-radius: 4px;
                    padding: 6px;
                    width: 100%;
                    line-height:1.1rem;
                ">
                    <span style="">${start} - ${end}</span>
                    <span style="font-weight: 600;">${arg.event.title}</span>
                </div>`,
                        }
                    } else {
                        return {
                            html: `<div class="calendar-event-content" style="display: flex; flex-direction: column; align-items: start; padding: 6px; line-height:1.1rem;">
                    <span style="">${start} - ${end}</span>
                    <span style="font-weight: 600;">${arg.event.title}</span>
                </div>`,
                        }
                    }
                },
            },
        }
    },
    mounted: function () {
        this.loadData()
        this.calendarOptions.slotDuration = this.slotDurationProps
        this.calendarOptions.slotMinTime = this.slotMinTimeProps
        this.calendarOptions.slotMaxTime = this.slotMaxTimeProps
    },
    methods: {
        refresh: function (event) {
            this.reset()
            this.loadData()
        },
        reset: function () {
            const events = this.$refs.fullCalendar.calendar.getEvents()
            events.forEach(function (event, index) {
                event.remove()
            })
        },
        loadData: function () {
            this.$nextTick(function () {
                const _this = this
                this.events.forEach(function (event, index) {
                    console.log(event)
                    _this.$refs.fullCalendar.calendar.addEvent(event)
                })
            })
        },
        handleEventClick: function (clickInfo) {
            console.log('Event click')
            console.log(clickInfo)
            this.$emit('showEvent', { clickInfo })
        },
        handleNextEvent: function (eventData) {
            console.log('Date set event')
            console.log(eventData)
            this.$emit('datesSetEvent', { eventData })
        },
    },
}

</script>
