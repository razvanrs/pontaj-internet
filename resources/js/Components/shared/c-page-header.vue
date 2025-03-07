<template>
    <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-lg font-semibold uppercase"> {{ pageTitle }} </h2>

            <div v-if="customText" class="flex flex-col">
                <div class="flex items-center space-x-1.5 text-sm">
                    <div> {{ customText }} </div>
                </div>
            </div>

            <div v-if="totalText" class="flex flex-col">
                <div class="flex items-center space-x-1.5 text-sm">
                    <div> {{ totalText }}: </div>
                    <div class="font-semibold"> {{ totalCount }} </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

defineProps({
    pageTitle: String,
    totalText: String,
    totalCount: Number,
    customText: String,
})

</script>
