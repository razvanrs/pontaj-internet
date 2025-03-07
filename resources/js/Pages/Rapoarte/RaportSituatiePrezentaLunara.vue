<template>
    <div>

        <!-- Page Title -->
        <Head title="Situație prezență lunară" />

        <!-- Sidebar -->
        <SidebarMenu class="print:hidden" />

        <main class="lg:pl-80 print:hidden">
            <div class="px-4 sm:px-6 lg:px-8 mb-10">
                <div class="flex flex-col divide-y divide-line">

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-5 xl:space-y-0 py-5 xl:h-24">

                        <!-- Page Header -->
                        <Header pageTitle="Raport situație prezență lunară" customText="Vezi mai jos situația prezenței lunare la program a personalului"/>

                        <!-- SELECT BOXES -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3.5 space-y-3.5 sm:space-y-0">
                            <!-- Compartment Select -->
                            <Select
                                v-model="selectedBusinessUnitGroup"
                                :options="businessUnitGroups"
                                filter
                                optionLabel="name"
                                placeholder="Selectează structura"
                                class="w-full md:w-60"
                            >
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

                            <!-- Month Picker -->
                            <DatePicker
                                v-model="date"
                                view="month"
                                dateFormat="mm/yy"
                                :showIcon="true"
                                placeholder="Selectează luna"
                                class="w-full md:w-44"

                            />

                            <!-- Print Button -->
                            <button
                                class="bg-brand hover:opacity-90 text-white uppercase text-sm font-medium rounded-md px-5 py-3 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                                :disabled="!selectedBusinessUnitGroup || !reportData.people?.length"
                                @click="printTable">
                                <PrinterIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Add No Filter Selected State -->
                    <div v-if="!selectedBusinessUnitGroup" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <img :src="'/images/select-structure.png'" class="w-64 mx-auto">
                            <div class="flex flex-col mt-3">
                                <h3 class="text-lg font-medium text-brand">Selectează o structură</h3>
                                <p>Pentru a vizualiza datele, te rugăm să selectezi o structură.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-else-if="loading" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <i class="pi pi-spin pi-spinner text-5xl text-brand"></i>
                            <div class="mt-2">Se încarcă datele...</div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="!reportData.people?.length" class="flex justify-center items-center h-[calc(100vh-12rem)]">
                        <div class="text-center">
                            <img :src="'/images/no-results.png'" class="w-64 mx-auto">
                            <div class="flex flex-col mt-3">
                                <h3 class="text-lg font-medium text-brand">Nu există date disponibile</h3>
                                <p> Nu au fost găsite înregistrări pentru perioada selectată.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data State -->
                    <div v-else class="pt-8 flow-root">
                        <div class="flex flex-col space-y-1.5 mb-6">
                            <div class="text-sm">
                                Număr zile lucrătoare / lună
                                <span class="font-semibold">
                                    {{ date.toLocaleString('ro-RO', { month: 'long' }).toUpperCase() }} =
                                    {{ monthDetails.workingDays }} zile
                                </span>
                            </div>
                            <div class="text-sm">
                                Total ore / lună
                                <span class="font-semibold">
                                    {{ date.toLocaleString('ro-RO', { month: 'long' }).toUpperCase() }} =
                                    {{ monthDetails.totalHours }} ore
                                </span>
                            </div>
                        </div>

                        <!-- Main Table -->
                        <div class="overflow-hidden">
                            <table class="table-auto border border-collapse min-w-full">
                                <thead>
                                    <tr>
                                        <th class="border p-1.5 text-xs" rowspan="2">Nr. crt.</th>
                                        <th class="border p-1.5 text-xs">Data/ziua</th>
                                        <th v-for="day in monthDetails.daysInMonth" :key="day" class="border p-1.5 text-xs font-semibold">
                                            {{ day }}
                                        </th>
                                        <th class="border p-1.5 text-xs" rowspan="2">TOTAL ORE <br/>lucrate</th>
                                        <th class="border p-1.5 text-xs" rowspan="2">TOTAL ORE <br/>Spor condiții grele</th>
                                        <th class="border p-1.5 text-xs" rowspan="2">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <div>OBSERVAȚII</div>
                                                <i
                                                    class="pi pi-info-circle text-brand"
                                                    v-tooltip.bottom="'CO, CM, CS, Î, P, CP, M, S, D, CIC, PR, L, DS, LS, R'"
                                                ></i>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="border p-1.5 text-xs">Numele și Prenumele</th>
                                        <th v-for="day in monthDetails.daysInMonth" :key="`initial-${day}`" class="border p-1.5 text-xs uppercase font-semibold">
                                            {{ getDayInitial(day) }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(person, index) in reportData.people"
                                        :key="`print-person-${index}`"
                                        :class="{ 'avoid-break': hasMultipleLines(person) }"
                                    >
                                        <td class="border p-1.5 text-center text-sm">{{ index + 1 }}</td>
                                        <td class="border p-1.5 text-left text-sm whitespace-nowrap">{{ formatName(person.name) }}</td>
                                        <td
                                            v-for="day in monthDetails.daysInMonth"
                                            :key="`hours-${day}`"
                                            :class="{ 'bg-gray-100': isWeekend(day) }"
                                            class="border p-1.5 text-center text-sm whitespace-pre-line leading-3"
                                        >
                                            <template v-if="typeof person.hours[day] === 'number'">
                                                {{ person.display_codes?.[day] || (person.hours[day] === 0 ? '' : person.hours[day]) }}
                                            </template>
                                            <template v-else>
                                                <span class="font-semibold text-xs leading-3">{{ person.hours[day] }}</span>
                                            </template>
                                        </td>
                                        <td class="border p-1.5 text-center text-sm">{{ person.totalHours }}</td>
                                        <td class="border p-1.5 text-center text-sm">{{ person.spor }}</td>
                                        <td class="border p-2 text-left text-xs cursor-pointer hover:bg-gray-50 w-48"
                                            @click="openDetailsEditor(person)"
                                        >
                                            <div class="flex flex-col space-y-1">
                                                <template v-for="(detail, index) in person.details" :key="`detail-${index}`">
                                                    <div class="flex">
                                                        <!-- Read-Only Event -->
                                                        <span class="text-gray-600">
                                                            <span v-html="detail"></span>
                                                            <!-- Add ":" only if an observation exists -->
                                                            <span v-if="getEditedObservations(person) && getEditedObservations(person)[index]">:</span>
                                                        </span>

                                                        <!-- Attach Observation if Available -->
                                                        <span v-if="getEditedObservations(person) && getEditedObservations(person)[index]" class="font-medium text-brand ml-1">
                                                            {{ getEditedObservations(person)[index] }}
                                                        </span>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Update the dialog for editing observations -->
                            <Dialog v-model:visible="detailsEditorVisible" header="Editare observații" :modal="true" :style="{ width: '45rem' }">
                                <div class="p-fluid">
                                    <div class="mt-1 mb-5">
                                        <h3 class="text-sm font-semibold">{{ currentPerson?.name }}</h3>
                                        <p class="text-sm text-gray-500">Adaugă observații pentru fiecare eveniment</p>
                                    </div>
                                    <div class="field">
                                        <div class="border rounded p-3.5 bg-white space-y-3.5">
                                            <!-- Each event with corresponding observation input -->
                                            <div v-for="(detail, index) in currentPersonDetails" :key="index" class="flex items-center gap-3">
                                                <!-- Event display (read-only) -->
                                                <div class="bg-gray-100 p-2 rounded min-w-[100px] text-center font-medium" v-html="extractEventCode(detail)"></div>

                                                <!-- Observation input -->
                                                <div class="flex-grow flex items-center gap-2">
                                                    <InputText
                                                        v-model="editedObservations[index]"
                                                        class="flex-grow"
                                                        placeholder="adaugă observație"
                                                    />
                                                    <Button
                                                        icon="pi pi-trash"
                                                        class="p-button-rounded p-button-text p-button-sm flex-shrink-0"
                                                        @click="editedObservations[index] = ''"
                                                        v-tooltip="'Șterge'"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Display message if no events -->
                                            <div v-if="currentPersonDetails.length === 0" class="text-gray-500 text-sm">
                                                Nu există evenimente pentru această persoană.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <template #footer>
                                    <Button
                                        label="Anulează"
                                        size="small"
                                        @click="closeDetailsEditor"
                                        class="p-button-text"
                                    />
                                    <Button
                                        label="Salvează"
                                        size="small"
                                        @click="saveDetailsEdit"
                                        class="p-button-primary"
                                    />
                                </template>
                            </Dialog>
                        </div>

                        <!-- Signatures Preview Section -->
                        <div class="flex justify-between items-center mt-8 border-t border-gray-200 pt-5">
                            <!-- INTOCMIT -->
                            <div v-if="selectedCreator" class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                <div class="font-semibold text-sm uppercase">Întocmit,</div>
                                <div class="mt-0 text-sm italic">{{ selectedCreatorRank?.name || 'grad militar' }}</div>
                                <div class="font-semibold uppercase text-sm text-brand mt-3">
                                    {{ selectedCreator?.full_name || '...' }}
                                </div>
                            </div>
                            <div v-else class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                <div class="font-semibold text-sm uppercase">Adaugă semnătură <br> Întocmit</div>
                                <div class="font-medium text-sm text-brand mt-1">
                                    Selectează persoana
                                </div>
                            </div>

                            <!-- Show "De acord" only if approval type is 'deacord' -->
                            <div v-if="signatures.approvalType === 'deacord'">
                                <div v-if="selectedApprover" class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                    <div class="font-semibold text-sm uppercase">De acord,</div>
                                    <div class="font-semibold text-sm uppercase">
                                        {{ signatures.jobTitle || 'Funcție' }}
                                    </div>
                                    <div class="mt-0 text-sm italic">{{ selectedApproverRank?.name || 'grad militar' }}</div>
                                    <div class="font-semibold uppercase text-sm text-brand mt-3">
                                        {{ selectedApprover?.full_name || 'selectează persoana' }}
                                    </div>
                                </div>

                                <div v-else class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                    <div class="font-semibold text-sm uppercase">Adaugă semnătură <br> De acord</div>
                                    <div class="font-medium text-sm text-brand mt-1">
                                        Selectează persoana
                                    </div>
                                </div>
                            </div>
                            <!-- Show "Aprob" info if approval type is 'aprob' -->
                            <div v-else-if="signatures.approvalType === 'aprob'" class="text-center cursor-pointer hover:bg-gray-50 p-4 rounded" @click="openSignatureEditor">
                                <div class="font-semibold text-sm uppercase">Aprob,</div>
                                <div class="font-semibold text-sm uppercase">Directorul școlii</div>
                                <div class="mt-0 text-sm italic">Chestor de poliție</div>
                                <div class="font-semibold uppercase text-sm text-brand mt-3">
                                    TACHE VASILE
                                </div>
                            </div>
                        </div>

                        <!-- Signature Editor Dialog -->
                        <Dialog v-model:visible="signatureEditorVisible" header="Editare semnături" :modal="true" :style="{ width: '41rem' }">
                            <div class="p-fluid">
                                <div class="mb-3">
                                    <h3 class="font-semibold">Editează semnăturile de pe document</h3>
                                    <p class="text-sm text-gray-500">Modifică persoanele care vor semna documentul și tipul de aprobare.</p>
                                </div>

                                <!-- Add approval type selection -->
                                <div class="flex flex-row items-center space-x-3 my-5">
                                    <h4 class="font-medium">Tip aprobare:</h4>
                                    <div class="flex space-x-3">
                                        <div class="flex items-center">
                                            <input v-model="tempSignatures.approvalType" inputId="deacord" value="deacord" type="radio" class="relative size-4 appearance-none rounded-full border border-gray-300 before:absolute before:inset-1 before:rounded-full before:bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden [&:not(:checked)]:before:hidden" />
                                            <label for="deacord" class="ml-2">De acord</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input v-model="tempSignatures.approvalType" inputId="aprob" value="aprob" type="radio" class="relative size-4 appearance-none rounded-full border border-gray-300 before:absolute before:inset-1 before:rounded-full before:bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden [&:not(:checked)]:before:hidden" />
                                            <label for="aprob" class="ml-2">Aprob</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <!-- LEFT COLUMN - CREATOR -->
                                    <div class="border rounded p-3.5">
                                        <h4 class="font-medium mb-2">Întocmit de:</h4>

                                        <div class="field mb-4">
                                            <Select
                                                v-model="tempSignatures.creatorId"
                                                :options="businessUnitEmployees"
                                                optionLabel="full_name"
                                                optionValue="id"
                                                filter
                                                placeholder="Selectează persoana"
                                                class="w-full"
                                            >
                                                <template #value="slotProps">
                                                    <div v-if="slotProps.value" class="flex items-center">
                                                        <div>{{ businessUnitEmployees.find(e => e.id === slotProps.value)?.full_name }}</div>
                                                    </div>
                                                    <span v-else>
                                                        {{ slotProps.placeholder }}
                                                    </span>
                                                </template>
                                            </Select>
                                        </div>
                                    </div>

                                    <!-- RIGHT COLUMN - APPROVER -->
                                    <div class="border rounded p-3.5">
                                        <h4 class="font-medium mb-2">{{ tempSignatures.approvalType === 'deacord' ? 'De acord:' : 'Aprob:' }}</h4>

                                        <!-- For "De acord" - show dropdown and options -->
                                        <template v-if="tempSignatures.approvalType === 'deacord'">
                                            <div class="field mb-4">
                                                <Select
                                                    v-model="tempSignatures.approverId"
                                                    :options="businessUnitEmployees"
                                                    optionLabel="full_name"
                                                    optionValue="id"
                                                    filter
                                                    placeholder="Selectează persoana"
                                                    class="w-full"
                                                >
                                                    <template #value="slotProps">
                                                        <div v-if="slotProps.value" class="flex items-center">
                                                            <div>{{ businessUnitEmployees.find(e => e.id === slotProps.value)?.full_name }}</div>
                                                        </div>
                                                        <span v-else>
                                                            {{ slotProps.placeholder }}
                                                        </span>
                                                    </template>
                                                </Select>
                                            </div>

                                            <!-- Show InputText only when an approver is selected -->
                                            <div v-if="tempSignatures.approverId" class="field mb-4">
                                                <InputText
                                                    v-model="tempSignatures.jobTitle"
                                                    placeholder="adaugă funcție (ex: Șef birou)"
                                                    class="w-full"
                                                    :class="{'p-invalid': showJobTitleError}"
                                                />
                                                <small v-if="showJobTitleError" class="p-error block leading-4 mt-2">Funcția este obligatorie!</small>
                                                <small class="text-gray-500 leading-4 text-justify block mt-2">Modifică denumirea funcției dacă este necesar (ex: Șef serviciu, Contabil șef, Consilier juridic)</small>
                                            </div>
                                        </template>

                                        <!-- For "Aprob" - show fixed values -->
                                        <template v-else>
                                            <div class="field mb-4">
                                                <InputText value="Dr. TACHE VASILE" disabled class="w-full bg-gray-100" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <template #footer>
                                <Button
                                    label="Anulează"
                                    size="small"
                                    @click="closeSignatureEditor"
                                    class="p-button-text"
                                />
                                <Button
                                    label="Salvează"
                                    size="small"
                                    @click="saveSignatureEdit"
                                    class="p-button-primary"
                                />
                            </template>
                        </Dialog>
                    </div>
                </div>
            </div>
        </main>

        <!-- PRINT TABLE -->
        <div class="hidden print:block font-times">
            <!-- Header Section -->
            <div class="flex justify-between mb-8">
                <!-- Left Header -->
                <div class="text-center font-bold text-xs uppercase space-y-0.5">
                    <div>Ministerul Afacerilor Interne</div>
                    <div>Inspectoratul General al Poliției Române</div>
                    <div>Școala de Agenți de Poliție „Vasile Lascăr" Câmpina</div>
                    <div>{{ selectedBusinessUnitGroup?.name || '...' }}</div>
                </div>

                <!-- Right Header -->
                <div class="text-xs text-center space-y-1.5">
                    <div class="uppercase">Nesecret</div>
                    <div>Nr. _____________ din _____.{{ date.getFullYear() }}</div>
                    <div>Ex. ____ /</div>
                </div>
            </div>

            <!-- Approval Section -->
            <div class="flex justify-end mr-60 mt-5">
                <div class="text-xs text-center">
                    <div class="font-bold text-base uppercase">Aprob,</div>
                    <div class="font-bold text-base uppercase">Directorul școlii</div>
                    <div class="mt-0 text-base italic">Chestor de poliție</div>
                    <div class="font-bold text-base uppercase mt-3">Dr. <span class="font-bold">TACHE VASILE</span></div>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-lg uppercase max-w-3xl mx-auto mt-10">
                <span class="underline font-bold">{{ selectedBusinessUnitGroup?.name || '...' }}</span><br>
                Situația prezenței la program a personalului în luna {{ date.toLocaleString('ro-RO', { month: 'long' }) }}
                {{ date.getFullYear() }}
            </h2>

            <!-- Month Details -->
            <div class="flex flex-col mt-5 text-sm">
                <div>
                    Număr zile lucrătoare / lună
                    <span class="font-bold">
                        {{ date.toLocaleString('ro-RO', { month: 'long' }).toUpperCase() }} =
                        {{ monthDetails.workingDays }} zile
                    </span>
                </div>
                <div>
                    Total ore / lună
                    <span class="font-bold">
                        {{ date.toLocaleString('ro-RO', { month: 'long' }).toUpperCase() }} =
                        {{ monthDetails.totalHours }} ore
                    </span>
                </div>
            </div>

            <!-- Main Table -->
            <table class="table-auto border border-black/80 border-collapse w-full mt-5">
                <thead>
                    <tr>
                        <th class="border border-black/80 p-1.5 text-xs" rowspan="2">Nr. crt.</th>
                        <th class="border border-black/80 p-1.5 text-xs">Data/ziua</th>
                        <th
                            v-for="day in monthDetails.daysInMonth"
                            :key="`print-${day}`"
                            class="border border-black/80 p-1.5 text-xs w-7"
                        >
                            {{ day }}
                        </th>
                        <th class="border border-black/80 p-1.5 text-xs" rowspan="2">TOTAL ORE<br/>lucrate</th>
                        <th class="border border-black/80 p-1.5 text-xs" rowspan="2">TOTAL ORE<br/>Spor condiții grele</th>
                        <th class="border border-black/80 p-1.5 text-xs w-96" rowspan="2">
                            OBS.<br/>(CO, CM, CS, Î, P, CP, M, S, D, CIC, PR, L, DS, LS, R)
                        </th>
                    </tr>
                    <tr>
                        <th class="border border-black/80 p-1.5 text-xs">Numele și Prenumele</th>
                        <th
                            v-for="day in monthDetails.daysInMonth"
                            :key="`print-initial-${day}`"
                            class="border border-black/80 p-1.5 text-xs uppercase w-7"
                        >
                            {{ getDayInitial(day) }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(person, index) in reportData.people"
                        :key="`print-person-${index}`"
                        :class="{ 'avoid-break': hasMultipleLines(person) }"
                    >
                        <td class="border border-black/80 p-1.5 text-center text-sm">{{ index + 1 }}</td>
                        <td class="border border-black/80 p-1.5 text-left text-sm whitespace-nowrap">{{ formatName(person.name) }}</td>
                        <td
                            v-for="day in monthDetails.daysInMonth"
                            :key="`print-hours-${day}`"
                            :class="{ 'bg-gray-300': isWeekend(day) }"
                            class="border border-black/80 p-1.5 text-center text-sm w-7"
                        >
                            <template v-if="typeof person.hours[day] === 'number'">
                                {{ person.display_codes?.[day] || formatHours(person.hours[day]) }}
                            </template>
                            <template v-else>
                                <span class="font-bold">{{ person.hours[day] }}</span>
                            </template>
                        </td>
                        <td class="border border-black/80 p-1.5 text-center text-sm">{{ person.totalHours }}</td>
                        <td class="border border-black/80 p-1.5 text-center text-sm">{{ person.spor }}</td>
                        <td class="border border-black/80 p-2 text-left text-xs whitespace-nowrap">
                            <ul class="list-none">
                                <template v-for="(detail, index) in person.details" :key="`print-detail-${index}`">
                                    <li class="mb-0.5 last:mb-0">
                                        <!-- Read-Only Event -->
                                        <span v-html="detail"></span>

                                        <!-- Add ":" only if an observation exists -->
                                        <span v-if="getEditedObservations(person) && getEditedObservations(person)[index]">:</span>

                                        <!-- Attach Observation if Available -->
                                        <span v-if="getEditedObservations(person) && getEditedObservations(person)[index]" class="ml-1">
                                            {{ getEditedObservations(person)[index] }}
                                        </span>
                                    </li>
                                </template>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer Note -->
            <div class="mt-6 text-xs leading-5">
                NOTĂ: Din numărul de 8 ore/zi vor fi scăzute orele corespunzătoare situației în care se poate afla personalul școlii:
                concediu de odihnă (CO), concediu de studiu (CS), concediu medical (CM), învoiri (Î) și permisii (P),
                cursuri de pregătire (CP), misiuni în afara garnizoanei Câmpina (M), seminar (S), documentare (D),
                concediu pentru îngrijirea copilului (CIC), program redus (PR), zilele libere fără plată (L),
                deplasări în străinătate (DS), liber după serviciu (LS), recuperare (R) sau alte situații.
            </div>

            <!-- Signatures -->
            <div class="flex justify-between items-center px-10 mt-12">
                <!-- If we have both creator and approver, show both -->
                <template v-if="selectedCreator && signatures.approvalType === 'deacord' && selectedApprover">
                    <!-- INTOCMIT on the left -->
                    <div class="text-center">
                        <div class="font-bold text-base uppercase">Întocmit,</div>
                        <div class="mt-0 text-base italic">{{ selectedCreatorRank?.name || 'grad militar' }}</div>
                        <div class="font-bold text-base uppercase mt-4">
                            {{ selectedCreator?.full_name || 'Selectează persoana' }}
                        </div>
                    </div>

                    <!-- DE ACORD on the right -->
                    <div class="text-center">
                        <div class="font-bold text-base uppercase">De acord,</div>
                        <div class="font-bold text-base uppercase">
                            {{ signatures.jobTitle }}
                        </div>
                        <div class="mt-0 text-base italic">{{ selectedApproverRank?.name || 'grad militar' }}</div>
                        <div class="font-bold text-base uppercase mt-4">
                            {{ selectedApprover?.full_name }}
                        </div>
                    </div>
                </template>

                <!-- If we have creator but no approver OR approvalType is 'aprob' -->
                <template v-else-if="selectedCreator && (signatures.approvalType !== 'deacord' || !selectedApprover)">
                    <!-- Empty div on the left to maintain spacing -->
                    <div></div>

                    <!-- INTOCMIT moved to the right -->
                    <div class="text-center">
                        <div class="font-bold text-base uppercase">Întocmit,</div>
                        <div class="mt-0 text-base italic">{{ selectedCreatorRank?.name || 'grad militar' }}</div>
                        <div class="font-bold text-base uppercase mt-4">
                            {{ selectedCreator?.full_name || 'Selectează persoana' }}
                        </div>
                    </div>
                </template>

                <!-- If we have no creator -->
                <template v-else>
                    <div></div>
                    <div class="text-center">
                        <div class="font-bold text-base uppercase">Întocmit,</div>
                        <div class="font-medium mt-1">
                            Selectează persoana
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>

import { ref, computed, watch, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { PrinterIcon } from '@heroicons/vue/24/outline'

import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

import Header from '@/Components/shared/c-page-header.vue'
import SidebarMenu from '@/Components/partials/c-sidebar-menu.vue'
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'
import axios from 'axios'

const toast = useToast()

// Signature editing
const signatureEditorVisible = ref(false)
const showJobTitleError = ref(false)

const businessUnitEmployees = ref([])

const tempSignatures = ref({
    creatorId: null,
    approverId: null,
    approvalType: 'deacord',
    jobTitle: 'Șef birou',
})

const signatures = ref({
    creatorId: null,
    approverId: null,
    approvalType: 'deacord',
    jobTitle: 'Șef birou',
})

const editedObservations = ref([])
const observationsEdits = ref({})

const extractEventCode = (detail) => {
    if (!detail) return ''
    // Extract the code part (before the colon if exists)
    const colonIndex = detail.indexOf(':')
    if (colonIndex > -1) {
        return detail.substring(0, colonIndex).trim()
    }
    // For details with HTML (like R<sup>02</sup>), return as is
    return detail
}

const getEditedObservations = (person) => {
    if (!person) return null

    // Find the person's index
    const personIndex = reportData.value.people.findIndex(p => p === person)
    if (personIndex === -1) return null

    // Get observations using the index key
    const personKey = `observations_${personIndex}`
    return observationsEdits.value[personKey] || null
}

// Computed properties for signatures
const selectedCreator = computed(() =>
    signatures.value.creatorId ? businessUnitEmployees.value.find(e => e.id === signatures.value.creatorId) : null,
)

const selectedApprover = computed(() =>
    signatures.value.approverId ? businessUnitEmployees.value.find(e => e.id === signatures.value.approverId) : null,
)

const selectedCreatorRank = computed(() =>
    selectedCreator.value?.military_rank || null,
)

const selectedApproverRank = computed(() =>
    selectedApprover.value?.military_rank || null,
)

const fetchBusinessUnitEmployees = async () => {
    if (!selectedBusinessUnitGroup.value) {
        businessUnitEmployees.value = []
        return
    }

    try {
        const response = await axios.post('/employees/by-business-unit-group', {
            businessUnitGroupId: selectedBusinessUnitGroup.value.id,
        })

        if (response.data.result === 'RESULT_OK') {
            businessUnitEmployees.value = response.data.employees
        } else {
            toast.error('Eroare la încărcarea angajaților: ' + response.data.error)
        }
    } catch (error) {
        console.error('Error loading employees:', error)
        toast.error('Eroare la încărcarea angajaților')
    }
}

const openSignatureEditor = () => {
    // Load employees if needed
    if (businessUnitEmployees.value.length === 0) {
        fetchBusinessUnitEmployees()
    }

    // Load saved signatures from localStorage
    loadSignaturesFromStorage()

    // Create a deep copy of the current signatures for editing
    tempSignatures.value = JSON.parse(JSON.stringify(signatures.value))

    // Make sure the job title has a default value if empty
    if (!tempSignatures.value.jobTitle) {
        tempSignatures.value.jobTitle = 'Șef birou'
    }

    // Reset error state
    showJobTitleError.value = false

    signatureEditorVisible.value = true
}

const closeSignatureEditor = () => {
    // Reset error state
    showJobTitleError.value = false
    // Just close the dialog without saving changes
    signatureEditorVisible.value = false
    // No need to commit tempSignatures to signatures
}

const saveSignatureEdit = () => {
    // Reset error state
    showJobTitleError.value = false

    // Validate job title when approver is selected
    if (tempSignatures.value.approvalType === 'deacord' &&
        tempSignatures.value.approverId &&
        (!tempSignatures.value.jobTitle || tempSignatures.value.jobTitle.trim() === '')) {
        showJobTitleError.value = true
        return
    }

    // Copy the temporary values to the actual signatures object
    signatures.value = JSON.parse(JSON.stringify(tempSignatures.value))

    // Save to localStorage
    saveSignaturesToStorage()
    signatureEditorVisible.value = false

    toast.success('Semnături salvate cu succes!', {
        timeout: 2000,
        position: 'bottom-right',
    })
}

const saveSignaturesToStorage = () => {
    try {
        const storageKey = `monthly-report-signatures-${selectedBusinessUnitGroup.value?.id}-${date.value.getFullYear()}-${date.value.getMonth() + 1}`
        localStorage.setItem(storageKey, JSON.stringify(signatures.value))
    } catch (error) {
        console.error('Error saving signatures to localStorage:', error)
    }
}

const loadSignaturesFromStorage = () => {
    try {
        if (!selectedBusinessUnitGroup.value || !date.value) return

        const storageKey = `monthly-report-signatures-${selectedBusinessUnitGroup.value?.id}-${date.value.getFullYear()}-${date.value.getMonth() + 1}`
        const savedSignatures = localStorage.getItem(storageKey)

        if (savedSignatures) {
            const parsed = JSON.parse(savedSignatures)
            signatures.value = {
                ...signatures.value,
                ...parsed,
            }
        }
    } catch (error) {
        console.error('Error loading signatures from localStorage:', error)
    }
}

// Editing cells
const detailsEdits = ref({})
const detailsEditorVisible = ref(false)
const currentPerson = ref(null)
const editedDetails = ref([])

// State
const selectedBusinessUnitGroup = ref(null)
const date = ref(new Date())
const loading = ref(false)
const error = ref(null)
const reportData = ref({
    people: [],
    month_details: {
        working_days: 0,
        total_hours: 0,
    },
})

defineProps({
    businessUnitGroups: {
        type: Array,
        required: true,
    },
    people: {
        type: Array,
        required: true,
    },
})

// Editing cells
const currentPersonDetails = computed(() => {
    if (!currentPerson.value) return []
    return currentPerson.value.details || []
})

const openDetailsEditor = (person) => {
    currentPerson.value = person

    // Find the person's index
    const personIndex = reportData.value.people.findIndex(p => p === person)
    if (personIndex === -1) {
        toast.error('Nu s-a putut găsi persoana în lista de date.')
        return
    }

    // Use a different key format for observations
    const personKey = `observations_${personIndex}`

    // Initialize the edited observations array
    editedObservations.value = []

    // Get existing observations or initialize empty array
    const existingObservations = observationsEdits.value[personKey] || []

    // If we have previously saved observations, use those
    if (existingObservations.length > 0) {
        editedObservations.value = [...existingObservations]
    } else {
        // Otherwise initialize empty observations for each event
        editedObservations.value = Array(person.details?.length || 0).fill('')
    }

    // Show dialog
    detailsEditorVisible.value = true
}

const closeDetailsEditor = () => {
    detailsEditorVisible.value = false
    currentPerson.value = null
    editedDetails.value = []
}

const loadEditsFromStorage = () => {
    try {
        if (!selectedBusinessUnitGroup.value || !date.value) return

        const storageKey = `monthly-report-edits-${selectedBusinessUnitGroup.value?.id}-${date.value.getFullYear()}-${date.value.getMonth() + 1}`
        const savedEdits = localStorage.getItem(storageKey)

        if (savedEdits) {
            detailsEdits.value = JSON.parse(savedEdits)
        }
    } catch (error) {
        console.error('Error loading edits from localStorage:', error)
    }
}

watch(() => reportData.value.people, () => {
    if (reportData.value.people?.length > 0) {
        loadEditsFromStorage()
        loadObservationsFromStorage() // Add this line
        loadSignaturesFromStorage()
    }
}, { immediate: true })

const saveDetailsEdit = () => {
    try {
        if (!currentPerson.value) {
            toast.error('Nu s-a putut identifica persoana selectată.')
            return
        }

        // Find person's index
        const personIndex = reportData.value.people.findIndex(p => p === currentPerson.value)
        if (personIndex === -1) {
            toast.error('Nu s-a putut găsi persoana în lista de date.')
            return
        }

        // Use a different key for observations
        const personKey = `observations_${personIndex}`

        // Filter out any completely empty observations
        const filteredObservations = editedObservations.value
            .map(obs => obs.trim())

        // Save observations
        observationsEdits.value[personKey] = filteredObservations

        // Save to localStorage
        saveObservationsToStorage()

        toast.success('Observații salvate cu succes!', {
            timeout: 2000,
            position: 'bottom-right',
        })
    } catch (error) {
        console.error('Error saving observations:', error)
        toast.error('A apărut o eroare la salvarea observațiilor')
    } finally {
        closeDetailsEditor()
    }
}

const saveObservationsToStorage = async () => {
    try {
        if (!selectedBusinessUnitGroup.value?.id || !date.value) return

        // Continue to save in localStorage as a fallback
        const storageKey = `monthly-report-observations-${selectedBusinessUnitGroup.value?.id}-${date.value.getFullYear()}-${date.value.getMonth() + 1}`
        localStorage.setItem(storageKey, JSON.stringify(observationsEdits.value))

        // For each person with observations, save to database
        for (const [key, observations] of Object.entries(observationsEdits.value)) {
            if (!key.startsWith('observations_')) continue

            const personIndex = parseInt(key.replace('observations_', ''))

            // Save to database via API
            await axios.post('/temporary-observations/store', {
                business_unit_group_id: selectedBusinessUnitGroup.value.id,
                year: date.value.getFullYear(),
                month: date.value.getMonth() + 1,
                person_index: personIndex,
                observations,
            })
        }
    } catch (error) {
        console.error('Error saving observations to database:', error)
        toast.error('A apărut o eroare la salvarea observațiilor în baza de date')
    }
}

const loadObservationsFromStorage = async () => {
    try {
        if (!selectedBusinessUnitGroup.value?.id || !date.value) return

        const storageKey = `monthly-report-observations-${selectedBusinessUnitGroup.value?.id}-${date.value.getFullYear()}-${date.value.getMonth() + 1}`

        // Try to load from localStorage first (as fallback)
        const savedObservations = localStorage.getItem(storageKey)
        if (savedObservations) {
            observationsEdits.value = JSON.parse(savedObservations)
        }

        // Then try to load from database (will override localStorage values)
        try {
            const response = await axios.get('/temporary-observations/by-month', {
                params: {
                    business_unit_group_id: selectedBusinessUnitGroup.value.id,
                    year: date.value.getFullYear(),
                    month: date.value.getMonth() + 1,
                },
            })

            if (response.data.success && Object.keys(response.data.data).length > 0) {
                // Merge with existing data or replace completely
                observationsEdits.value = {
                    ...observationsEdits.value,
                    ...response.data.data,
                }
            }
        } catch (apiError) {
            console.error('Error loading observations from API:', apiError)
            // Continue with localStorage data if API fails
        }
    } catch (error) {
        console.error('Error loading observations:', error)
    }
}

// OTHER CONSTS
const getDayInitial = (day) => {
    const currentDate = new Date(date.value.getFullYear(), date.value.getMonth(), day)
    return currentDate.toLocaleDateString('ro-RO', { weekday: 'short' }).charAt(0)
}

const isWeekend = (day) => {
    const currentDate = new Date(date.value.getFullYear(), date.value.getMonth(), day)
    const dayOfWeek = currentDate.getDay()
    return dayOfWeek === 0 || dayOfWeek === 6
}

const formatHours = (hours) => {
    if (!hours || hours === 0) return ''
    return hours
}

const formatName = (name) => {
    if (!name) return ''
    return name
}

const printTable = () => {
    window.print()
}

const workingDaysInfo = ref({
    month: '',
    year: '',
    working_days: 0,
    working_hours: 0,
})

// Add or update the fetchWorkingDays function
const fetchWorkingDays = async () => {
    if (!date.value) return

    try {
        const year = date.value.getFullYear()
        const month = date.value.getMonth() + 1

        console.log(`Fetching working days for ${year}/${month}`)
        const response = await axios.get(`/working-days/${year}/${month}`)
        console.log('API response:', response.data)
        workingDaysInfo.value = response.data
    } catch (error) {
        console.error('Error fetching working days data:', error)

        // Set fallback default values if the API call fails
        workingDaysInfo.value = {
            month: date.value.toLocaleString('ro-RO', { month: 'long' }).toUpperCase(),
            year: date.value.getFullYear(),
            working_days: calculateDefaultWorkingDays(date.value),
            working_hours: calculateDefaultWorkingDays(date.value) * 8,
        }
    }
}

// Helper function to calculate working days based on weekdays
const calculateDefaultWorkingDays = (date) => {
    // Get year and month
    const year = date.getFullYear()
    const month = date.getMonth()

    // Get the last day of the month
    const lastDayOfMonth = new Date(year, month + 1, 0).getDate()

    let workingDays = 0

    // Loop through all days in the month using a for loop
    for (let day = 1; day <= lastDayOfMonth; day++) {
        const checkDate = new Date(year, month, day)

        // Check if day is a weekday (not Saturday or Sunday)
        if (checkDate.getDay() !== 0 && checkDate.getDay() !== 6) {
            workingDays++
        }
    }

    return workingDays
}

// Update the monthDetails computed property
const monthDetails = computed(() => ({
    daysInMonth: new Date(date.value.getFullYear(), date.value.getMonth() + 1, 0).getDate(),
    workingDays: workingDaysInfo.value.working_days || reportData.value?.month_details?.working_days || 0,
    totalHours: workingDaysInfo.value.working_hours || reportData.value?.month_details?.total_hours || 0,
}))

// Add a watch to fetch working days when the date changes
watch(date, () => {
    fetchWorkingDays()
}, { immediate: true })

// Add this to your script setup section, with the other functions
const hasMultipleLines = (person) => {
    // Consider a person has multiple lines if they have more than one detail
    return person.details && person.details.length > 1
}

// Methods
const fetchData = async () => {
    // Don't fetch if no business unit group is selected
    if (!selectedBusinessUnitGroup.value) {
        reportData.value = {
            people: [],
            month_details: {
                working_days: 0,
                total_hours: 0,
            },
        }
        return
    }

    try {
        loading.value = true
        error.value = null

        if (!date.value) {
            date.value = new Date()
        }

        const year = date.value.getFullYear()
        const month = date.value.getMonth() + 1

        const startOfMonth = `${year}-${month.toString().padStart(2, '0')}-01`
        const lastDay = new Date(year, month, 0).getDate()
        const endOfMonth = `${year}-${month.toString().padStart(2, '0')}-${lastDay}`

        const response = await axios.get('/rapoarte/prezenta-lunara/data', {
            params: {
                start_date: startOfMonth,
                end_date: endOfMonth,
                business_unit_group_id: selectedBusinessUnitGroup.value?.id,
            },
        })

        // Make sure to set the entire reportData structure
        reportData.value = {
            people: response.data.people || [],
            month_details: response.data.month_details || {
                working_days: 0,
                total_hours: 0,
            },
        }
    } catch (e) {
        error.value = 'A apărut o eroare la încărcarea datelor.'
        console.error('Error fetching data:', e)
    } finally {
        loading.value = false
    }
}

watch([date, selectedBusinessUnitGroup], () => {
    if (loading.value) return
    fetchData()
}, { deep: true })

watch(() => reportData.value.people, () => {
    if (reportData.value.people?.length > 0) {
        loadEditsFromStorage()
    }
}, { immediate: true })

watch(() => reportData.value.people, () => {
    if (reportData.value.people?.length > 0) {
        loadEditsFromStorage()
        loadSignaturesFromStorage() // Add this line
    }
}, { immediate: true })

// Add watch for business unit group changes
watch(() => selectedBusinessUnitGroup.value, () => {
    if (selectedBusinessUnitGroup.value) {
        fetchBusinessUnitEmployees()
    }
})

// Initial data fetch
onMounted(() => {
    fetchWorkingDays()
    if (!reportData.value.people?.length) {
        fetchData()
    }
})

</script>

<style scoped>
@media print {
    @page {
        size: landscape;
        margin: 1cm;
    }
}

.avoid-break {
    page-break-inside: avoid;
}

.edited-detail {
  color: #4338ca;
  font-weight: 500;
}

@media print {
  .edited-detail {
    color: black !important;
    font-weight: normal;
  }
}
</style>
