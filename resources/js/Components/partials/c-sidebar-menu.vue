<template>
    <div>

        <!-- MOBILE VIEW -->
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>

                            <!-- MENU -->
                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white p-6">
                                <div class="flex h-16 shrink-0 items-center justify-center">
                                    <Link href="/"><img class="h-12 w-auto" :src="'/images/logo.svg'" alt="SAPVLC" /></Link>
                                </div>
                                <nav class="flex flex-1 flex-col pt-1">
                                    <ul role="list" class="flex flex-1 flex-col gap-y-2">
                                        <li v-for="item in navigation" :key="item.name">
                                            <Link
                                                v-if="!item.children"
                                                :key="item.name"
                                                :href="item.href"
                                                :class="[
                                                    currentMenu === item.href ? 'bg-brand text-white' : 'text-brand',
                                                    currentMenu !== item.href ? 'text-primary hover:text-brand' : '',
                                                    'group flex items-center gap-x-3 rounded-md p-2.5 text-sm'
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[currentMenu === item.href ? 'h-6 w-6 flex-shrink-0 text-white' : 'group-hover:text-brand h-6 w-6 flex-shrink-0 text-gray-400', 'group']"
                                                    aria-hidden="true"
                                                />
                                                <span class="flex-1">{{ item.name }}</span>
                                            </Link>
                                            <Disclosure as="div" v-else v-slot="{ open }">
                                                <DisclosureButton :class="[
                                                    currentMenu === item.href ? 'bg-brand text-white' : 'text-brand',
                                                    currentMenu !== item.href ? 'text-primary hover:text-brand' : '',
                                                    'group flex items-center gap-x-3 rounded-md p-2.5 text-sm w-full'
                                                ]">
                                                    <component
                                                        :is="item.icon"
                                                        :class="[currentMenu === item.href ? 'h-6 w-6 flex-shrink-0 text-white' : 'group-hover:text-brand h-6 w-6 flex-shrink-0 text-gray-400', 'group']"
                                                        aria-hidden="true"
                                                    />
                                                    <span>{{ item.name }}</span>
                                                    <ChevronRightIcon :class="[open ? 'rotate-90' : '', '-ml-1 mt-0.5 h-4 w-4 shrink-0']" aria-hidden="true" />
                                                </DisclosureButton>
                                                <DisclosurePanel as="ul" class="mt-1">
                                                    <li v-for="subItem in item.children" :key="subItem.name">
                                                        <DisclosureButton as="a" :href="subItem.href" :class="[subItem.current ? 'text-brand' : 'hover:text-brand', 'block py-2.5 pl-12 text-sm']">{{ subItem.name }}</DisclosureButton>
                                                    </li>
                                                </DisclosurePanel>
                                            </Disclosure>
                                        </li>

                                        <form @submit.prevent="logout" class="group flex items-center text-sm w-full px-3 pt-3">
                                            <button as="button" class="flex items-center gap-x-3 w-full group-hover:text-brand">
                                                <PowerIcon class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-brand" />
                                                <span>Ieși din cont</span>
                                            </button>
                                        </form>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- DESKTOP VIEW -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-80 lg:flex-col">
            <div class="flex grow flex-col gap-y-6 overflow-y-auto divide-y divide-line bg-main px-5 py-6">
                <div class="flex shrink-0 items-center justify-center">
                    <Link href="/"><img class="h-12 w-auto mx-auto" :src="'/images/logo.svg'" alt="SAPVLC" /></Link>
                </div>
                <nav class="flex flex-1 flex-col pt-6">
                    <ul role="list" class="flex flex-1 flex-col gap-y-2">
                        <li v-for="item in navigation" :key="item.name">
                            <Link
                                v-if="!item.children"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    currentMenu === item.href ? 'bg-brand text-white' : 'text-brand',
                                    currentMenu !== item.href ? 'text-primary hover:text-brand' : '',
                                    'group flex items-center gap-x-3 rounded-md p-3 text-sm'
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[currentMenu === item.href ? 'h-6 w-6 flex-shrink-0 text-white' : 'group-hover:text-brand h-6 w-6 flex-shrink-0 text-gray-400', 'group']"
                                    aria-hidden="true"
                                />
                                <span class="flex-1">{{ item.name }}</span>
                            </Link>
                            <Disclosure as="div" v-else v-slot="{ open }">
                                <DisclosureButton :class="[
                                    currentMenu === item.href ? 'bg-brand text-white' : 'text-brand',
                                    currentMenu !== item.href ? 'text-primary hover:text-brand' : '',
                                    'group flex items-center gap-x-3 rounded-md p-3 text-sm w-full'
                                ]">
                                    <component
                                        :is="item.icon"
                                        :class="[currentMenu === item.href ? 'h-6 w-6 flex-shrink-0 text-white' : 'group-hover:text-brand h-6 w-6 flex-shrink-0 text-gray-400', 'group']"
                                        aria-hidden="true"
                                    />
                                    <span>{{ item.name }}</span>
                                    <ChevronRightIcon :class="[open ? 'rotate-90' : '', '-ml-1 mt-0.5 h-4 w-4 shrink-0']" aria-hidden="true" />
                                </DisclosureButton>
                                <DisclosurePanel as="ul">
                                    <li v-for="subItem in item.children" :key="subItem.name">
                                        <DisclosureButton as="a" :href="subItem.href" :class="[subItem.current ? 'text-brand' : 'hover:text-brand', 'block py-2.5 pl-12 text-sm']">{{ subItem.name }}</DisclosureButton>
                                    </li>
                                </DisclosurePanel>
                            </Disclosure>
                        </li>

                        <form @submit.prevent="logout" class="group flex items-center text-sm w-full px-3 pt-3">
                            <button as="button" class="flex items-center gap-x-3 w-full group-hover:text-brand">
                                <PowerIcon class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-brand" />
                                <span>Ieși din cont</span>
                            </button>
                        </form>

                        <li class="mt-auto">
                            <div class="card flex justify-center">
                                <DatePicker v-model="date" inline class="w-full" />
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- HAMBURGER BUTTON -->
        <div class="sticky top-0 z-40 flex items-center gap-x-3 bg-main px-4 py-4 shadow-sm sm:px-6 lg:hidden">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                <Bars3Icon class="h-6 w-6" />
            </button>
            <div class="flex-1 text-sm font-semibold">Meniu</div>
        </div>
    </div>
