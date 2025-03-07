<template>
    <div>

        <!-- PAGE TITLE -->
        <Head title="Planificare profesori" />

        <!-- SIDEBAR -->
        <SidebarMenu class="print:hidden" />

        <main class="lg:pl-80 print:hidden">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24">

                        <!-- PAGE HEADER -->
                        <Header pageTitle="Raport planificare profesori" customText="Vezi mai jos planificarea activităților de învățământ" />

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <Select v-model="selectedCompartment" :options="compartments" filter optionLabel="name" placeholder="Selectează structura" class="w-full md:w-52">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex align-items-center">
                                        <div>{{ slotProps.value.name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex align-items-center">
                                        <div>{{ slotProps.option.name }}</div>
                                    </div>
                                </template>
                            </Select>

                            <Select v-model="selectedTeacher" :options="teachers" filter optionLabel="name" placeholder="Selectează profesor" class="w-full md:w-52">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex align-items-center">
                                        <div>{{ slotProps.value.name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex align-items-center">
                                        <div>{{ slotProps.option.name }}</div>
                                    </div>
                                </template>
                            </Select>

                            <DatePicker v-model="dates" selectionMode="range" :manualInput="false" placeholder="Selectează perioada"  class="w-full md:w-44" />

                            <button class="bg-brand hover:opacity-90 text-white uppercase text-xs font-medium rounded-md px-5 py-3" @click="printTable">
                                Printează
                            </button>
                        </div>
                    </div>

                    <!-- MAIN TABLE -->
                    <div class="pt-8">
                        <table class="table-auto border border-collapse w-full">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-1.5 text-xs text-center uppercase">Ziua/Data</th>
                                    <th class="border p-1.5 text-xs text-center">ORA</th>
                                    <th class="border p-1.5 text-xs text-center">201</th>
                                    <th class="border p-1.5 text-xs text-center">202</th>
                                    <th class="border p-1.5 text-xs text-center">203</th>
                                    <th class="border p-1.5 text-xs text-center">204</th>
                                    <th class="border p-1.5 text-xs text-center">205</th>
                                    <th class="border p-1.5 text-xs text-center">206</th>
                                    <th class="border p-1.5 text-xs text-center">207</th>
                                    <th class="border p-1.5 text-xs text-center">208</th>
                                    <th class="border p-1.5 text-xs text-center">209</th>
                                    <th class="border p-1.5 text-xs text-center">210</th>
                                    <th class="border p-1.5 text-xs text-center">211</th>
                                    <th class="border p-1.5 text-xs text-center">212</th>
                                    <th class="border p-1.5 text-xs text-center">213</th>
                                    <th class="border p-1.5 text-xs text-center">214</th>
                                    <th class="border p-1.5 text-xs text-center">215</th>
                                    <th class="border p-1.5 text-xs text-center">216</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border p-1.5 text-xs font-semibold text-center uppercase" rowspan="3">Luni<br>28.08.2023<br></td>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xs font-semibold text-center uppercase" rowspan="3">Marți<br>29.08.2023<br></td>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xs font-semibold text-center uppercase" rowspan="3">Miercuri<br>30.08.2023<br></td>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xs font-semibold text-center uppercase" rowspan="3">Joi<br>31.08.2023<br></td>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xs font-semibold text-center uppercase" rowspan="3">Vineri<br>01.09.2023<br></td>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                                <tr>
                                    <td class="border p-1.5 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center divide-y divide-primary"><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div><div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                    <td class="border p-1.5 text-xs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </main>

        <!-- PRINT TABLE -->
        <div class="hidden print:block">
            <table class="w-full">
                <tr>
                    <td class="text-xs text-center">
                        <div class="uppercase">Neclasificat</div>
                        <div>Nr. _____________ din _____________</div>
                    </td>
                    <td class="text-center">
                        <div class="uppercase text-sm font-bold">Planificarea activităților de învățământ pe perioada</div>
                        <div class="font-bold text-sm">28.08.2023 - 08.09.2023</div>
                        <div class="uppercase font-bold text-sm mt-3">Promoția iulie 2023 - aprilie 2024</div>
                    </td>
                    <td class="text-xs text-center">
                        <div class="font-bold uppercase">Aprob</div>
                        <div class="font-bold uppercase">Directorul școlii</div>
                        <div class="text-xxs italic">Chestor de poliție</div>
                        <div class="mt-2">Dr. <span class="bold">TACHE VASILE</span></div>
                    </td>
                </tr>
            </table>

            <table class="table-auto border border-black border-collapse w-full mt-10">
                <thead>
                    <tr class="bg-gray-400 font-bold">
                        <th class="border border-black p-1 text-xxs text-center uppercase">Ziua/Data</th>
                        <th class="border border-black p-1 text-xxs text-center">ORA</th>
                        <th class="border border-black p-1 text-xxs text-center">201</th>
                        <th class="border border-black p-1 text-xxs text-center">202</th>
                        <th class="border border-black p-1 text-xxs text-center">203</th>
                        <th class="border border-black p-1 text-xxs text-center">204</th>
                        <th class="border border-black p-1 text-xxs text-center">205</th>
                        <th class="border border-black p-1 text-xxs text-center">206</th>
                        <th class="border border-black p-1 text-xxs text-center">207</th>
                        <th class="border border-black p-1 text-xxs text-center">208</th>
                        <th class="border border-black p-1 text-xxs text-center">209</th>
                        <th class="border border-black p-1 text-xxs text-center">210</th>
                        <th class="border border-black p-1 text-xxs text-center">211</th>
                        <th class="border border-black p-1 text-xxs text-center">212</th>
                        <th class="border border-black p-1 text-xxs text-center">213</th>
                        <th class="border border-black p-1 text-xxs text-center">214</th>
                        <th class="border border-black p-1 text-xxs text-center">215</th>
                        <th class="border border-black p-1 text-xxs text-center">216</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-1 text-xxs font-bold text-center uppercase" rowspan="3">Luni<br>28.08.2023<br></td>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs font-bold text-center uppercase" rowspan="3">Marți<br>29.08.2023<br></td>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs font-bold text-center uppercase" rowspan="3">Miercuri<br>30.08.2023<br></td>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs font-bold text-center uppercase" rowspan="3">Joi<br>31.08.2023<br></td>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs font-bold text-center uppercase" rowspan="3">Vineri<br>01.09.2023<br></td>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">08<sup>00</sup> - 08<sup>40</sup><br>08<sup>50</sup> - 09<sup>30</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">09<sup>40</sup> - 10<sup>20</sup><br>10<sup>30</sup> - 11<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-xxs text-center tabular-nums">11<sup>40</sup> - 12<sup>20</sup><br>12<sup>30</sup> - 13<sup>10</sup></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center divide-y divide-primary">
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                            <div><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></div>
                        </td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                        <td class="border border-black p-1 text-xxs text-center"><sup>24</sup>5.3<sup>P11</sup><sub>TV</sub></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script setup>

import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'

defineProps({
    pageTitle: String,
    compartments: Array,
    teachers: Array,
})

const selectedCompartment = ref(null)
const selectedTeacher = ref(null)
const dates = ref()

const printTable = () => {
    // Trigger the print action
    window.print()
}

</script>
