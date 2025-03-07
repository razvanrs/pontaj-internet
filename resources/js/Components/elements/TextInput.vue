<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: String,
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>

<template>
    <input
        ref="input"
        class="border-gray-200 focus:border-brand focus:ring-brand rounded-md py-2.5"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