</template>

<script setup>

import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot, Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { AcademicCapIcon, BriefcaseIcon, IdentificationIcon, Squares2X2Icon, CalendarDaysIcon, ClockIcon, PowerIcon, Bars3Icon, XMarkIcon, DocumentChartBarIcon } from '@heroicons/vue/24/outline'
import { ChevronRightIcon } from '@heroicons/vue/20/solid'

import DatePicker from 'primevue/datepicker'

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: Squares2X2Icon, current: false },
    // { name: 'Management calendar', href: '/management-calendar', icon: CalendarDaysIcon, current: false },
    { name: 'Planificare profesori', href: '/planificare-profesori', icon: AcademicCapIcon, current: false },
    { name: 'Planificare personal', href: '/planificare-personal', icon: BriefcaseIcon, current: false },
    { name: 'Carnet abateri elevi', href: '/carnet-abateri-elevi', icon: IdentificationIcon, current: false },
    // { name: 'Ore recuperare', href: '/ore-recuperare', icon: ClockIcon, current: false },
    {
        name: 'Rapoarte',
        icon: DocumentChartBarIcon,
        current: false,
        children: [
            { name: 'Situatie prezență zilnică', href: '/raport-situatie-prezenta-zilnica' },
            { name: 'Situatie prezență lunară', href: '/raport-situatie-prezenta-lunara' },
            { name: 'Planificare profesori', href: '/raport-planificare-profesori' },
        ],
    },
]

const currentMenu = computed(() => {
    const path = window.location.pathname
    const foundItem = findItemByPath(navigation, path)

    if (foundItem) {
        navigation.forEach(item => {
            item.current = item === foundItem || item === foundItem.parent
        })

        return foundItem.parent ? foundItem.parent.href : foundItem.href
    }

    return false
})

function findItemByPath (items, path) {
    for (const item of items) {
        if (item.href === path) {
            return { parent: null, href: item.href }
        }
        if (item.children) {
            const childItem = findItemByPath(item.children, path)
            if (childItem) {
                return { parent: item, href: childItem.href }
            }
        }
    }
    return null
}

const date = ref()

const sidebarOpen = ref(false)

const logout = () => {
    router.post('/logout')
    location.reload()
}

</script>
