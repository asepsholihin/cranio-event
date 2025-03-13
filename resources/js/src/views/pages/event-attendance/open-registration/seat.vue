<template>
    <div>
        <b-card>
            <h3 class="text-default">{{ event.name }} - {{ formatDate(event.event_date) }}</h3>
            <h5>Total Pax: {{ event.pax_ikhwan + event.pax_akhwat }} Participant</h5>
            <h5>Ikhwan: {{ event.pax_ikhwan }} Participant</h5>
            <h5>Akhwat: {{ event.pax_akhwat }} Participant</h5>

            <b-row class="mt-1">
                <b-col cols="12" class="text-right">
                    <b-button class="mb-1 mr-1" variant="danger" @click="resetMappingSeat" v-if="hasPermission('event-attendance-add-or-edit')" :disabled="loadingSeat">
                        <span class="text-nowrap"><b-spinner small v-show="loadingSeat" /> Reset</span>
                    </b-button>
                    <b-button class="mb-1" variant="primary" @click="submitMappingSeat" v-if="hasPermission('event-attendance-add-or-edit')" :disabled="loadingSeat">
                        <span class="text-nowrap"><b-spinner small v-show="loadingSeat" /> Simpan</span>
                    </b-button>
                </b-col>
                <b-col cols="12" md="4">
                    <div class="wrapper">
                        <div class="card-body border-bottom">
                            <b-form-input v-model="queryFilter" @input="getParticipantList" type="search" placeholder="Cari participant..." />
                        </div>

                        <div class="wrap-participant">
                            <div class="text-center"><span v-if="loadingParticipantList">Loading...</span></div>
                            <ul class="list-group list-group-flush">
                                <template v-for="participant in participantList">
                                    <li class="list-group-item list-group-item-action" role="button" :class="{'active': participant.id == selectedParticipant.id}" @click="selectingParticipant(participant)">
                                        {{ participant.name }}<br>
                                        Pax: {{ participant.pax }}
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </b-col>
                <b-col cols="12" md="8">
                    <div class="wrapper p-2 position-relative">
                        <div v-if="loadingSeat" class="position-absolute d-flex justify-content-center align-items-center loading-box-spinner">
                            <b-spinner variant="primary" type="grow" label="Spinning" />
                        </div>
                        <!-- UV -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="4">
                                <div class="d-flex justify-content-start mb-1">
                                    <div class="box" :class="{ 'bg-success': box.v1.selected && box.v1.gender == 1, 'bg-danger': box.v1.selected && box.v1.gender == 2, 'disabled': !box.v1.active }" @click="checkingBox('v1', box.v1.selected)">{{ box.v1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.v2.selected && box.v2.gender == 1, 'bg-danger': box.v2.selected && box.v2.gender == 2, 'disabled': !box.v2.active }" @click="checkingBox('v2', box.v2.selected)">{{ box.v2.name }}</div>
                                </div>
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.u1.selected && box.u1.gender == 1, 'bg-danger': box.u1.selected && box.u1.gender == 2, 'disabled': !box.u1.active }" @click="checkingBox('u1', box.u1.selected)">{{ box.u1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.u2.selected && box.u2.gender == 1, 'bg-danger': box.u2.selected && box.u2.gender == 2, 'disabled': !box.u2.active }" @click="checkingBox('u2', box.u2.selected)">{{ box.u2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.u3.selected && box.u3.gender == 1, 'bg-danger': box.u3.selected && box.u3.gender == 2, 'disabled': !box.u3.active }" @click="checkingBox('u3', box.u3.selected)">{{ box.u3.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="4" class="text-center bg-dark text-white p-1 align-selft-center">Sound Control</b-col>
                            <b-col cols="4">
                                <div class="d-flex justify-content-end mb-1">
                                    <div class="box" :class="{ 'bg-success': box.v3.selected && box.v3.gender == 1, 'bg-danger': box.v3.selected && box.v3.gender == 2, 'disabled': !box.v3.active }" @click="checkingBox('v3', box.v3.selected)">{{ box.v3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.v4.selected && box.v4.gender == 1, 'bg-danger': box.v4.selected && box.v4.gender == 2, 'disabled': !box.v4.active }" @click="checkingBox('v4', box.v4.selected)">{{ box.v4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.v5.selected && box.v5.gender == 1, 'bg-danger': box.v5.selected && box.v5.gender == 2, 'disabled': !box.v5.active }" @click="checkingBox('v5', box.v5.selected)">{{ box.v5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.v6.selected && box.v6.gender == 1, 'bg-danger': box.v6.selected && box.v6.gender == 2, 'disabled': !box.v6.active }" @click="checkingBox('v6', box.v6.selected)">{{ box.v6.name }}</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.u4.selected && box.u4.gender == 1, 'bg-danger': box.u4.selected && box.u4.gender == 2, 'disabled': !box.u4.active }" @click="checkingBox('u4', box.u4.selected)">{{ box.u4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.u5.selected && box.u5.gender == 1, 'bg-danger': box.u5.selected && box.u5.gender == 2, 'disabled': !box.u5.active }" @click="checkingBox('u5', box.u5.selected)">{{ box.u5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.u6.selected && box.u6.gender == 1, 'bg-danger': box.u6.selected && box.u6.gender == 2, 'disabled': !box.u6.active }" @click="checkingBox('u6', box.u6.selected)">{{ box.u6.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- T -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="3">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.t1.selected && box.t1.gender == 1, 'bg-danger': box.t1.selected && box.t1.gender == 2, 'disabled': !box.t1.active }" @click="checkingBox('t1', box.t1.selected)">{{ box.t1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t2.selected && box.t2.gender == 1, 'bg-danger': box.t2.selected && box.t2.gender == 2, 'disabled': !box.t2.active }" @click="checkingBox('t2', box.t2.selected)">{{ box.t2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t3.selected && box.t3.gender == 1, 'bg-danger': box.t3.selected && box.t3.gender == 2, 'disabled': !box.t3.active }" @click="checkingBox('t3', box.t3.selected)">{{ box.t3.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-center">
                                    <div class="box" :class="{ 'bg-success': box.t4.selected && box.t4.gender == 1, 'bg-danger': box.t4.selected && box.t4.gender == 2, 'disabled': !box.t4.active }" @click="checkingBox('t4', box.t4.selected)">{{ box.t4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t5.selected && box.t5.gender == 1, 'bg-danger': box.t5.selected && box.t5.gender == 2, 'disabled': !box.t5.active }" @click="checkingBox('t5', box.t5.selected)">{{ box.t5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t6.selected && box.t6.gender == 1, 'bg-danger': box.t6.selected && box.t6.gender == 2, 'disabled': !box.t6.active }" @click="checkingBox('t6', box.t6.selected)">{{ box.t6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t7.selected && box.t7.gender == 1, 'bg-danger': box.t7.selected && box.t7.gender == 2, 'disabled': !box.t7.active }" @click="checkingBox('t7', box.t7.selected)">{{ box.t7.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t8.selected && box.t8.gender == 1, 'bg-danger': box.t8.selected && box.t8.gender == 2, 'disabled': !box.t8.active }" @click="checkingBox('t8', box.t8.selected)">{{ box.t8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t9.selected && box.t9.gender == 1, 'bg-danger': box.t9.selected && box.t9.gender == 2, 'disabled': !box.t9.active }" @click="checkingBox('t9', box.t9.selected)">{{ box.t9.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="3">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.t10.selected && box.t10.gender == 1, 'bg-danger': box.t10.selected && box.t10.gender == 2, 'disabled': !box.t10.active }" @click="checkingBox('t10', box.t10.selected)">{{ box.t10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t11.selected && box.t11.gender == 1, 'bg-danger': box.t11.selected && box.t11.gender == 2, 'disabled': !box.t11.active }" @click="checkingBox('t11', box.t11.selected)">{{ box.t11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.t12.selected && box.t12.gender == 1, 'bg-danger': box.t12.selected && box.t12.gender == 2, 'disabled': !box.t12.active }" @click="checkingBox('t12', box.t12.selected)">{{ box.t12.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- S -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.s1.selected && box.s1.gender == 1, 'bg-danger': box.s1.selected && box.s1.gender == 2, 'disabled': !box.s1.active }" @click="checkingBox('s1', box.s1.selected)">{{ box.s1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s2.selected && box.s2.gender == 1, 'bg-danger': box.s2.selected && box.s2.gender == 2, 'disabled': !box.s2.active }" @click="checkingBox('s2', box.s2.selected)">{{ box.s2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s3.selected && box.s3.gender == 1, 'bg-danger': box.s3.selected && box.s3.gender == 2, 'disabled': !box.s3.active }" @click="checkingBox('s3', box.s3.selected)">{{ box.s3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s4.selected && box.s4.gender == 1, 'bg-danger': box.s4.selected && box.s4.gender == 2, 'disabled': !box.s4.active }" @click="checkingBox('s4', box.s4.selected)">{{ box.s4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s5.selected && box.s5.gender == 1, 'bg-danger': box.s5.selected && box.s5.gender == 2, 'disabled': !box.s5.active }" @click="checkingBox('s5', box.s5.selected)">{{ box.s5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s6.selected && box.s6.gender == 1, 'bg-danger': box.s6.selected && box.s6.gender == 2, 'disabled': !box.s6.active }" @click="checkingBox('s6', box.s6.selected)">{{ box.s6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s7.selected && box.s7.gender == 1, 'bg-danger': box.s7.selected && box.s7.gender == 2, 'disabled': !box.s7.active }" @click="checkingBox('s7', box.s7.selected)">{{ box.s7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.s8.selected && box.s8.gender == 1, 'bg-danger': box.s8.selected && box.s8.gender == 2, 'disabled': !box.s8.active }" @click="checkingBox('s8', box.s8.selected)">{{ box.s8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s9.selected && box.s9.gender == 1, 'bg-danger': box.s9.selected && box.s9.gender == 2, 'disabled': !box.s9.active }" @click="checkingBox('s9', box.s9.selected)">{{ box.s9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s10.selected && box.s10.gender == 1, 'bg-danger': box.s10.selected && box.s10.gender == 2, 'disabled': !box.s10.active }" @click="checkingBox('s10', box.s10.selected)">{{ box.s10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s11.selected && box.s11.gender == 1, 'bg-danger': box.s11.selected && box.s11.gender == 2, 'disabled': !box.s11.active }" @click="checkingBox('s11', box.s11.selected)">{{ box.s11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s12.selected && box.s12.gender == 1, 'bg-danger': box.s12.selected && box.s12.gender == 2, 'disabled': !box.s12.active }" @click="checkingBox('s12', box.s12.selected)">{{ box.s12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s13.selected && box.s13.gender == 1, 'bg-danger': box.s13.selected && box.s13.gender == 2, 'disabled': !box.s13.active }" @click="checkingBox('s13', box.s13.selected)">{{ box.s13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.s14.selected && box.s14.gender == 1, 'bg-danger': box.s14.selected && box.s14.gender == 2, 'disabled': !box.s14.active }" @click="checkingBox('s14', box.s14.selected)">{{ box.s14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- R -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.r1.selected && box.r1.gender == 1, 'bg-danger': box.r1.selected && box.r1.gender == 2, 'disabled': !box.r1.active }" @click="checkingBox('r1', box.r1.selected)">{{ box.r1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r2.selected && box.r2.gender == 1, 'bg-danger': box.r2.selected && box.r2.gender == 2, 'disabled': !box.r2.active }" @click="checkingBox('r2', box.r2.selected)">{{ box.r2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r3.selected && box.r3.gender == 1, 'bg-danger': box.r3.selected && box.r3.gender == 2, 'disabled': !box.r3.active }" @click="checkingBox('r3', box.r3.selected)">{{ box.r3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r4.selected && box.r4.gender == 1, 'bg-danger': box.r4.selected && box.r4.gender == 2, 'disabled': !box.r4.active }" @click="checkingBox('r4', box.r4.selected)">{{ box.r4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r5.selected && box.r5.gender == 1, 'bg-danger': box.r5.selected && box.r5.gender == 2, 'disabled': !box.r5.active }" @click="checkingBox('r5', box.r5.selected)">{{ box.r5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r6.selected && box.r6.gender == 1, 'bg-danger': box.r6.selected && box.r6.gender == 2, 'disabled': !box.r6.active }" @click="checkingBox('r6', box.r6.selected)">{{ box.r6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r7.selected && box.r7.gender == 1, 'bg-danger': box.r7.selected && box.r7.gender == 2, 'disabled': !box.r7.active }" @click="checkingBox('r7', box.r7.selected)">{{ box.r7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.r8.selected && box.r8.gender == 1, 'bg-danger': box.r8.selected && box.r8.gender == 2, 'disabled': !box.r8.active }" @click="checkingBox('r8', box.r8.selected)">{{ box.r8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r9.selected && box.r9.gender == 1, 'bg-danger': box.r9.selected && box.r9.gender == 2, 'disabled': !box.r9.active }" @click="checkingBox('r9', box.r9.selected)">{{ box.r9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r10.selected && box.r10.gender == 1, 'bg-danger': box.r10.selected && box.r10.gender == 2, 'disabled': !box.r10.active }" @click="checkingBox('r10', box.r10.selected)">{{ box.r10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r11.selected && box.r11.gender == 1, 'bg-danger': box.r11.selected && box.r11.gender == 2, 'disabled': !box.r11.active }" @click="checkingBox('r11', box.r11.selected)">{{ box.r11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r12.selected && box.r12.gender == 1, 'bg-danger': box.r12.selected && box.r12.gender == 2, 'disabled': !box.r12.active }" @click="checkingBox('r12', box.r12.selected)">{{ box.r12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r13.selected && box.r13.gender == 1, 'bg-danger': box.r13.selected && box.r13.gender == 2, 'disabled': !box.r13.active }" @click="checkingBox('r13', box.r13.selected)">{{ box.r13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.r14.selected && box.r14.gender == 1, 'bg-danger': box.r14.selected && box.r14.gender == 2, 'disabled': !box.r14.active }" @click="checkingBox('r14', box.r14.selected)">{{ box.r14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- Q -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.q1.selected && box.q1.gender == 1, 'bg-danger': box.q1.selected && box.q1.gender == 2, 'disabled': !box.q1.active }" @click="checkingBox('q1', box.q1.selected)">{{ box.q1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q2.selected && box.q2.gender == 1, 'bg-danger': box.q2.selected && box.q2.gender == 2, 'disabled': !box.q2.active }" @click="checkingBox('q2', box.q2.selected)">{{ box.q2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q3.selected && box.q3.gender == 1, 'bg-danger': box.q3.selected && box.q3.gender == 2, 'disabled': !box.q3.active }" @click="checkingBox('q3', box.q3.selected)">{{ box.q3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q4.selected && box.q4.gender == 1, 'bg-danger': box.q4.selected && box.q4.gender == 2, 'disabled': !box.q4.active }" @click="checkingBox('q4', box.q4.selected)">{{ box.q4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q5.selected && box.q5.gender == 1, 'bg-danger': box.q5.selected && box.q5.gender == 2, 'disabled': !box.q5.active }" @click="checkingBox('q5', box.q5.selected)">{{ box.q5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q6.selected && box.q6.gender == 1, 'bg-danger': box.q6.selected && box.q6.gender == 2, 'disabled': !box.q6.active }" @click="checkingBox('q6', box.q6.selected)">{{ box.q6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q7.selected && box.q7.gender == 1, 'bg-danger': box.q7.selected && box.q7.gender == 2, 'disabled': !box.q7.active }" @click="checkingBox('q7', box.q7.selected)">{{ box.q7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.q8.selected && box.q8.gender == 1, 'bg-danger': box.q8.selected && box.q8.gender == 2, 'disabled': !box.q8.active }" @click="checkingBox('q8', box.q8.selected)">{{ box.q8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q9.selected && box.q9.gender == 1, 'bg-danger': box.q9.selected && box.q9.gender == 2, 'disabled': !box.q9.active }" @click="checkingBox('q9', box.q9.selected)">{{ box.q9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q10.selected && box.q10.gender == 1, 'bg-danger': box.q10.selected && box.q10.gender == 2, 'disabled': !box.q10.active }" @click="checkingBox('q10', box.q10.selected)">{{ box.q10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q11.selected && box.q11.gender == 1, 'bg-danger': box.q11.selected && box.q11.gender == 2, 'disabled': !box.q11.active }" @click="checkingBox('q11', box.q11.selected)">{{ box.q11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q12.selected && box.q12.gender == 1, 'bg-danger': box.q12.selected && box.q12.gender == 2, 'disabled': !box.q12.active }" @click="checkingBox('q12', box.q12.selected)">{{ box.q12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q13.selected && box.q13.gender == 1, 'bg-danger': box.q13.selected && box.q13.gender == 2, 'disabled': !box.q13.active }" @click="checkingBox('q13', box.q13.selected)">{{ box.q13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.q14.selected && box.q14.gender == 1, 'bg-danger': box.q14.selected && box.q14.gender == 2, 'disabled': !box.q14.active }" @click="checkingBox('q14', box.q14.selected)">{{ box.q14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- P -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.p1.selected && box.p1.gender == 1, 'bg-danger': box.p1.selected && box.p1.gender == 2, 'disabled': !box.p1.active }" @click="checkingBox('p1', box.p1.selected)">{{ box.p1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p2.selected && box.p2.gender == 1, 'bg-danger': box.p2.selected && box.p2.gender == 2, 'disabled': !box.p2.active }" @click="checkingBox('p2', box.p2.selected)">{{ box.p2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p3.selected && box.p3.gender == 1, 'bg-danger': box.p3.selected && box.p3.gender == 2, 'disabled': !box.p3.active }" @click="checkingBox('p3', box.p3.selected)">{{ box.p3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p4.selected && box.p4.gender == 1, 'bg-danger': box.p4.selected && box.p4.gender == 2, 'disabled': !box.p4.active }" @click="checkingBox('p4', box.p4.selected)">{{ box.p4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p5.selected && box.p5.gender == 1, 'bg-danger': box.p5.selected && box.p5.gender == 2, 'disabled': !box.p5.active }" @click="checkingBox('p5', box.p5.selected)">{{ box.p5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p6.selected && box.p6.gender == 1, 'bg-danger': box.p6.selected && box.p6.gender == 2, 'disabled': !box.p6.active }" @click="checkingBox('p6', box.p6.selected)">{{ box.p6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p7.selected && box.p7.gender == 1, 'bg-danger': box.p7.selected && box.p7.gender == 2, 'disabled': !box.p7.active }" @click="checkingBox('p7', box.p7.selected)">{{ box.p7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.p8.selected && box.p8.gender == 1, 'bg-danger': box.p8.selected && box.p8.gender == 2, 'disabled': !box.p8.active }" @click="checkingBox('p8', box.p8.selected)">{{ box.p8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p9.selected && box.p9.gender == 1, 'bg-danger': box.p9.selected && box.p9.gender == 2, 'disabled': !box.p9.active }" @click="checkingBox('p9', box.p9.selected)">{{ box.p9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p10.selected && box.p10.gender == 1, 'bg-danger': box.p10.selected && box.p10.gender == 2, 'disabled': !box.p10.active }" @click="checkingBox('p10', box.p10.selected)">{{ box.p10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p11.selected && box.p11.gender == 1, 'bg-danger': box.p11.selected && box.p11.gender == 2, 'disabled': !box.p11.active }" @click="checkingBox('p11', box.p11.selected)">{{ box.p11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p12.selected && box.p12.gender == 1, 'bg-danger': box.p12.selected && box.p12.gender == 2, 'disabled': !box.p12.active }" @click="checkingBox('p12', box.p12.selected)">{{ box.p12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p13.selected && box.p13.gender == 1, 'bg-danger': box.p13.selected && box.p13.gender == 2, 'disabled': !box.p13.active }" @click="checkingBox('p13', box.p13.selected)">{{ box.p13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.p14.selected && box.p14.gender == 1, 'bg-danger': box.p14.selected && box.p14.gender == 2, 'disabled': !box.p14.active }" @click="checkingBox('p14', box.p14.selected)">{{ box.p14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <hr class="my-3">

                        <!-- O -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.o1.selected && box.o1.gender == 1, 'bg-danger': box.o1.selected && box.o1.gender == 2, 'disabled': !box.o1.active }" @click="checkingBox('o1', box.o1.selected)">{{ box.o1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o2.selected && box.o2.gender == 1, 'bg-danger': box.o2.selected && box.o2.gender == 2, 'disabled': !box.o2.active }" @click="checkingBox('o2', box.o2.selected)">{{ box.o2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o3.selected && box.o3.gender == 1, 'bg-danger': box.o3.selected && box.o3.gender == 2, 'disabled': !box.o3.active }" @click="checkingBox('o3', box.o3.selected)">{{ box.o3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o4.selected && box.o4.gender == 1, 'bg-danger': box.o4.selected && box.o4.gender == 2, 'disabled': !box.o4.active }" @click="checkingBox('o4', box.o4.selected)">{{ box.o4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o5.selected && box.o5.gender == 1, 'bg-danger': box.o5.selected && box.o5.gender == 2, 'disabled': !box.o5.active }" @click="checkingBox('o5', box.o5.selected)">{{ box.o5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o6.selected && box.o6.gender == 1, 'bg-danger': box.o6.selected && box.o6.gender == 2, 'disabled': !box.o6.active }" @click="checkingBox('o6', box.o6.selected)">{{ box.o6.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.o7.selected && box.o7.gender == 1, 'bg-danger': box.o7.selected && box.o7.gender == 2, 'disabled': !box.o7.active }" @click="checkingBox('o7', box.o7.selected)">{{ box.o7.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o8.selected && box.o8.gender == 1, 'bg-danger': box.o8.selected && box.o8.gender == 2, 'disabled': !box.o8.active }" @click="checkingBox('o8', box.o8.selected)">{{ box.o8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o9.selected && box.o9.gender == 1, 'bg-danger': box.o9.selected && box.o9.gender == 2, 'disabled': !box.o9.active }" @click="checkingBox('o9', box.o9.selected)">{{ box.o9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o10.selected && box.o10.gender == 1, 'bg-danger': box.o10.selected && box.o10.gender == 2, 'disabled': !box.o10.active }" @click="checkingBox('o10', box.o10.selected)">{{ box.o10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o11.selected && box.o11.gender == 1, 'bg-danger': box.o11.selected && box.o11.gender == 2, 'disabled': !box.o11.active }" @click="checkingBox('o11', box.o11.selected)">{{ box.o11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.o12.selected && box.o12.gender == 1, 'bg-danger': box.o12.selected && box.o12.gender == 2, 'disabled': !box.o12.active }" @click="checkingBox('o12', box.o12.selected)">{{ box.o12.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- N -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.n1.selected && box.n1.gender == 1, 'bg-danger': box.n1.selected && box.n1.gender == 2, 'disabled': !box.n1.active }" @click="checkingBox('n1', box.n1.selected)">{{ box.n1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n2.selected && box.n2.gender == 1, 'bg-danger': box.n2.selected && box.n2.gender == 2, 'disabled': !box.n2.active }" @click="checkingBox('n2', box.n2.selected)">{{ box.n2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n3.selected && box.n3.gender == 1, 'bg-danger': box.n3.selected && box.n3.gender == 2, 'disabled': !box.n3.active }" @click="checkingBox('n3', box.n3.selected)">{{ box.n3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n4.selected && box.n4.gender == 1, 'bg-danger': box.n4.selected && box.n4.gender == 2, 'disabled': !box.n4.active }" @click="checkingBox('n4', box.n4.selected)">{{ box.n4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n5.selected && box.n5.gender == 1, 'bg-danger': box.n5.selected && box.n5.gender == 2, 'disabled': !box.n5.active }" @click="checkingBox('n5', box.n5.selected)">{{ box.n5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n6.selected && box.n6.gender == 1, 'bg-danger': box.n6.selected && box.n6.gender == 2, 'disabled': !box.n6.active }" @click="checkingBox('n6', box.n6.selected)">{{ box.n6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n7.selected && box.n7.gender == 1, 'bg-danger': box.n7.selected && box.n7.gender == 2, 'disabled': !box.n7.active }" @click="checkingBox('n7', box.n7.selected)">{{ box.n7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.n8.selected && box.n8.gender == 1, 'bg-danger': box.n8.selected && box.n8.gender == 2, 'disabled': !box.n8.active }" @click="checkingBox('n8', box.n8.selected)">{{ box.n8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n9.selected && box.n9.gender == 1, 'bg-danger': box.n9.selected && box.n9.gender == 2, 'disabled': !box.n9.active }" @click="checkingBox('n9', box.n9.selected)">{{ box.n9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n10.selected && box.n10.gender == 1, 'bg-danger': box.n10.selected && box.n10.gender == 2, 'disabled': !box.n10.active }" @click="checkingBox('n10', box.n10.selected)">{{ box.n10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n11.selected && box.n11.gender == 1, 'bg-danger': box.n11.selected && box.n11.gender == 2, 'disabled': !box.n11.active }" @click="checkingBox('n11', box.n11.selected)">{{ box.n11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n12.selected && box.n12.gender == 1, 'bg-danger': box.n12.selected && box.n12.gender == 2, 'disabled': !box.n12.active }" @click="checkingBox('n12', box.n12.selected)">{{ box.n12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n13.selected && box.n13.gender == 1, 'bg-danger': box.n13.selected && box.n13.gender == 2, 'disabled': !box.n13.active }" @click="checkingBox('n13', box.n13.selected)">{{ box.n13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.n14.selected && box.n14.gender == 1, 'bg-danger': box.n14.selected && box.n14.gender == 2, 'disabled': !box.n14.active }" @click="checkingBox('n14', box.n14.selected)">{{ box.n14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- M -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.m1.selected && box.m1.gender == 1, 'bg-danger': box.m1.selected && box.m1.gender == 2, 'disabled': !box.m1.active }" @click="checkingBox('m1', box.m1.selected)">{{ box.m1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m2.selected && box.m2.gender == 1, 'bg-danger': box.m2.selected && box.m2.gender == 2, 'disabled': !box.m2.active }" @click="checkingBox('m2', box.m2.selected)">{{ box.m2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m3.selected && box.m3.gender == 1, 'bg-danger': box.m3.selected && box.m3.gender == 2, 'disabled': !box.m3.active }" @click="checkingBox('m3', box.m3.selected)">{{ box.m3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m4.selected && box.m4.gender == 1, 'bg-danger': box.m4.selected && box.m4.gender == 2, 'disabled': !box.m4.active }" @click="checkingBox('m4', box.m4.selected)">{{ box.m4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m5.selected && box.m5.gender == 1, 'bg-danger': box.m5.selected && box.m5.gender == 2, 'disabled': !box.m5.active }" @click="checkingBox('m5', box.m5.selected)">{{ box.m5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m6.selected && box.m6.gender == 1, 'bg-danger': box.m6.selected && box.m6.gender == 2, 'disabled': !box.m6.active }" @click="checkingBox('m6', box.m6.selected)">{{ box.m6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m7.selected && box.m7.gender == 1, 'bg-danger': box.m7.selected && box.m7.gender == 2, 'disabled': !box.m7.active }" @click="checkingBox('m7', box.m7.selected)">{{ box.m7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.m8.selected && box.m8.gender == 1, 'bg-danger': box.m8.selected && box.m8.gender == 2, 'disabled': !box.m8.active }" @click="checkingBox('m8', box.m8.selected)">{{ box.m8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m9.selected && box.m9.gender == 1, 'bg-danger': box.m9.selected && box.m9.gender == 2, 'disabled': !box.m9.active }" @click="checkingBox('m9', box.m9.selected)">{{ box.m9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m10.selected && box.m10.gender == 1, 'bg-danger': box.m10.selected && box.m10.gender == 2, 'disabled': !box.m10.active }" @click="checkingBox('m10', box.m10.selected)">{{ box.m10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m11.selected && box.m11.gender == 1, 'bg-danger': box.m11.selected && box.m11.gender == 2, 'disabled': !box.m11.active }" @click="checkingBox('m11', box.m11.selected)">{{ box.m11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m12.selected && box.m12.gender == 1, 'bg-danger': box.m12.selected && box.m12.gender == 2, 'disabled': !box.m12.active }" @click="checkingBox('m12', box.m12.selected)">{{ box.m12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m13.selected && box.m13.gender == 1, 'bg-danger': box.m13.selected && box.m13.gender == 2, 'disabled': !box.m13.active }" @click="checkingBox('m13', box.m13.selected)">{{ box.m13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.m14.selected && box.m14.gender == 1, 'bg-danger': box.m14.selected && box.m14.gender == 2, 'disabled': !box.m14.active }" @click="checkingBox('m14', box.m14.selected)">{{ box.m14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- L -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.l1.selected && box.l1.gender == 1, 'bg-danger': box.l1.selected && box.l1.gender == 2, 'disabled': !box.l1.active }" @click="checkingBox('l1', box.l1.selected)">{{ box.l1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l2.selected && box.l2.gender == 1, 'bg-danger': box.l2.selected && box.l2.gender == 2, 'disabled': !box.l2.active }" @click="checkingBox('l2', box.l2.selected)">{{ box.l2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l3.selected && box.l3.gender == 1, 'bg-danger': box.l3.selected && box.l3.gender == 2, 'disabled': !box.l3.active }" @click="checkingBox('l3', box.l3.selected)">{{ box.l3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l4.selected && box.l4.gender == 1, 'bg-danger': box.l4.selected && box.l4.gender == 2, 'disabled': !box.l4.active }" @click="checkingBox('l4', box.l4.selected)">{{ box.l4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l5.selected && box.l5.gender == 1, 'bg-danger': box.l5.selected && box.l5.gender == 2, 'disabled': !box.l5.active }" @click="checkingBox('l5', box.l5.selected)">{{ box.l5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l6.selected && box.l6.gender == 1, 'bg-danger': box.l6.selected && box.l6.gender == 2, 'disabled': !box.l6.active }" @click="checkingBox('l6', box.l6.selected)">{{ box.l6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l7.selected && box.l7.gender == 1, 'bg-danger': box.l7.selected && box.l7.gender == 2, 'disabled': !box.l7.active }" @click="checkingBox('l7', box.l7.selected)">{{ box.l7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.l8.selected && box.l8.gender == 1, 'bg-danger': box.l8.selected && box.l8.gender == 2, 'disabled': !box.l8.active }" @click="checkingBox('l8', box.l8.selected)">{{ box.l8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l9.selected && box.l9.gender == 1, 'bg-danger': box.l9.selected && box.l9.gender == 2, 'disabled': !box.l9.active }" @click="checkingBox('l9', box.l9.selected)">{{ box.l9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l10.selected && box.l10.gender == 1, 'bg-danger': box.l10.selected && box.l10.gender == 2, 'disabled': !box.l10.active }" @click="checkingBox('l10', box.l10.selected)">{{ box.l10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l11.selected && box.l11.gender == 1, 'bg-danger': box.l11.selected && box.l11.gender == 2, 'disabled': !box.l11.active }" @click="checkingBox('l11', box.l11.selected)">{{ box.l11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l12.selected && box.l12.gender == 1, 'bg-danger': box.l12.selected && box.l12.gender == 2, 'disabled': !box.l12.active }" @click="checkingBox('l12', box.l12.selected)">{{ box.l12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l13.selected && box.l13.gender == 1, 'bg-danger': box.l13.selected && box.l13.gender == 2, 'disabled': !box.l13.active }" @click="checkingBox('l13', box.l13.selected)">{{ box.l13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.l14.selected && box.l14.gender == 1, 'bg-danger': box.l14.selected && box.l14.gender == 2, 'disabled': !box.l14.active }" @click="checkingBox('l14', box.l14.selected)">{{ box.l14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>
                        
                        <!-- K -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.k1.selected && box.k1.gender == 1, 'bg-danger': box.k1.selected && box.k1.gender == 2, 'disabled': !box.k1.active }" @click="checkingBox('k1', box.k1.selected)">{{ box.k1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k2.selected && box.k2.gender == 1, 'bg-danger': box.k2.selected && box.k2.gender == 2, 'disabled': !box.k2.active }" @click="checkingBox('k2', box.k2.selected)">{{ box.k2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k3.selected && box.k3.gender == 1, 'bg-danger': box.k3.selected && box.k3.gender == 2, 'disabled': !box.k3.active }" @click="checkingBox('k3', box.k3.selected)">{{ box.k3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k4.selected && box.k4.gender == 1, 'bg-danger': box.k4.selected && box.k4.gender == 2, 'disabled': !box.k4.active }" @click="checkingBox('k4', box.k4.selected)">{{ box.k4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k5.selected && box.k5.gender == 1, 'bg-danger': box.k5.selected && box.k5.gender == 2, 'disabled': !box.k5.active }" @click="checkingBox('k5', box.k5.selected)">{{ box.k5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k6.selected && box.k6.gender == 1, 'bg-danger': box.k6.selected && box.k6.gender == 2, 'disabled': !box.k6.active }" @click="checkingBox('k6', box.k6.selected)">{{ box.k6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k7.selected && box.k7.gender == 1, 'bg-danger': box.k7.selected && box.k7.gender == 2, 'disabled': !box.k7.active }" @click="checkingBox('k7', box.k7.selected)">{{ box.k7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.k8.selected && box.k8.gender == 1, 'bg-danger': box.k8.selected && box.k8.gender == 2, 'disabled': !box.k8.active }" @click="checkingBox('k8', box.k8.selected)">{{ box.k8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k9.selected && box.k9.gender == 1, 'bg-danger': box.k9.selected && box.k9.gender == 2, 'disabled': !box.k9.active }" @click="checkingBox('k9', box.k9.selected)">{{ box.k9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k10.selected && box.k10.gender == 1, 'bg-danger': box.k10.selected && box.k10.gender == 2, 'disabled': !box.k10.active }" @click="checkingBox('k10', box.k10.selected)">{{ box.k10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k11.selected && box.k11.gender == 1, 'bg-danger': box.k11.selected && box.k11.gender == 2, 'disabled': !box.k11.active }" @click="checkingBox('k11', box.k11.selected)">{{ box.k11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k12.selected && box.k12.gender == 1, 'bg-danger': box.k12.selected && box.k12.gender == 2, 'disabled': !box.k12.active }" @click="checkingBox('k12', box.k12.selected)">{{ box.k12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k13.selected && box.k13.gender == 1, 'bg-danger': box.k13.selected && box.k13.gender == 2, 'disabled': !box.k13.active }" @click="checkingBox('k13', box.k13.selected)">{{ box.k13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.k14.selected && box.k14.gender == 1, 'bg-danger': box.k14.selected && box.k14.gender == 2, 'disabled': !box.k14.active }" @click="checkingBox('k14', box.k14.selected)">{{ box.k14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>
                        
                        <!-- J -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.j1.selected && box.j1.gender == 1, 'bg-danger': box.j1.selected && box.j1.gender == 2, 'disabled': !box.j1.active }" @click="checkingBox('j1', box.j1.selected)">{{ box.j1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j2.selected && box.j2.gender == 1, 'bg-danger': box.j2.selected && box.j2.gender == 2, 'disabled': !box.j2.active }" @click="checkingBox('j2', box.j2.selected)">{{ box.j2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j3.selected && box.j3.gender == 1, 'bg-danger': box.j3.selected && box.j3.gender == 2, 'disabled': !box.j3.active }" @click="checkingBox('j3', box.j3.selected)">{{ box.j3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j4.selected && box.j4.gender == 1, 'bg-danger': box.j4.selected && box.j4.gender == 2, 'disabled': !box.j4.active }" @click="checkingBox('j4', box.j4.selected)">{{ box.j4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j5.selected && box.j5.gender == 1, 'bg-danger': box.j5.selected && box.j5.gender == 2, 'disabled': !box.j5.active }" @click="checkingBox('j5', box.j5.selected)">{{ box.j5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j6.selected && box.j6.gender == 1, 'bg-danger': box.j6.selected && box.j6.gender == 2, 'disabled': !box.j6.active }" @click="checkingBox('j6', box.j6.selected)">{{ box.j6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j7.selected && box.j7.gender == 1, 'bg-danger': box.j7.selected && box.j7.gender == 2, 'disabled': !box.j7.active }" @click="checkingBox('j7', box.j7.selected)">{{ box.j7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.j8.selected && box.j8.gender == 1, 'bg-danger': box.j8.selected && box.j8.gender == 2, 'disabled': !box.j8.active }" @click="checkingBox('j8', box.j8.selected)">{{ box.j8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j9.selected && box.j9.gender == 1, 'bg-danger': box.j9.selected && box.j9.gender == 2, 'disabled': !box.j9.active }" @click="checkingBox('j9', box.j9.selected)">{{ box.j9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j10.selected && box.j10.gender == 1, 'bg-danger': box.j10.selected && box.j10.gender == 2, 'disabled': !box.j10.active }" @click="checkingBox('j10', box.j10.selected)">{{ box.j10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j11.selected && box.j11.gender == 1, 'bg-danger': box.j11.selected && box.j11.gender == 2, 'disabled': !box.j11.active }" @click="checkingBox('j11', box.j11.selected)">{{ box.j11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j12.selected && box.j12.gender == 1, 'bg-danger': box.j12.selected && box.j12.gender == 2, 'disabled': !box.j12.active }" @click="checkingBox('j12', box.j12.selected)">{{ box.j12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j13.selected && box.j13.gender == 1, 'bg-danger': box.j13.selected && box.j13.gender == 2, 'disabled': !box.j13.active }" @click="checkingBox('j13', box.j13.selected)">{{ box.j13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.j14.selected && box.j14.gender == 1, 'bg-danger': box.j14.selected && box.j14.gender == 2, 'disabled': !box.j14.active }" @click="checkingBox('j14', box.j14.selected)">{{ box.j14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>
                        
                        <!-- I -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.i1.selected && box.i1.gender == 1, 'bg-danger': box.i1.selected && box.i1.gender == 2, 'disabled': !box.i1.active }" @click="checkingBox('i1', box.i1.selected)">{{ box.i1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i2.selected && box.i2.gender == 1, 'bg-danger': box.i2.selected && box.i2.gender == 2, 'disabled': !box.i2.active }" @click="checkingBox('i2', box.i2.selected)">{{ box.i2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i3.selected && box.i3.gender == 1, 'bg-danger': box.i3.selected && box.i3.gender == 2, 'disabled': !box.i3.active }" @click="checkingBox('i3', box.i3.selected)">{{ box.i3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i4.selected && box.i4.gender == 1, 'bg-danger': box.i4.selected && box.i4.gender == 2, 'disabled': !box.i4.active }" @click="checkingBox('i4', box.i4.selected)">{{ box.i4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i5.selected && box.i5.gender == 1, 'bg-danger': box.i5.selected && box.i5.gender == 2, 'disabled': !box.i5.active }" @click="checkingBox('i5', box.i5.selected)">{{ box.i5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i6.selected && box.i6.gender == 1, 'bg-danger': box.i6.selected && box.i6.gender == 2, 'disabled': !box.i6.active }" @click="checkingBox('i6', box.i6.selected)">{{ box.i6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i7.selected && box.i7.gender == 1, 'bg-danger': box.i7.selected && box.i7.gender == 2, 'disabled': !box.i7.active }" @click="checkingBox('i7', box.i7.selected)">{{ box.i7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.i8.selected && box.i8.gender == 1, 'bg-danger': box.i8.selected && box.i8.gender == 2, 'disabled': !box.i8.active }" @click="checkingBox('i8', box.i8.selected)">{{ box.i8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i9.selected && box.i9.gender == 1, 'bg-danger': box.i9.selected && box.i9.gender == 2, 'disabled': !box.i9.active }" @click="checkingBox('i9', box.i9.selected)">{{ box.i9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i10.selected && box.i10.gender == 1, 'bg-danger': box.i10.selected && box.i10.gender == 2, 'disabled': !box.i10.active }" @click="checkingBox('i10', box.i10.selected)">{{ box.i10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i11.selected && box.i11.gender == 1, 'bg-danger': box.i11.selected && box.i11.gender == 2, 'disabled': !box.i11.active }" @click="checkingBox('i11', box.i11.selected)">{{ box.i11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i12.selected && box.i12.gender == 1, 'bg-danger': box.i12.selected && box.i12.gender == 2, 'disabled': !box.i12.active }" @click="checkingBox('i12', box.i12.selected)">{{ box.i12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i13.selected && box.i13.gender == 1, 'bg-danger': box.i13.selected && box.i13.gender == 2, 'disabled': !box.i13.active }" @click="checkingBox('i13', box.i13.selected)">{{ box.i13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.i14.selected && box.i14.gender == 1, 'bg-danger': box.i14.selected && box.i14.gender == 2, 'disabled': !box.i14.active }" @click="checkingBox('i14', box.i14.selected)">{{ box.i14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- H -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.h1.selected && box.h1.gender == 1, 'bg-danger': box.h1.selected && box.h1.gender == 2, 'disabled': !box.h1.active }" @click="checkingBox('h1', box.h1.selected)">{{ box.h1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h2.selected && box.h2.gender == 1, 'bg-danger': box.h2.selected && box.h2.gender == 2, 'disabled': !box.h2.active }" @click="checkingBox('h2', box.h2.selected)">{{ box.h2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h3.selected && box.h3.gender == 1, 'bg-danger': box.h3.selected && box.h3.gender == 2, 'disabled': !box.h3.active }" @click="checkingBox('h3', box.h3.selected)">{{ box.h3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h4.selected && box.h4.gender == 1, 'bg-danger': box.h4.selected && box.h4.gender == 2, 'disabled': !box.h4.active }" @click="checkingBox('h4', box.h4.selected)">{{ box.h4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h5.selected && box.h5.gender == 1, 'bg-danger': box.h5.selected && box.h5.gender == 2, 'disabled': !box.h5.active }" @click="checkingBox('h5', box.h5.selected)">{{ box.h5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h6.selected && box.h6.gender == 1, 'bg-danger': box.h6.selected && box.h6.gender == 2, 'disabled': !box.h6.active }" @click="checkingBox('h6', box.h6.selected)">{{ box.h6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h7.selected && box.h7.gender == 1, 'bg-danger': box.h7.selected && box.h7.gender == 2, 'disabled': !box.h7.active }" @click="checkingBox('h7', box.h7.selected)">{{ box.h7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.h8.selected && box.h8.gender == 1, 'bg-danger': box.h8.selected && box.h8.gender == 2, 'disabled': !box.h8.active }" @click="checkingBox('h8', box.h8.selected)">{{ box.h8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h9.selected && box.h9.gender == 1, 'bg-danger': box.h9.selected && box.h9.gender == 2, 'disabled': !box.h9.active }" @click="checkingBox('h9', box.h9.selected)">{{ box.h9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h10.selected && box.h10.gender == 1, 'bg-danger': box.h10.selected && box.h10.gender == 2, 'disabled': !box.h10.active }" @click="checkingBox('h10', box.h10.selected)">{{ box.h10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h11.selected && box.h11.gender == 1, 'bg-danger': box.h11.selected && box.h11.gender == 2, 'disabled': !box.h11.active }" @click="checkingBox('h11', box.h11.selected)">{{ box.h11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h12.selected && box.h12.gender == 1, 'bg-danger': box.h12.selected && box.h12.gender == 2, 'disabled': !box.h12.active }" @click="checkingBox('h12', box.h12.selected)">{{ box.h12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h13.selected && box.h13.gender == 1, 'bg-danger': box.h13.selected && box.h13.gender == 2, 'disabled': !box.h13.active }" @click="checkingBox('h13', box.h13.selected)">{{ box.h13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.h14.selected && box.h14.gender == 1, 'bg-danger': box.h14.selected && box.h14.gender == 2, 'disabled': !box.h14.active }" @click="checkingBox('h14', box.h14.selected)">{{ box.h14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- G -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.g1.selected && box.g1.gender == 1, 'bg-danger': box.g1.selected && box.g1.gender == 2, 'disabled': !box.g1.active }" @click="checkingBox('g1', box.g1.selected)">{{ box.g1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g2.selected && box.g2.gender == 1, 'bg-danger': box.g2.selected && box.g2.gender == 2, 'disabled': !box.g2.active }" @click="checkingBox('g2', box.g2.selected)">{{ box.g2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g3.selected && box.g3.gender == 1, 'bg-danger': box.g3.selected && box.g3.gender == 2, 'disabled': !box.g3.active }" @click="checkingBox('g3', box.g3.selected)">{{ box.g3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g4.selected && box.g4.gender == 1, 'bg-danger': box.g4.selected && box.g4.gender == 2, 'disabled': !box.g4.active }" @click="checkingBox('g4', box.g4.selected)">{{ box.g4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g5.selected && box.g5.gender == 1, 'bg-danger': box.g5.selected && box.g5.gender == 2, 'disabled': !box.g5.active }" @click="checkingBox('g5', box.g5.selected)">{{ box.g5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g6.selected && box.g6.gender == 1, 'bg-danger': box.g6.selected && box.g6.gender == 2, 'disabled': !box.g6.active }" @click="checkingBox('g6', box.g6.selected)">{{ box.g6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g7.selected && box.g7.gender == 1, 'bg-danger': box.g7.selected && box.g7.gender == 2, 'disabled': !box.g7.active }" @click="checkingBox('g7', box.g7.selected)">{{ box.g7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.g8.selected && box.g8.gender == 1, 'bg-danger': box.g8.selected && box.g8.gender == 2, 'disabled': !box.g8.active }" @click="checkingBox('g8', box.g8.selected)">{{ box.g8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g9.selected && box.g9.gender == 1, 'bg-danger': box.g9.selected && box.g9.gender == 2, 'disabled': !box.g9.active }" @click="checkingBox('g9', box.g9.selected)">{{ box.g9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g10.selected && box.g10.gender == 1, 'bg-danger': box.g10.selected && box.g10.gender == 2, 'disabled': !box.g10.active }" @click="checkingBox('g10', box.g10.selected)">{{ box.g10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g11.selected && box.g11.gender == 1, 'bg-danger': box.g11.selected && box.g11.gender == 2, 'disabled': !box.g11.active }" @click="checkingBox('g11', box.g11.selected)">{{ box.g11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g12.selected && box.g12.gender == 1, 'bg-danger': box.g12.selected && box.g12.gender == 2, 'disabled': !box.g12.active }" @click="checkingBox('g12', box.g12.selected)">{{ box.g12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g13.selected && box.g13.gender == 1, 'bg-danger': box.g13.selected && box.g13.gender == 2, 'disabled': !box.g13.active }" @click="checkingBox('g13', box.g13.selected)">{{ box.g13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.g14.selected && box.g14.gender == 1, 'bg-danger': box.g14.selected && box.g14.gender == 2, 'disabled': !box.g14.active }" @click="checkingBox('g14', box.g14.selected)">{{ box.g14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- F -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.f1.selected && box.f1.gender == 1, 'bg-danger': box.f1.selected && box.f1.gender == 2, 'disabled': !box.f1.active }" @click="checkingBox('f1', box.f1.selected)">{{ box.f1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f2.selected && box.f2.gender == 1, 'bg-danger': box.f2.selected && box.f2.gender == 2, 'disabled': !box.f2.active }" @click="checkingBox('f2', box.f2.selected)">{{ box.f2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f3.selected && box.f3.gender == 1, 'bg-danger': box.f3.selected && box.f3.gender == 2, 'disabled': !box.f3.active }" @click="checkingBox('f3', box.f3.selected)">{{ box.f3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f4.selected && box.f4.gender == 1, 'bg-danger': box.f4.selected && box.f4.gender == 2, 'disabled': !box.f4.active }" @click="checkingBox('f4', box.f4.selected)">{{ box.f4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f5.selected && box.f5.gender == 1, 'bg-danger': box.f5.selected && box.f5.gender == 2, 'disabled': !box.f5.active }" @click="checkingBox('f5', box.f5.selected)">{{ box.f5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f6.selected && box.f6.gender == 1, 'bg-danger': box.f6.selected && box.f6.gender == 2, 'disabled': !box.f6.active }" @click="checkingBox('f6', box.f6.selected)">{{ box.f6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f7.selected && box.f7.gender == 1, 'bg-danger': box.f7.selected && box.f7.gender == 2, 'disabled': !box.f7.active }" @click="checkingBox('f7', box.f7.selected)">{{ box.f7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.f8.selected && box.f8.gender == 1, 'bg-danger': box.f8.selected && box.f8.gender == 2, 'disabled': !box.f8.active }" @click="checkingBox('f8', box.f8.selected)">{{ box.f8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f9.selected && box.f9.gender == 1, 'bg-danger': box.f9.selected && box.f9.gender == 2, 'disabled': !box.f9.active }" @click="checkingBox('f9', box.f9.selected)">{{ box.f9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f10.selected && box.f10.gender == 1, 'bg-danger': box.f10.selected && box.f10.gender == 2, 'disabled': !box.f10.active }" @click="checkingBox('f10', box.f10.selected)">{{ box.f10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f11.selected && box.f11.gender == 1, 'bg-danger': box.f11.selected && box.f11.gender == 2, 'disabled': !box.f11.active }" @click="checkingBox('f11', box.f11.selected)">{{ box.f11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f12.selected && box.f12.gender == 1, 'bg-danger': box.f12.selected && box.f12.gender == 2, 'disabled': !box.f12.active }" @click="checkingBox('f12', box.f12.selected)">{{ box.f12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f13.selected && box.f13.gender == 1, 'bg-danger': box.f13.selected && box.f13.gender == 2, 'disabled': !box.f13.active }" @click="checkingBox('f13', box.f13.selected)">{{ box.f13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.f14.selected && box.f14.gender == 1, 'bg-danger': box.f14.selected && box.f14.gender == 2, 'disabled': !box.f14.active }" @click="checkingBox('f14', box.f14.selected)">{{ box.f14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- E -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.e1.selected && box.e1.gender == 1, 'bg-danger': box.e1.selected && box.e1.gender == 2, 'disabled': !box.e1.active }" @click="checkingBox('e1', box.e1.selected)">{{ box.e1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e2.selected && box.e2.gender == 1, 'bg-danger': box.e2.selected && box.e2.gender == 2, 'disabled': !box.e2.active }" @click="checkingBox('e2', box.e2.selected)">{{ box.e2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e3.selected && box.e3.gender == 1, 'bg-danger': box.e3.selected && box.e3.gender == 2, 'disabled': !box.e3.active }" @click="checkingBox('e3', box.e3.selected)">{{ box.e3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e4.selected && box.e4.gender == 1, 'bg-danger': box.e4.selected && box.e4.gender == 2, 'disabled': !box.e4.active }" @click="checkingBox('e4', box.e4.selected)">{{ box.e4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e5.selected && box.e5.gender == 1, 'bg-danger': box.e5.selected && box.e5.gender == 2, 'disabled': !box.e5.active }" @click="checkingBox('e5', box.e5.selected)">{{ box.e5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e6.selected && box.e6.gender == 1, 'bg-danger': box.e6.selected && box.e6.gender == 2, 'disabled': !box.e6.active }" @click="checkingBox('e6', box.e6.selected)">{{ box.e6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e7.selected && box.e7.gender == 1, 'bg-danger': box.e7.selected && box.e7.gender == 2, 'disabled': !box.e7.active }" @click="checkingBox('e7', box.e7.selected)">{{ box.e7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.e8.selected && box.e8.gender == 1, 'bg-danger': box.e8.selected && box.e8.gender == 2, 'disabled': !box.e8.active }" @click="checkingBox('e8', box.e8.selected)">{{ box.e8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e9.selected && box.e9.gender == 1, 'bg-danger': box.e9.selected && box.e9.gender == 2, 'disabled': !box.e9.active }" @click="checkingBox('e9', box.e9.selected)">{{ box.e9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e10.selected && box.e10.gender == 1, 'bg-danger': box.e10.selected && box.e10.gender == 2, 'disabled': !box.e10.active }" @click="checkingBox('e10', box.e10.selected)">{{ box.e10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e11.selected && box.e11.gender == 1, 'bg-danger': box.e11.selected && box.e11.gender == 2, 'disabled': !box.e11.active }" @click="checkingBox('e11', box.e11.selected)">{{ box.e11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e12.selected && box.e12.gender == 1, 'bg-danger': box.e12.selected && box.e12.gender == 2, 'disabled': !box.e12.active }" @click="checkingBox('e12', box.e12.selected)">{{ box.e12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e13.selected && box.e13.gender == 1, 'bg-danger': box.e13.selected && box.e13.gender == 2, 'disabled': !box.e13.active }" @click="checkingBox('e13', box.e13.selected)">{{ box.e13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.e14.selected && box.e14.gender == 1, 'bg-danger': box.e14.selected && box.e14.gender == 2, 'disabled': !box.e14.active }" @click="checkingBox('e14', box.e14.selected)">{{ box.e14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- D -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="6">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.d1.selected && box.d1.gender == 1, 'bg-danger': box.d1.selected && box.d1.gender == 2, 'disabled': !box.d1.active }" @click="checkingBox('d1', box.d1.selected)">{{ box.d1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d2.selected && box.d2.gender == 1, 'bg-danger': box.d2.selected && box.d2.gender == 2, 'disabled': !box.d2.active }" @click="checkingBox('d2', box.d2.selected)">{{ box.d2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d3.selected && box.d3.gender == 1, 'bg-danger': box.d3.selected && box.d3.gender == 2, 'disabled': !box.d3.active }" @click="checkingBox('d3', box.d3.selected)">{{ box.d3.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d4.selected && box.d4.gender == 1, 'bg-danger': box.d4.selected && box.d4.gender == 2, 'disabled': !box.d4.active }" @click="checkingBox('d4', box.d4.selected)">{{ box.d4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d5.selected && box.d5.gender == 1, 'bg-danger': box.d5.selected && box.d5.gender == 2, 'disabled': !box.d5.active }" @click="checkingBox('d5', box.d5.selected)">{{ box.d5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d6.selected && box.d6.gender == 1, 'bg-danger': box.d6.selected && box.d6.gender == 2, 'disabled': !box.d6.active }" @click="checkingBox('d6', box.d6.selected)">{{ box.d6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d7.selected && box.d7.gender == 1, 'bg-danger': box.d7.selected && box.d7.gender == 2, 'disabled': !box.d7.active }" @click="checkingBox('d7', box.d7.selected)">{{ box.d7.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.d8.selected && box.d8.gender == 1, 'bg-danger': box.d8.selected && box.d8.gender == 2, 'disabled': !box.d8.active }" @click="checkingBox('d8', box.d8.selected)">{{ box.d8.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d9.selected && box.d9.gender == 1, 'bg-danger': box.d9.selected && box.d9.gender == 2, 'disabled': !box.d9.active }" @click="checkingBox('d9', box.d9.selected)">{{ box.d9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d10.selected && box.d10.gender == 1, 'bg-danger': box.d10.selected && box.d10.gender == 2, 'disabled': !box.d10.active }" @click="checkingBox('d10', box.d10.selected)">{{ box.d10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d11.selected && box.d11.gender == 1, 'bg-danger': box.d11.selected && box.d11.gender == 2, 'disabled': !box.d11.active }" @click="checkingBox('d11', box.d11.selected)">{{ box.d11.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d12.selected && box.d12.gender == 1, 'bg-danger': box.d12.selected && box.d12.gender == 2, 'disabled': !box.d12.active }" @click="checkingBox('d12', box.d12.selected)">{{ box.d12.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d13.selected && box.d13.gender == 1, 'bg-danger': box.d13.selected && box.d13.gender == 2, 'disabled': !box.d13.active }" @click="checkingBox('d13', box.d13.selected)">{{ box.d13.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.d14.selected && box.d14.gender == 1, 'bg-danger': box.d14.selected && box.d14.gender == 2, 'disabled': !box.d14.active }" @click="checkingBox('d14', box.d14.selected)">{{ box.d14.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- C -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="3">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.c1.selected && box.c1.gender == 1, 'bg-danger': box.c1.selected && box.c1.gender == 2, 'disabled': !box.c1.active }" @click="checkingBox('c1', box.c1.selected)">{{ box.c1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c2.selected && box.c2.gender == 1, 'bg-danger': box.c2.selected && box.c2.gender == 2, 'disabled': !box.c2.active }" @click="checkingBox('c2', box.c2.selected)">{{ box.c2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c3.selected && box.c3.gender == 1, 'bg-danger': box.c3.selected && box.c3.gender == 2, 'disabled': !box.c3.active }" @click="checkingBox('c3', box.c3.selected)">{{ box.c3.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-center">
                                    <div class="box" :class="{ 'bg-success': box.c4.selected && box.c4.gender == 1, 'bg-danger': box.c4.selected && box.c4.gender == 2, 'disabled': !box.c4.active }" @click="checkingBox('c4', box.c4.selected)">{{ box.c4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c5.selected && box.c5.gender == 1, 'bg-danger': box.c5.selected && box.c5.gender == 2, 'disabled': !box.c5.active }" @click="checkingBox('c5', box.c5.selected)">{{ box.c5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c6.selected && box.c6.gender == 1, 'bg-danger': box.c6.selected && box.c6.gender == 2, 'disabled': !box.c6.active }" @click="checkingBox('c6', box.c6.selected)">{{ box.c6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c7.selected && box.c7.gender == 1, 'bg-danger': box.c7.selected && box.c7.gender == 2, 'disabled': !box.c7.active }" @click="checkingBox('c7', box.c7.selected)">{{ box.c7.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c8.selected && box.c8.gender == 1, 'bg-danger': box.c8.selected && box.c8.gender == 2, 'disabled': !box.c8.active }" @click="checkingBox('c8', box.c8.selected)">{{ box.c8.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="3">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.c9.selected && box.c9.gender == 1, 'bg-danger': box.c9.selected && box.c9.gender == 2, 'disabled': !box.c9.active }" @click="checkingBox('c9', box.c9.selected)">{{ box.c9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c10.selected && box.c10.gender == 1, 'bg-danger': box.c10.selected && box.c10.gender == 2, 'disabled': !box.c10.active }" @click="checkingBox('c10', box.c10.selected)">{{ box.c10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.c11.selected && box.c11.gender == 1, 'bg-danger': box.c11.selected && box.c11.gender == 2, 'disabled': !box.c11.active }" @click="checkingBox('c11', box.c11.selected)">{{ box.c11.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- B -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="3">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.b1.selected && box.b1.gender == 1, 'bg-danger': box.b1.selected && box.b1.gender == 2, 'disabled': !box.b1.active }" @click="checkingBox('b1', box.b1.selected)">{{ box.b1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b2.selected && box.b2.gender == 1, 'bg-danger': box.b2.selected && box.b2.gender == 2, 'disabled': !box.b2.active }" @click="checkingBox('b2', box.b2.selected)">{{ box.b2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b3.selected && box.b3.gender == 1, 'bg-danger': box.b3.selected && box.b3.gender == 2, 'disabled': !box.b3.active }" @click="checkingBox('b3', box.b3.selected)">{{ box.b3.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-center">
                                    <div class="box" :class="{ 'bg-success': box.b4.selected && box.b4.gender == 1, 'bg-danger': box.b4.selected && box.b4.gender == 2, 'disabled': !box.b4.active }" @click="checkingBox('b4', box.b4.selected)">{{ box.b4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b5.selected && box.b5.gender == 1, 'bg-danger': box.b5.selected && box.b5.gender == 2, 'disabled': !box.b5.active }" @click="checkingBox('b5', box.b5.selected)">{{ box.b5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b6.selected && box.b6.gender == 1, 'bg-danger': box.b6.selected && box.b6.gender == 2, 'disabled': !box.b6.active }" @click="checkingBox('b6', box.b6.selected)">{{ box.b6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b7.selected && box.b7.gender == 1, 'bg-danger': box.b7.selected && box.b7.gender == 2, 'disabled': !box.b7.active }" @click="checkingBox('b7', box.b7.selected)">{{ box.b7.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b8.selected && box.b8.gender == 1, 'bg-danger': box.b8.selected && box.b8.gender == 2, 'disabled': !box.b8.active }" @click="checkingBox('b8', box.b8.selected)">{{ box.b8.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="3">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.b9.selected && box.b9.gender == 1, 'bg-danger': box.b9.selected && box.b9.gender == 2, 'disabled': !box.b9.active }" @click="checkingBox('b9', box.b9.selected)">{{ box.b9.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b10.selected && box.b10.gender == 1, 'bg-danger': box.b10.selected && box.b10.gender == 2, 'disabled': !box.b10.active }" @click="checkingBox('b10', box.b10.selected)">{{ box.b10.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.b11.selected && box.b11.gender == 1, 'bg-danger': box.b11.selected && box.b11.gender == 2, 'disabled': !box.b11.active }" @click="checkingBox('b11', box.b11.selected)">{{ box.b11.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <!-- A -->
                        <b-row align-h="between" class="mb-1">
                            <b-col cols="3">
                                <div class="d-flex justify-content-start">
                                    <div class="box" :class="{ 'bg-success': box.a1.selected && box.a1.gender == 1, 'bg-danger': box.a1.selected && box.a1.gender == 2, 'disabled': !box.a1.active }" @click="checkingBox('a1', box.a1.selected)">{{ box.a1.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a2.selected && box.a2.gender == 1, 'bg-danger': box.a2.selected && box.a2.gender == 2, 'disabled': !box.a2.active }" @click="checkingBox('a2', box.a2.selected)">{{ box.a2.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a3.selected && box.a3.gender == 1, 'bg-danger': box.a3.selected && box.a3.gender == 2, 'disabled': !box.a3.active }" @click="checkingBox('a3', box.a3.selected)">{{ box.a3.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="6">
                                <div class="d-flex justify-content-center">
                                    <div class="box" :class="{ 'bg-success': box.a4.selected && box.a4.gender == 1, 'bg-danger': box.a4.selected && box.a4.gender == 2, 'disabled': !box.a4.active }" @click="checkingBox('a4', box.a4.selected)">{{ box.a4.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a5.selected && box.a5.gender == 1, 'bg-danger': box.a5.selected && box.a5.gender == 2, 'disabled': !box.a5.active }" @click="checkingBox('a5', box.a5.selected)">{{ box.a5.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a6.selected && box.a6.gender == 1, 'bg-danger': box.a6.selected && box.a6.gender == 2, 'disabled': !box.a6.active }" @click="checkingBox('a6', box.a6.selected)">{{ box.a6.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a7.selected && box.a7.gender == 1, 'bg-danger': box.a7.selected && box.a7.gender == 2, 'disabled': !box.a7.active }" @click="checkingBox('a7', box.a7.selected)">{{ box.a7.name }}</div>
                                    <div class="box" :class="{ 'bg-success': box.a8.selected && box.a8.gender == 1, 'bg-danger': box.a8.selected && box.a8.gender == 2, 'disabled': !box.a8.active }" @click="checkingBox('a8', box.a8.selected)">{{ box.a8.name }}</div>
                                </div>
                            </b-col>
                            <b-col cols="3">
                                <div class="d-flex justify-content-end">
                                    <div class="box" :class="{ 'bg-success': box.a9.selected && box.a9.gender == 1, 'bg-danger': box.a9.selected && box.a9.gender == 2, 'disabled': !box.a9.active }" @click="checkingBox('a9', box.a9.selected)">{{ box.a9.name }}</div>
                                </div>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="6" class="text-center bg-dark text-white p-1">Stage</b-col>
                        </b-row>
                    </div>
                </b-col>
            </b-row>

        </b-card>
    </div>
</template>

<script>
import { BCard, BAvatar, BMedia, BRow, BLink, BDropdown, BDropdownItem, BPagination, BTable, BCol, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import vSelect from 'vue-select'
import { getDetail, getMappingSeatList, postMappingSeat, getSeatSettledList } from '@/network/event-open-registration'
import flatPickr from 'vue-flatpickr-component'
import { hasPermission } from '@/auth/utils'
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import { until } from '@vueuse/core'
import _ from 'lodash'

export default {
    components: {
        BRow,
        BMedia,
        BAvatar,
        BPagination,
        BDropdownItem,
        BDropdown,
        BLink,
        BCol,
        BTable,
        BCard,
        BForm,
        BFormGroup,
        BFormInput,
        BAlert,
        BFormInvalidFeedback,
        BButton,
        BSpinner,
        BInputGroup,
        BInputGroupAppend,
        BFormCheckboxGroup,
        vSelect,
        flatPickr,

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    setup() {
        const eventId = ""
        return {
            avatarText, formatDateTime, formatDate, hasPermission,
            eventId,
            queryFilter: "",
            genderFilter: ""
        }
    },
    data() {
        // box
        const box = {
            a1:{name:'A1', selected:false, active:true, gender:0},a2:{name:'A2', selected:false, active:true, gender:0},a3:{name:'A3', selected:false, active:true, gender:0},a4:{name:'A4', selected:false, active:true, gender:0},a5:{name:'A5', selected:false, active:true, gender:0},a6:{name:'A6', selected:false, active:true, gender:0},a7:{name:'A7', selected:false, active:true, gender:0},a8:{name:'A8', selected:false, active:true, gender:0},a9:{name:'A9', selected:false, active:true, gender:0},
            b1:{name:'B1', selected:false, active:true, gender:0},b2:{name:'B2', selected:false, active:true, gender:0},b3:{name:'B3', selected:false, active:true, gender:0},b4:{name:'B4', selected:false, active:true, gender:0},b5:{name:'B5', selected:false, active:true, gender:0},b6:{name:'B6', selected:false, active:true, gender:0},b7:{name:'B7', selected:false, active:true, gender:0},b8:{name:'B8', selected:false, active:true, gender:0},b9:{name:'B9', selected:false, active:true, gender:0},b10:{name:'B10', selected:false, active:true, gender:0},b11:{name:'B11', selected:false, active:true, gender:0},
            c1:{name:'C1', selected:false, active:true, gender:0},c2:{name:'C2', selected:false, active:true, gender:0},c3:{name:'C3', selected:false, active:true, gender:0},c4:{name:'C4', selected:false, active:true, gender:0},c5:{name:'C5', selected:false, active:true, gender:0},c6:{name:'C6', selected:false, active:true, gender:0},c7:{name:'C7', selected:false, active:true, gender:0},c8:{name:'C8', selected:false, active:true, gender:0},c9:{name:'C9', selected:false, active:true, gender:0},c10:{name:'C10', selected:false, active:true, gender:0},c11:{name:'C11', selected:false, active:true, gender:0},
            d1:{name:'D1', selected:false, active:true, gender:0},d2:{name:'D2', selected:false, active:true, gender:0},d3:{name:'D3', selected:false, active:true, gender:0},d4:{name:'D4', selected:false, active:true, gender:0},d5:{name:'D5', selected:false, active:true, gender:0},d6:{name:'D6', selected:false, active:true, gender:0},d7:{name:'D7', selected:false, active:true, gender:0},d8:{name:'D8', selected:false, active:true, gender:0},d9:{name:'D9', selected:false, active:true, gender:0},d10:{name:'D10', selected:false, active:true, gender:0},d11:{name:'D11', selected:false, active:true, gender:0},d12:{name:'D12', selected:false, active:true, gender:0},d13:{name:'D13', selected:false, active:true, gender:0},d14:{name:'D14', selected:false, active:true, gender:0},
            e1:{name:'E1', selected:false, active:true, gender:0},e2:{name:'E2', selected:false, active:true, gender:0},e3:{name:'E3', selected:false, active:true, gender:0},e4:{name:'E4', selected:false, active:true, gender:0},e5:{name:'E5', selected:false, active:true, gender:0},e6:{name:'E6', selected:false, active:true, gender:0},e7:{name:'E7', selected:false, active:true, gender:0},e8:{name:'E8', selected:false, active:true, gender:0},e9:{name:'E9', selected:false, active:true, gender:0},e10:{name:'E10', selected:false, active:true, gender:0},e11:{name:'E11', selected:false, active:true, gender:0},e12:{name:'E12', selected:false, active:true, gender:0},e13:{name:'E13', selected:false, active:true, gender:0},e14:{name:'E14', selected:false, active:true, gender:0},
            f1:{name:'F1', selected:false, active:true, gender:0},f2:{name:'F2', selected:false, active:true, gender:0},f3:{name:'F3', selected:false, active:true, gender:0},f4:{name:'F4', selected:false, active:true, gender:0},f5:{name:'F5', selected:false, active:true, gender:0},f6:{name:'F6', selected:false, active:true, gender:0},f7:{name:'F7', selected:false, active:true, gender:0},f8:{name:'F8', selected:false, active:true, gender:0},f9:{name:'F9', selected:false, active:true, gender:0},f10:{name:'F10', selected:false, active:true, gender:0},f11:{name:'F11', selected:false, active:true, gender:0},f12:{name:'F12', selected:false, active:true, gender:0},f13:{name:'F13', selected:false, active:true, gender:0},f14:{name:'F14', selected:false, active:true, gender:0},
            g1:{name:'G1', selected:false, active:true, gender:0},g2:{name:'G2', selected:false, active:true, gender:0},g3:{name:'G3', selected:false, active:true, gender:0},g4:{name:'G4', selected:false, active:true, gender:0},g5:{name:'G5', selected:false, active:true, gender:0},g6:{name:'G6', selected:false, active:true, gender:0},g7:{name:'G7', selected:false, active:true, gender:0},g8:{name:'G8', selected:false, active:true, gender:0},g9:{name:'G9', selected:false, active:true, gender:0},g10:{name:'G10', selected:false, active:true, gender:0},g11:{name:'G11', selected:false, active:true, gender:0},g12:{name:'G12', selected:false, active:true, gender:0},g13:{name:'G13', selected:false, active:true, gender:0},g14:{name:'G14', selected:false, active:true, gender:0},
            h1:{name:'H1', selected:false, active:true, gender:0},h2:{name:'H2', selected:false, active:true, gender:0},h3:{name:'H3', selected:false, active:true, gender:0},h4:{name:'H4', selected:false, active:true, gender:0},h5:{name:'H5', selected:false, active:true, gender:0},h6:{name:'H6', selected:false, active:true, gender:0},h7:{name:'H7', selected:false, active:true, gender:0},h8:{name:'H8', selected:false, active:true, gender:0},h9:{name:'H9', selected:false, active:true, gender:0},h10:{name:'H10', selected:false, active:true, gender:0},h11:{name:'H11', selected:false, active:true, gender:0},h12:{name:'H12', selected:false, active:true, gender:0},h13:{name:'H13', selected:false, active:true, gender:0},h14:{name:'H14', selected:false, active:true, gender:0},
            i1:{name:'I1', selected:false, active:true, gender:0},i2:{name:'I2', selected:false, active:true, gender:0},i3:{name:'I3', selected:false, active:true, gender:0},i4:{name:'I4', selected:false, active:true, gender:0},i5:{name:'I5', selected:false, active:true, gender:0},i6:{name:'I6', selected:false, active:true, gender:0},i7:{name:'I7', selected:false, active:true, gender:0},i8:{name:'I8', selected:false, active:true, gender:0},i9:{name:'I9', selected:false, active:true, gender:0},i10:{name:'I10', selected:false, active:true, gender:0},i11:{name:'I11', selected:false, active:true, gender:0},i12:{name:'I12', selected:false, active:true, gender:0},i13:{name:'I13', selected:false, active:true, gender:0},i14:{name:'I14', selected:false, active:true, gender:0},
            j1:{name:'J1', selected:false, active:true, gender:0},j2:{name:'J2', selected:false, active:true, gender:0},j3:{name:'J3', selected:false, active:true, gender:0},j4:{name:'J4', selected:false, active:true, gender:0},j5:{name:'J5', selected:false, active:true, gender:0},j6:{name:'J6', selected:false, active:true, gender:0},j7:{name:'J7', selected:false, active:true, gender:0},j8:{name:'J8', selected:false, active:true, gender:0},j9:{name:'J9', selected:false, active:true, gender:0},j10:{name:'J10', selected:false, active:true, gender:0},j11:{name:'J11', selected:false, active:true, gender:0},j12:{name:'J12', selected:false, active:true, gender:0},j13:{name:'J13', selected:false, active:true, gender:0},j14:{name:'J14', selected:false, active:true, gender:0},
            k1:{name:'K1', selected:false, active:true, gender:0},k2:{name:'K2', selected:false, active:true, gender:0},k3:{name:'K3', selected:false, active:true, gender:0},k4:{name:'K4', selected:false, active:true, gender:0},k5:{name:'K5', selected:false, active:true, gender:0},k6:{name:'K6', selected:false, active:true, gender:0},k7:{name:'K7', selected:false, active:true, gender:0},k8:{name:'K8', selected:false, active:true, gender:0},k9:{name:'K9', selected:false, active:true, gender:0},k10:{name:'K10', selected:false, active:true, gender:0},k11:{name:'K11', selected:false, active:true, gender:0},k12:{name:'K12', selected:false, active:true, gender:0},k13:{name:'K13', selected:false, active:true, gender:0},k14:{name:'K14', selected:false, active:true, gender:0},
            l1:{name:'L1', selected:false, active:true, gender:0},l2:{name:'L2', selected:false, active:true, gender:0},l3:{name:'L3', selected:false, active:true, gender:0},l4:{name:'L4', selected:false, active:true, gender:0},l5:{name:'L5', selected:false, active:true, gender:0},l6:{name:'L6', selected:false, active:true, gender:0},l7:{name:'L7', selected:false, active:true, gender:0},l8:{name:'L8', selected:false, active:true, gender:0},l9:{name:'L9', selected:false, active:true, gender:0},l10:{name:'L10', selected:false, active:true, gender:0},l11:{name:'L11', selected:false, active:true, gender:0},l12:{name:'L12', selected:false, active:true, gender:0},l13:{name:'L13', selected:false, active:true, gender:0},l14:{name:'L14', selected:false, active:true, gender:0},
            m1:{name:'M1', selected:false, active:true, gender:0},m2:{name:'M2', selected:false, active:true, gender:0},m3:{name:'M3', selected:false, active:true, gender:0},m4:{name:'M4', selected:false, active:true, gender:0},m5:{name:'M5', selected:false, active:true, gender:0},m6:{name:'M6', selected:false, active:true, gender:0},m7:{name:'M7', selected:false, active:true, gender:0},m8:{name:'M8', selected:false, active:true, gender:0},m9:{name:'M9', selected:false, active:true, gender:0},m10:{name:'M10', selected:false, active:true, gender:0},m11:{name:'M11', selected:false, active:true, gender:0},m12:{name:'M12', selected:false, active:true, gender:0},m13:{name:'M13', selected:false, active:true, gender:0},m14:{name:'M14', selected:false, active:true, gender:0},
            n1:{name:'N1', selected:false, active:true, gender:0},n2:{name:'N2', selected:false, active:true, gender:0},n3:{name:'N3', selected:false, active:true, gender:0},n4:{name:'N4', selected:false, active:true, gender:0},n5:{name:'N5', selected:false, active:true, gender:0},n6:{name:'N6', selected:false, active:true, gender:0},n7:{name:'N7', selected:false, active:true, gender:0},n8:{name:'N8', selected:false, active:true, gender:0},n9:{name:'N9', selected:false, active:true, gender:0},n10:{name:'N10', selected:false, active:true, gender:0},n11:{name:'N11', selected:false, active:true, gender:0},n12:{name:'N12', selected:false, active:true, gender:0},n13:{name:'N13', selected:false, active:true, gender:0},n14:{name:'N14', selected:false, active:true, gender:0},
            o1:{name:'O1', selected:false, active:true, gender:0},o2:{name:'O2', selected:false, active:true, gender:0},o3:{name:'O3', selected:false, active:true, gender:0},o4:{name:'O4', selected:false, active:true, gender:0},o5:{name:'O5', selected:false, active:true, gender:0},o6:{name:'O6', selected:false, active:true, gender:0},o7:{name:'O7', selected:false, active:true, gender:0},o8:{name:'O8', selected:false, active:true, gender:0},o9:{name:'O9', selected:false, active:true, gender:0},o10:{name:'O10', selected:false, active:true, gender:0},o11:{name:'O11', selected:false, active:true, gender:0},o12:{name:'O12', selected:false, active:true, gender:0},
            p1:{name:'P1', selected:false, active:true, gender:0},p2:{name:'P2', selected:false, active:true, gender:0},p3:{name:'P3', selected:false, active:true, gender:0},p4:{name:'P4', selected:false, active:true, gender:0},p5:{name:'P5', selected:false, active:true, gender:0},p6:{name:'P6', selected:false, active:true, gender:0},p7:{name:'P7', selected:false, active:true, gender:0},p8:{name:'P8', selected:false, active:true, gender:0},p9:{name:'P9', selected:false, active:true, gender:0},p10:{name:'P10', selected:false, active:true, gender:0},p11:{name:'P11', selected:false, active:true, gender:0},p12:{name:'P12', selected:false, active:true, gender:0},p13:{name:'P13', selected:false, active:true, gender:0},p14:{name:'P14', selected:false, active:true, gender:0},
            q1:{name:'Q1', selected:false, active:true, gender:0},q2:{name:'Q2', selected:false, active:true, gender:0},q3:{name:'Q3', selected:false, active:true, gender:0},q4:{name:'Q4', selected:false, active:true, gender:0},q5:{name:'Q5', selected:false, active:true, gender:0},q6:{name:'Q6', selected:false, active:true, gender:0},q7:{name:'Q7', selected:false, active:true, gender:0},q8:{name:'Q8', selected:false, active:true, gender:0},q9:{name:'Q9', selected:false, active:true, gender:0},q10:{name:'Q10', selected:false, active:true, gender:0},q11:{name:'Q11', selected:false, active:true, gender:0},q12:{name:'Q12', selected:false, active:true, gender:0},q13:{name:'Q13', selected:false, active:true, gender:0},q14:{name:'Q14', selected:false, active:true, gender:0},
            r1:{name:'R1', selected:false, active:true, gender:0},r2:{name:'R2', selected:false, active:true, gender:0},r3:{name:'R3', selected:false, active:true, gender:0},r4:{name:'R4', selected:false, active:true, gender:0},r5:{name:'R5', selected:false, active:true, gender:0},r6:{name:'R6', selected:false, active:true, gender:0},r7:{name:'R7', selected:false, active:true, gender:0},r8:{name:'R8', selected:false, active:true, gender:0},r9:{name:'R9', selected:false, active:true, gender:0},r10:{name:'R10', selected:false, active:true, gender:0},r11:{name:'R11', selected:false, active:true, gender:0},r12:{name:'R12', selected:false, active:true, gender:0},r13:{name:'R13', selected:false, active:true, gender:0},r14:{name:'R14', selected:false, active:true, gender:0},
            s1:{name:'S1', selected:false, active:true, gender:0},s2:{name:'S2', selected:false, active:true, gender:0},s3:{name:'S3', selected:false, active:true, gender:0},s4:{name:'S4', selected:false, active:true, gender:0},s5:{name:'S5', selected:false, active:true, gender:0},s6:{name:'S6', selected:false, active:true, gender:0},s7:{name:'S7', selected:false, active:true, gender:0},s8:{name:'S8', selected:false, active:true, gender:0},s9:{name:'S9', selected:false, active:true, gender:0},s10:{name:'S10', selected:false, active:true, gender:0},s11:{name:'S11', selected:false, active:true, gender:0},s12:{name:'S12', selected:false, active:true, gender:0},s13:{name:'S13', selected:false, active:true, gender:0},s14:{name:'S14', selected:false, active:true, gender:0},
            t1:{name:'T1', selected:false, active:true, gender:0},t2:{name:'T2', selected:false, active:true, gender:0},t3:{name:'T3', selected:false, active:true, gender:0},t4:{name:'T4', selected:false, active:true, gender:0},t5:{name:'T5', selected:false, active:true, gender:0},t6:{name:'T6', selected:false, active:true, gender:0},t7:{name:'T7', selected:false, active:true, gender:0},t8:{name:'T8', selected:false, active:true, gender:0},t9:{name:'T9', selected:false, active:true, gender:0},t10:{name:'T10', selected:false, active:true, gender:0},t11:{name:'T11', selected:false, active:true, gender:0},t12:{name:'T12', selected:false, active:true, gender:0},
            u1:{name:'U1', selected:false, active:true, gender:0},u2:{name:'U2', selected:false, active:true, gender:0},u3:{name:'U3', selected:false, active:true, gender:0},u4:{name:'U4', selected:false, active:true, gender:0},u5:{name:'U5', selected:false, active:true, gender:0},u6:{name:'U6', selected:false, active:true, gender:0},
            v1:{name:'V1', selected:false, active:true, gender:0},v2:{name:'V2', selected:false, active:true, gender:0},v3:{name:'V3', selected:false, active:true, gender:0},v4:{name:'V4', selected:false, active:true, gender:0},v5:{name:'V5', selected:false, active:true, gender:0},v6:{name:'V6', selected:false, active:true, gender:0},
        }
        const event = {}
        const participantList = []

        const id = this.$route.params.id || ""
        if (id == "") this.$router.back()
        this.eventId = id
        getDetail(id).then(response => {
            this.event = response.data
            this.resetBox()
        }).catch(error => { })

        this.getParticipantList()

        return {
            required,
            min,
            numeric,
            email,
            event,
            loadingParticipantList: false,
            participantList,
            selectedParticipant: {},
            selectedSeats: [],
            loadingSeat: false,
            box
        }
    },
    computed: {
    },
    methods: {
        selectingParticipant(participant) {
            this.resetBox()
            this.selectedSeats = []
            this.selectedParticipant = participant
        },
        checkingBox(box, val) {
            if(!this.box[box].active) {
                this.deleteSeat(box, this.box[box].name)
            }

            if (_.isEmpty(this.selectedParticipant)) return
            if(!this.box[box].active) return
            
            this.box[box].selected = !val
            if(this.box[box].selected) {
                if(this.selectedSeats.length >= this.selectedParticipant.pax) {
                    this.box[box].selected = false
                    this.$bvToast.toast(`Warning: Melebihi jumlah pax`, { title: `Warning`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    return
                }

                this.$swal({
                    title: `Pilihan Kursi`,
                    text: "Kursi untuk ikhwan / akhwat?",
                    icon: 'warning',
                    showCancelButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Ikhwan',
                    cancelButtonText: 'Akhwat',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger ml-1',
                    },
                    buttonsStyling: false,
                }).then(result => {
                    var gender = 2;
                    if (result.value) {
                        gender = 1
                    }
                    this.box[box].gender = gender
                })
            }
            
            if(this.box[box].selected) {
                this.selectedSeats.push(this.box[box])
            } else {
                const idxToRemove = this.selectedSeats.findIndex((obj) => obj.name == this.box[box].name)
                this.selectedSeats.splice(idxToRemove, 1);
            }

            console.log(this.selectedSeats)
            console.log(this.selectedSeats.length)
        },
        getParticipantList() {
            getMappingSeatList({
                q: this.queryFilter,
                eventId: this.eventId,
                gender: this.genderFilter,
            }).then(response => {
                this.loadingParticipantList = false
                this.participantList = response.data


            }).catch(error => {
                if (error.response.data.errors) {
                    this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                } else {
                    this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                }
                this.loadingParticipantList = false
            })
        },
        getSeatList() {
            this.loadingSeat = true
            getSeatSettledList({
                eventId: this.eventId,
            }).then(response => {
                this.loadingSeat = false
                response.data.forEach(element => {
                    this.box[element.seat_number].active = false;
                    this.box[element.seat_number].selected = true;
                    this.box[element.seat_number].gender = element.gender;
                });
            }).catch(error => {
                if (error.response.data.errors) {
                    this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                } else {
                    this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                }
                this.loadingSeat = false
            })
        },
        resetBox() {
            this.box = {
                a1:{name:'A1', selected:false, active:true, gender:0},a2:{name:'A2', selected:false, active:true, gender:0},a3:{name:'A3', selected:false, active:true, gender:0},a4:{name:'A4', selected:false, active:true, gender:0},a5:{name:'A5', selected:false, active:true, gender:0},a6:{name:'A6', selected:false, active:true, gender:0},a7:{name:'A7', selected:false, active:true, gender:0},a8:{name:'A8', selected:false, active:true, gender:0},a9:{name:'A9', selected:false, active:true, gender:0},
                b1:{name:'B1', selected:false, active:true, gender:0},b2:{name:'B2', selected:false, active:true, gender:0},b3:{name:'B3', selected:false, active:true, gender:0},b4:{name:'B4', selected:false, active:true, gender:0},b5:{name:'B5', selected:false, active:true, gender:0},b6:{name:'B6', selected:false, active:true, gender:0},b7:{name:'B7', selected:false, active:true, gender:0},b8:{name:'B8', selected:false, active:true, gender:0},b9:{name:'B9', selected:false, active:true, gender:0},b10:{name:'B10', selected:false, active:true, gender:0},b11:{name:'B11', selected:false, active:true, gender:0},
                c1:{name:'C1', selected:false, active:true, gender:0},c2:{name:'C2', selected:false, active:true, gender:0},c3:{name:'C3', selected:false, active:true, gender:0},c4:{name:'C4', selected:false, active:true, gender:0},c5:{name:'C5', selected:false, active:true, gender:0},c6:{name:'C6', selected:false, active:true, gender:0},c7:{name:'C7', selected:false, active:true, gender:0},c8:{name:'C8', selected:false, active:true, gender:0},c9:{name:'C9', selected:false, active:true, gender:0},c10:{name:'C10', selected:false, active:true, gender:0},c11:{name:'C11', selected:false, active:true, gender:0},
                d1:{name:'D1', selected:false, active:true, gender:0},d2:{name:'D2', selected:false, active:true, gender:0},d3:{name:'D3', selected:false, active:true, gender:0},d4:{name:'D4', selected:false, active:true, gender:0},d5:{name:'D5', selected:false, active:true, gender:0},d6:{name:'D6', selected:false, active:true, gender:0},d7:{name:'D7', selected:false, active:true, gender:0},d8:{name:'D8', selected:false, active:true, gender:0},d9:{name:'D9', selected:false, active:true, gender:0},d10:{name:'D10', selected:false, active:true, gender:0},d11:{name:'D11', selected:false, active:true, gender:0},d12:{name:'D12', selected:false, active:true, gender:0},d13:{name:'D13', selected:false, active:true, gender:0},d14:{name:'D14', selected:false, active:true, gender:0},
                e1:{name:'E1', selected:false, active:true, gender:0},e2:{name:'E2', selected:false, active:true, gender:0},e3:{name:'E3', selected:false, active:true, gender:0},e4:{name:'E4', selected:false, active:true, gender:0},e5:{name:'E5', selected:false, active:true, gender:0},e6:{name:'E6', selected:false, active:true, gender:0},e7:{name:'E7', selected:false, active:true, gender:0},e8:{name:'E8', selected:false, active:true, gender:0},e9:{name:'E9', selected:false, active:true, gender:0},e10:{name:'E10', selected:false, active:true, gender:0},e11:{name:'E11', selected:false, active:true, gender:0},e12:{name:'E12', selected:false, active:true, gender:0},e13:{name:'E13', selected:false, active:true, gender:0},e14:{name:'E14', selected:false, active:true, gender:0},
                f1:{name:'F1', selected:false, active:true, gender:0},f2:{name:'F2', selected:false, active:true, gender:0},f3:{name:'F3', selected:false, active:true, gender:0},f4:{name:'F4', selected:false, active:true, gender:0},f5:{name:'F5', selected:false, active:true, gender:0},f6:{name:'F6', selected:false, active:true, gender:0},f7:{name:'F7', selected:false, active:true, gender:0},f8:{name:'F8', selected:false, active:true, gender:0},f9:{name:'F9', selected:false, active:true, gender:0},f10:{name:'F10', selected:false, active:true, gender:0},f11:{name:'F11', selected:false, active:true, gender:0},f12:{name:'F12', selected:false, active:true, gender:0},f13:{name:'F13', selected:false, active:true, gender:0},f14:{name:'F14', selected:false, active:true, gender:0},
                g1:{name:'G1', selected:false, active:true, gender:0},g2:{name:'G2', selected:false, active:true, gender:0},g3:{name:'G3', selected:false, active:true, gender:0},g4:{name:'G4', selected:false, active:true, gender:0},g5:{name:'G5', selected:false, active:true, gender:0},g6:{name:'G6', selected:false, active:true, gender:0},g7:{name:'G7', selected:false, active:true, gender:0},g8:{name:'G8', selected:false, active:true, gender:0},g9:{name:'G9', selected:false, active:true, gender:0},g10:{name:'G10', selected:false, active:true, gender:0},g11:{name:'G11', selected:false, active:true, gender:0},g12:{name:'G12', selected:false, active:true, gender:0},g13:{name:'G13', selected:false, active:true, gender:0},g14:{name:'G14', selected:false, active:true, gender:0},
                h1:{name:'H1', selected:false, active:true, gender:0},h2:{name:'H2', selected:false, active:true, gender:0},h3:{name:'H3', selected:false, active:true, gender:0},h4:{name:'H4', selected:false, active:true, gender:0},h5:{name:'H5', selected:false, active:true, gender:0},h6:{name:'H6', selected:false, active:true, gender:0},h7:{name:'H7', selected:false, active:true, gender:0},h8:{name:'H8', selected:false, active:true, gender:0},h9:{name:'H9', selected:false, active:true, gender:0},h10:{name:'H10', selected:false, active:true, gender:0},h11:{name:'H11', selected:false, active:true, gender:0},h12:{name:'H12', selected:false, active:true, gender:0},h13:{name:'H13', selected:false, active:true, gender:0},h14:{name:'H14', selected:false, active:true, gender:0},
                i1:{name:'I1', selected:false, active:true, gender:0},i2:{name:'I2', selected:false, active:true, gender:0},i3:{name:'I3', selected:false, active:true, gender:0},i4:{name:'I4', selected:false, active:true, gender:0},i5:{name:'I5', selected:false, active:true, gender:0},i6:{name:'I6', selected:false, active:true, gender:0},i7:{name:'I7', selected:false, active:true, gender:0},i8:{name:'I8', selected:false, active:true, gender:0},i9:{name:'I9', selected:false, active:true, gender:0},i10:{name:'I10', selected:false, active:true, gender:0},i11:{name:'I11', selected:false, active:true, gender:0},i12:{name:'I12', selected:false, active:true, gender:0},i13:{name:'I13', selected:false, active:true, gender:0},i14:{name:'I14', selected:false, active:true, gender:0},
                j1:{name:'J1', selected:false, active:true, gender:0},j2:{name:'J2', selected:false, active:true, gender:0},j3:{name:'J3', selected:false, active:true, gender:0},j4:{name:'J4', selected:false, active:true, gender:0},j5:{name:'J5', selected:false, active:true, gender:0},j6:{name:'J6', selected:false, active:true, gender:0},j7:{name:'J7', selected:false, active:true, gender:0},j8:{name:'J8', selected:false, active:true, gender:0},j9:{name:'J9', selected:false, active:true, gender:0},j10:{name:'J10', selected:false, active:true, gender:0},j11:{name:'J11', selected:false, active:true, gender:0},j12:{name:'J12', selected:false, active:true, gender:0},j13:{name:'J13', selected:false, active:true, gender:0},j14:{name:'J14', selected:false, active:true, gender:0},
                k1:{name:'K1', selected:false, active:true, gender:0},k2:{name:'K2', selected:false, active:true, gender:0},k3:{name:'K3', selected:false, active:true, gender:0},k4:{name:'K4', selected:false, active:true, gender:0},k5:{name:'K5', selected:false, active:true, gender:0},k6:{name:'K6', selected:false, active:true, gender:0},k7:{name:'K7', selected:false, active:true, gender:0},k8:{name:'K8', selected:false, active:true, gender:0},k9:{name:'K9', selected:false, active:true, gender:0},k10:{name:'K10', selected:false, active:true, gender:0},k11:{name:'K11', selected:false, active:true, gender:0},k12:{name:'K12', selected:false, active:true, gender:0},k13:{name:'K13', selected:false, active:true, gender:0},k14:{name:'K14', selected:false, active:true, gender:0},
                l1:{name:'L1', selected:false, active:true, gender:0},l2:{name:'L2', selected:false, active:true, gender:0},l3:{name:'L3', selected:false, active:true, gender:0},l4:{name:'L4', selected:false, active:true, gender:0},l5:{name:'L5', selected:false, active:true, gender:0},l6:{name:'L6', selected:false, active:true, gender:0},l7:{name:'L7', selected:false, active:true, gender:0},l8:{name:'L8', selected:false, active:true, gender:0},l9:{name:'L9', selected:false, active:true, gender:0},l10:{name:'L10', selected:false, active:true, gender:0},l11:{name:'L11', selected:false, active:true, gender:0},l12:{name:'L12', selected:false, active:true, gender:0},l13:{name:'L13', selected:false, active:true, gender:0},l14:{name:'L14', selected:false, active:true, gender:0},
                m1:{name:'M1', selected:false, active:true, gender:0},m2:{name:'M2', selected:false, active:true, gender:0},m3:{name:'M3', selected:false, active:true, gender:0},m4:{name:'M4', selected:false, active:true, gender:0},m5:{name:'M5', selected:false, active:true, gender:0},m6:{name:'M6', selected:false, active:true, gender:0},m7:{name:'M7', selected:false, active:true, gender:0},m8:{name:'M8', selected:false, active:true, gender:0},m9:{name:'M9', selected:false, active:true, gender:0},m10:{name:'M10', selected:false, active:true, gender:0},m11:{name:'M11', selected:false, active:true, gender:0},m12:{name:'M12', selected:false, active:true, gender:0},m13:{name:'M13', selected:false, active:true, gender:0},m14:{name:'M14', selected:false, active:true, gender:0},
                n1:{name:'N1', selected:false, active:true, gender:0},n2:{name:'N2', selected:false, active:true, gender:0},n3:{name:'N3', selected:false, active:true, gender:0},n4:{name:'N4', selected:false, active:true, gender:0},n5:{name:'N5', selected:false, active:true, gender:0},n6:{name:'N6', selected:false, active:true, gender:0},n7:{name:'N7', selected:false, active:true, gender:0},n8:{name:'N8', selected:false, active:true, gender:0},n9:{name:'N9', selected:false, active:true, gender:0},n10:{name:'N10', selected:false, active:true, gender:0},n11:{name:'N11', selected:false, active:true, gender:0},n12:{name:'N12', selected:false, active:true, gender:0},n13:{name:'N13', selected:false, active:true, gender:0},n14:{name:'N14', selected:false, active:true, gender:0},
                o1:{name:'O1', selected:false, active:true, gender:0},o2:{name:'O2', selected:false, active:true, gender:0},o3:{name:'O3', selected:false, active:true, gender:0},o4:{name:'O4', selected:false, active:true, gender:0},o5:{name:'O5', selected:false, active:true, gender:0},o6:{name:'O6', selected:false, active:true, gender:0},o7:{name:'O7', selected:false, active:true, gender:0},o8:{name:'O8', selected:false, active:true, gender:0},o9:{name:'O9', selected:false, active:true, gender:0},o10:{name:'O10', selected:false, active:true, gender:0},o11:{name:'O11', selected:false, active:true, gender:0},o12:{name:'O12', selected:false, active:true, gender:0},
                p1:{name:'P1', selected:false, active:true, gender:0},p2:{name:'P2', selected:false, active:true, gender:0},p3:{name:'P3', selected:false, active:true, gender:0},p4:{name:'P4', selected:false, active:true, gender:0},p5:{name:'P5', selected:false, active:true, gender:0},p6:{name:'P6', selected:false, active:true, gender:0},p7:{name:'P7', selected:false, active:true, gender:0},p8:{name:'P8', selected:false, active:true, gender:0},p9:{name:'P9', selected:false, active:true, gender:0},p10:{name:'P10', selected:false, active:true, gender:0},p11:{name:'P11', selected:false, active:true, gender:0},p12:{name:'P12', selected:false, active:true, gender:0},p13:{name:'P13', selected:false, active:true, gender:0},p14:{name:'P14', selected:false, active:true, gender:0},
                q1:{name:'Q1', selected:false, active:true, gender:0},q2:{name:'Q2', selected:false, active:true, gender:0},q3:{name:'Q3', selected:false, active:true, gender:0},q4:{name:'Q4', selected:false, active:true, gender:0},q5:{name:'Q5', selected:false, active:true, gender:0},q6:{name:'Q6', selected:false, active:true, gender:0},q7:{name:'Q7', selected:false, active:true, gender:0},q8:{name:'Q8', selected:false, active:true, gender:0},q9:{name:'Q9', selected:false, active:true, gender:0},q10:{name:'Q10', selected:false, active:true, gender:0},q11:{name:'Q11', selected:false, active:true, gender:0},q12:{name:'Q12', selected:false, active:true, gender:0},q13:{name:'Q13', selected:false, active:true, gender:0},q14:{name:'Q14', selected:false, active:true, gender:0},
                r1:{name:'R1', selected:false, active:true, gender:0},r2:{name:'R2', selected:false, active:true, gender:0},r3:{name:'R3', selected:false, active:true, gender:0},r4:{name:'R4', selected:false, active:true, gender:0},r5:{name:'R5', selected:false, active:true, gender:0},r6:{name:'R6', selected:false, active:true, gender:0},r7:{name:'R7', selected:false, active:true, gender:0},r8:{name:'R8', selected:false, active:true, gender:0},r9:{name:'R9', selected:false, active:true, gender:0},r10:{name:'R10', selected:false, active:true, gender:0},r11:{name:'R11', selected:false, active:true, gender:0},r12:{name:'R12', selected:false, active:true, gender:0},r13:{name:'R13', selected:false, active:true, gender:0},r14:{name:'R14', selected:false, active:true, gender:0},
                s1:{name:'S1', selected:false, active:true, gender:0},s2:{name:'S2', selected:false, active:true, gender:0},s3:{name:'S3', selected:false, active:true, gender:0},s4:{name:'S4', selected:false, active:true, gender:0},s5:{name:'S5', selected:false, active:true, gender:0},s6:{name:'S6', selected:false, active:true, gender:0},s7:{name:'S7', selected:false, active:true, gender:0},s8:{name:'S8', selected:false, active:true, gender:0},s9:{name:'S9', selected:false, active:true, gender:0},s10:{name:'S10', selected:false, active:true, gender:0},s11:{name:'S11', selected:false, active:true, gender:0},s12:{name:'S12', selected:false, active:true, gender:0},s13:{name:'S13', selected:false, active:true, gender:0},s14:{name:'S14', selected:false, active:true, gender:0},
                t1:{name:'T1', selected:false, active:true, gender:0},t2:{name:'T2', selected:false, active:true, gender:0},t3:{name:'T3', selected:false, active:true, gender:0},t4:{name:'T4', selected:false, active:true, gender:0},t5:{name:'T5', selected:false, active:true, gender:0},t6:{name:'T6', selected:false, active:true, gender:0},t7:{name:'T7', selected:false, active:true, gender:0},t8:{name:'T8', selected:false, active:true, gender:0},t9:{name:'T9', selected:false, active:true, gender:0},t10:{name:'T10', selected:false, active:true, gender:0},t11:{name:'T11', selected:false, active:true, gender:0},t12:{name:'T12', selected:false, active:true, gender:0},
                u1:{name:'U1', selected:false, active:true, gender:0},u2:{name:'U2', selected:false, active:true, gender:0},u3:{name:'U3', selected:false, active:true, gender:0},u4:{name:'U4', selected:false, active:true, gender:0},u5:{name:'U5', selected:false, active:true, gender:0},u6:{name:'U6', selected:false, active:true, gender:0},
                v1:{name:'V1', selected:false, active:true, gender:0},v2:{name:'V2', selected:false, active:true, gender:0},v3:{name:'V3', selected:false, active:true, gender:0},v4:{name:'V4', selected:false, active:true, gender:0},v5:{name:'V5', selected:false, active:true, gender:0},v6:{name:'V6', selected:false, active:true, gender:0},
            }
            this.getSeatList()
        },
        submitMappingSeat() {
            this.loadingSeat = true
            const vForm = {}

            vForm.event_id = this.eventId,
            vForm.attendee = this.selectedParticipant
            vForm.seats = this.selectedSeats
            postMappingSeat(vForm).then(response => {
                this.loadingSeat = false
                this.$bvToast.toast('Nomor kursi berhasil disimpan', {
                    title: `Success`,
                    variant: 'primary',
                    toaster: 'b-toaster-top-center',
                    solid: true,
                })
                this.getParticipantList()
                this.resetBox()
                this.selectedSeats = []
                this.selectedParticipant = {}
            })
            .catch(error => {
                this.loadingSeat = false
                if (error.response.data.errors) {
                    this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                } else {
                    this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                }
            })
        },
        deleteSeat(box, name) {
            this.$swal({
                title: `Apakah anda yakin?`,
                text: "Kursi yang sudah di mapping untuk " + name + " akan dihapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.loadingSeat = true
                    const vForm = {}

                    vForm.event_id = this.eventId
                    vForm.reset_seat = true
                    vForm.seat_number = box
                    postMappingSeat(vForm).then(response => {
                        this.loadingSeat = false
                        this.$bvToast.toast('Nomor kursi berhasil direset', {
                            title: `Success`,
                            variant: 'primary',
                            toaster: 'b-toaster-top-center',
                            solid: true,
                        })
                        this.getParticipantList()
                        this.resetBox()
                        this.selectedSeats = []
                        this.selectedParticipant = {}
                    })
                    .catch(error => {
                        this.loadingSeat = false
                        if (error.response.data.errors) {
                            this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        } else {
                            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        }
                    })
                }
            })
        },
        resetMappingSeat() {
            this.$swal({
                title: `Apakah anda yakin?`,
                text: "Kursi yang sudah di mapping akan direset dan harus mulai dari awal",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary ml-1',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.value) {
                    this.loadingSeat = true
                    const vForm = {}

                    vForm.event_id = this.eventId
                    vForm.reset_all_seat = true
                    postMappingSeat(vForm).then(response => {
                        this.loadingSeat = false
                        this.$bvToast.toast('Nomor kursi berhasil direset', {
                            title: `Success`,
                            variant: 'primary',
                            toaster: 'b-toaster-top-center',
                            solid: true,
                        })
                        this.getParticipantList()
                        this.resetBox()
                        this.selectedSeats = []
                        this.selectedParticipant = {}
                    })
                    .catch(error => {
                        this.loadingSeat = false
                        if (error.response.data.errors) {
                            this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        } else {
                            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                        }
                    })
                }
            })
        }
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
.wrapper {
  border: 1px solid #ccc;
  border-radius: 12px;
}
.wrap-participant {
  height: 1268px;
  overflow: auto;
}
.disabled {
    cursor: auto !important;
}
.box {
    cursor: pointer;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin: 2px;
    width: 36px;
    height: 36px;
    line-height: 36px;
    text-align: center;
    color: #ddd;
}
.box.bg-danger,.box.bg-success,.box.disabled {
    color: #fff;
}
.box-nb {
    margin: 2px;
    width: 36px;
    height: 36px;
    line-height: 36px;
    text-align: center;
    font-weight: bold;
}
.box.filled {
    background-color: #28a745;
}
.loading-box-spinner {
    inset: 0px;
    margin: auto;
    width: 100%;
    height: 100%;
    background-color: #ffffffab;
    z-index: 999;
    border-radius: 12px;
}
</style>
