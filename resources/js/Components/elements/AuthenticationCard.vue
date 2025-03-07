<template>
    <div class="px-5 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-main">
        <div>
            <img :src="'/images/logo.svg'" class="max-w-xs w-full mx-auto">
        </div>

        <div class="w-full sm:max-w-md mt-7 p-6 sm:p-8 bg-white overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
