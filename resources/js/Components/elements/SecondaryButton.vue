<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    severity: {
        type: String,
        validator (value) {
            return ['info', 'warning', 'danger'].includes(value)
        },
        default: 'info',
    },
})

</script>

<template>
    <button
        :type="type"
        :severity="severity"
        :class="[
            'inline-flex',
            'items-center',
            'bg-white',
            'border',
            {'hover:bg-brand/10': props.severity==='info'},
            {'border-brand': props.severity==='info'},
            {'text-brand': severity==='info'},
            {'hover:bg-yellow-600/10': props.severity==='warning'},
            {'border-yellow-600': props.severity==='warning'},
            {'text-yellow-600': severity==='warning'},
            {'hover:bg-red-600/10': props.severity==='danger'},
            {'border-red-500': props.severity==='danger'},
            {'text-red-600': severity==='danger'},
            'font-medium',
            'uppercase',
            'text-sm',
            'rounded-md',
            'px-5',
            'py-3',
        ]"
    >
        <slot />
    </button>
</template>
