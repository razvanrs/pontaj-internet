<template>
    <div>
        <!-- PAGE TITLE -->
        <Head title="Carnet abateri elevi" />

        <!-- SIDEBAR -->
        <SidebarMenu />

        <main class="lg:pl-80">
            <ConfirmDialog />
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-5 lg:space-y-0 py-5 xl:h-24">
                        <!-- PAGE HEADER -->
                        <Header pageTitle="Carnet abateri elevi" totalText="Total sancțiuni" :totalCount="studentSanctions.total" />
                        <PrimaryButton
                            @click="addSanction"
                            :disabled="!can.createUser"
                        >
                            Adauga abatere
                        </PrimaryButton>
                    </div>

                    <!-- MAIN TABLE -->
                    <div class="pt-8">
                        <div class="card">
                            <DataTable
                                v-model:filters="filters"
                                stripedRows
                                @filter="onFilter"
                                :value="studentSanctions.data"
                                :paginator="studentSanctions.last_page!==1"
                                :rows="pagination.perPage"
                                :total-records="pagination.total"
                                dataKey="id"
                                filterDisplay="row"
                                :loading="false"
                                :lazy="true"
                                @page="onPageChange"
                            >
                                <template #empty> Nu au fost găsite înregistrari. </template>
                                <template #loading> Se încarca... </template>

                                <Column header="Nume" field="student_full_name" :showFilterMenu="false" style="width: 20%">
                                    <template #body="{ data }">
                                        <span class="text-sm"> {{ data.student_full_name }} </span>
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="caută ..." class="w-full"/>
                                    </template>
                                </Column>

                                <Column header="Clasa" filterField="student_class_name" :showFilterMenu="false" style="width: 10%">
                                    <template #body="{ data }">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm"> {{ data.student_class_name }} </span>
                                        </div>
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="caută ..." class="w-full"/>
                                    </template>
                                </Column>

                                <Column header="Sancțiune" filterField="sanction_long_description" class="min-w-72" :showFilterMenu="false" style="width: 40%">
                                    <template #body="{ data }">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm"> {{ data.sanction_long_description }} </span>
                                        </div>
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="caută ..." class="w-full"/>
                                    </template>
                                </Column>

                                <Column header="Data sancțiunii" filterField="student_sanction_date" dataType="date" class="min-w-36" :showFilterMenu="false" style="width: 15%">
                                    <template #body="{ data }">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm"> {{ moment(data.student_sanction_date).format("DD/MM/YYYY") }} </span>
                                        </div>
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <DatePicker v-model="filterModel.value" @date-select="dateSelectChange" dateFormat="dd/mm/yy" @input="filterCallback" placeholder="alege ziua" class="w-full"/>
                                    </template>
                                </Column>

                                <Column header="Ofițer" filterField="officer_name" :showFilterMenu="false" style="width: 15%">
                                    <template #body="{ data }">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm"> {{ data.officer_name }} </span>
                                        </div>
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="caută ..." class="w-full"/>
                                    </template>
                                </Column>

                                <Column>
                                    <template #body="slotProps" >
                                        <div class="flex justify-center items-center space-x-3">
                                            <ActionButton
                                                :disabled="!slotProps.data.can.update"
                                                type="edit" @click="editSanction(slotProps.data.id)"
                                                label="Edit"
                                                severity="warning"
                                                class="hover:text-brand"
                                            />
                                            <ActionButton
                                                :disabled="!slotProps.data.can.delete"
                                                type="delete"
                                                @click="deleteRecord(slotProps.data.id)"
                                                label="Delete"
                                                severity="warning"
                                                class="hover:text-red-500"
                                            />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>

                    <div class="card flex justify-content-center">
                        <Drawer v-model:visible="visible" position="right" style="width:100%; max-width: 32rem" :pt="{
                            header: {
                                style: 'justify-content: space-between'
                            }
                        }">
                            <template #header>
                                <div class="flex align-items-center gap-2 mr-auto">
                                    <h2 class="font-semibold text-lg text-brand uppercase">Sanctionare elev</h2>
                                </div>
                            </template>

                            <div class="border-t border-line py-6">
                                <p class="text-base">Adaugă mai jos detaliile necesare.</p>

                                <form @submit.prevent="submit" class="grid sm:grid-cols-2 gap-x-3.5 gap-y-5 mt-5">

                                    <div class="space-y-2 sm:col-span-2 w-full">
                                        <InputLabel value="Sanctiune" />
                                        <Select
                                            v-model="form.sanction"
                                            :options="sanctions"
                                            optionLabel="long_description"
                                            optionValue="id"
                                            :placeholder="sanctions===undefined ? `Se încarca...` : `Selectează sancțiune`"
                                            :loading="sanctions===undefined"
                                            class="w-full"
                                        >
                                            <template #option="slotProps">
                                                <li class="w-auto max-w-xs sm:max-w-sm lg:max-w-md whitespace-normal">
                                                    {{ slotProps.option.long_description }}
                                                </li>
                                            </template>
                                        </Select>
                                        <div v-if="form.errors.sanction" class="text-red-500 !mt-1"> {{ form.errors.sanction }} </div>
                                    </div>

                                    <div class="space-y-2 sm:col-span-2">
                                        <InputLabel value="Elev" />
                                        <AutoComplete
                                            v-if="!isEditMode"
                                            v-model="form.student"
                                            :suggestions="students"
                                            optionLabel="full_name"
                                            optionValue="id"
                                            field="full_name"
                                            @complete="onStudentSearch"
                                            :placeholder="studentsLoading ? `Se încarcă...` : `Caută elev`"
                                            class="w-full"
                                        />
                                        <div v-if="isEditMode" class="w-full">
                                            <InputText v-model="currentStudent" disabled placeholder="Numele elevului selectat" />
                                        </div>
                                        <div v-if="form.errors.student" class="text-red-500 !mt-1"> {{ form.errors.student }} </div>
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel value="Data" />
                                        <DatePicker v-model="form.date" placeholder="Selecteaza data" class="w-full"/>
                                        <div v-if="form.errors.date" class="text-red-500 !mt-1"> {{ form.errors.date }} </div>
                                    </div>

                                    <div v-if="!isEditMode" class="space-y-2 sm:col-span-2">
                                        <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                                Adaugă
                                            </PrimaryButton>

                                            <SecondaryButton @click="visible = false">
                                                Anulează
                                            </SecondaryButton>
                                        </div>
                                    </div>
                                    <div v-else class="space-y-2 sm:col-span-2">
                                        <div class="sm:col-span-2 flex items-center space-x-3.5 mt-1">
                                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="updateRecord">
                                                Actualizează
                                            </PrimaryButton>
                                            <SecondaryButton type="submit" :class="{'opacity-25': form.processing}" :disabled="form.processing" @click="deleteRecord(form.id)">
                                                Șterge
                                            </SecondaryButton>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </Drawer>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { router, Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import moment from 'moment'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import InputLabel from '@/Components/elements/InputLabel.vue'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import SecondaryButton from '@/Components/elements/SecondaryButton.vue'

import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import AutoComplete from 'primevue/autocomplete'
import Drawer from 'primevue/drawer'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import ActionButton from '@/Components/elements/ActionButton.vue'

const props = defineProps({
    can: Object,
    studentSanctions: Object,
    filtered: Object,
    sanctions: Object,
})

const URL = '/carnet-abateri-elevi/'
const visible = ref(false)
const isEditMode = ref(false)
const confirm = useConfirm()
const toast = useToast()

const students = ref([])
const currentStudent = ref('')
const studentsLoading = ref(false)
const query = ref('')

async function onStudentSearch (event) {
    query.value = event.query

    if (query.value.length < 3) {
        students.value = [] // Clear suggestions if query length is below threshold
        return
    }

    try {
        studentsLoading.value = true
        const response = await axios.get('/students/search', { params: { query: query.value } })
        students.value = response.data
    } catch (error) {
        console.error('Error fetching students:', error)
    } finally {
        studentsLoading.value = false
    }
}

const filters = ref({
    global: { value: null },
    student_full_name: { value: null },
    student_class_name: { value: null },
    student_sanction_date: { value: null },
    sanction_long_description: { value: null },
    officer_name: { value: null },
})

onMounted(() => {
    if (props.filtered) {
        filters.value.global = props.filtered.global
        filters.value.student_full_name = props.filtered.student_full_name
        filters.value.student_class_name = props.filtered.student_class_name
        filters.value.sanction_long_description = props.filtered.sanction_long_description
        filters.value.student_sanction_date = props.filtered.student_sanction_date
        filters.value.officer_name = props.filtered.officer_name
    }
})

const loading = ref(false)
const pagination = ref({
    page: props.studentSanctions.current_page || 1,
    perPage: props.studentSanctions.per_page || 10,
    total: props.studentSanctions.total || 0,
})

const onPageChange = (event) => {
    pagination.value.page = event.page + 1
    fetchData()
}

const onFilter = (e) => {
    filters.value = e.filters
    fetchData()
}

const fetchData = () => {
    loading.value = true
    router.get('/carnet-abateri-elevi', {
        page: pagination.value.page,
        perPage: pagination.value.perPage,
        filters: filters.value,
    }, {
        preserveState: true,
        replace: true,
        only: ['studentSanctions'],
        onSuccess: () => {
            loading.value = false
            pagination.value.page = props.studentSanctions.current_page
            pagination.value.perPage = props.studentSanctions.per_page
            pagination.value.total = props.studentSanctions.total
        },
    })
}

// add
const form = useForm({
    formAction: 'add',
    id: null,
    student: null,
    sanction: null,
    studentSignature: null,
    date: null,
})

function addSanction () {
    if (props.sanctions === undefined || props.students === undefined) {
        router.reload({ only: ['sanctions'] })
    }

    form.reset()
    form.clearErrors()
    query.value = ''
    students.value = []

    isEditMode.value = false
    visible.value = true
    form.formAction = 'add'
}

function submit () {
    if (form.isEditMode) updateRecord()
    if (form.formAction === 'add') addRecord()
}

// ADD
const addRecord = () => {
    form
        .transform((data) => ({
            ...data,
            date: data.date ? (data.date.toLocaleDateString('ro-RO', { timeZoneName: 'short' })) : '',
        }))
        .post('/carnet-abateri-elevi', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
                query.value = ''

                visible.value = false

                if (form.formAction === 'add') {
                    // or with options
                    toast.success('Sancțiune aplicata cu succes!', {
                        timeout: 2000,
                        position: 'bottom-right',
                    })
                }
            },
            onError: () => {
                if (props.sanctions === undefined) {
                    router.reload({ only: ['sanctions'] })
                }
            },
        })
}

