<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex',
            'items-center',
            'bg-brand',
            {'hover:opacity-90': !disabled},
            'text-white',
            'uppercase',
            'border',
            'border-brand',
            'uppercase',
            'text-sm',
            'font-medium',
            'rounded-md',
            'px-5',
            'py-3',
            'y-3',
            {'opacity-50 cursor-not-allowed': disabled},
        ]">
        <slot />
    </button>
</template>
