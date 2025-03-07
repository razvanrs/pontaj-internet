<script setup>
import { computed } from 'vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
})

const proxyChecked = computed({
    get () {
        return props.checked
    },

    set (val) {
        emit('update:checked', val)
    },
})
</script>

<template>
    <input
        v-model="proxyChecked"
        type="checkbox"
        :value="value"
        class="rounded border-gray-200 text-brand focus:ring-brand w-5 h-5"
    >
</template>