// EDIT
const editSanction = async (id) => {
    try {
        const response = await axios.post(URL + 'editeaza', { id })
        // console.log(response.data)
        form.id = response.data.id
        form.sanction = response.data.sanction.id
        form.student = response.data.student.id
        form.studentSignature = response.data.student.signature
        form.date = new Date(response.data.date)

        currentStudent.value = response.data.student.full_name

        form.clearErrors()
        query.value = ''
        isEditMode.value = true
        visible.value = true
        form.formAction = 'edit'

        if (props.sanctions === undefined) {
            router.reload({ only: ['sanctions'] })
        }
    } catch (error) {
        toast.error(error.message, {
            timeout: 2000,
            position: 'bottom-right',
        })
        console.error(error)
    }
}

// UPDATE
const updateRecord = async () => {
    form
        .transform((data) => ({
            ...data,
            date: data.date ? (data.date.toLocaleDateString('ro-RO', { timeZoneName: 'short' })) : '',
        }))
        .put(URL + form.id, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
                query.value = ''

                visible.value = false

                if (form.formAction === 'edit') {
                    // or with options
                    toast.success('Înregistrare editata cu success!', {
                        timeout: 2000,
                        position: 'bottom-right',
                    })
                }
            },
            onError: () => {
                if (props.sanctions === undefined || props.students === undefined) {
                    router.reload({ only: ['sanctions', 'students'] })
                }
            },
        })
}

// DELETE
const deleteRecord = (id) => {
    confirm.require({
        message: 'Vrei să ștergi acestă înregistrare?',
        header: 'Atenție!',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: () => {
            toast.success('Înregistrare ștearsă cu success!', {
                timeout: 2000,
                position: 'bottom-right',
            })
            router.delete(`/carnet-abateri-elevi/${id}`)
            visible.value = false

            // Check if the current page is the last page and if there are no more records on that page
            if (pagination.value.page === Math.ceil(pagination.value.total / pagination.value.perPage) && props.studentSanctions.data.length === 1) {
                // Update the page to the previous page
                pagination.value.page -= 1
                fetchData()
            }
        },
        reject: () => {
        },
    })
}

const dateSelectChange = (e) => {
    console.log(e.toLocaleDateString('ro-RO', { timeZoneName: 'short' }))
    filters.value.student_sanction_date.value = e.toLocaleDateString('ro-RO')
    fetchData()
}
</script>
