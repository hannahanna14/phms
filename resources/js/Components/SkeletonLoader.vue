<template>
    <div class="skeleton-loader" :class="[type]">
        <!-- Table Skeleton -->
        <div v-if="type === 'table'" class="skeleton-table" :style="{ '--columns': columns }">
            <div class="skeleton-table-header">
                <div v-for="i in columns" :key="i" class="skeleton-cell skeleton-header-cell"></div>
            </div>
            <div v-for="row in rows" :key="row" class="skeleton-table-row">
                <div v-for="i in columns" :key="i" class="skeleton-cell"></div>
            </div>
        </div>

        <!-- Card Skeleton -->
        <div v-else-if="type === 'card'" class="skeleton-card">
            <div class="skeleton-card-content">
                <div v-for="i in lines" :key="i" class="skeleton-line" :style="{ width: getLineWidth(i) }"></div>
            </div>
        </div>

        <!-- List Skeleton -->
        <div v-else-if="type === 'list'" class="skeleton-list">
            <div v-for="i in items" :key="i" class="skeleton-list-item">
                <div class="skeleton-avatar"></div>
                <div class="skeleton-list-content">
                    <div class="skeleton-line skeleton-title"></div>
                    <div class="skeleton-line skeleton-subtitle"></div>
                </div>
            </div>
        </div>

        <!-- Dashboard Card Skeleton -->
        <div v-else-if="type === 'dashboard-card'" class="skeleton-dashboard-card">
            <div class="skeleton-dashboard-icon"></div>
            <div class="skeleton-dashboard-content">
                <div class="skeleton-line skeleton-dashboard-title"></div>
                <div class="skeleton-line skeleton-dashboard-value"></div>
            </div>
        </div>

        <!-- Form Skeleton -->
        <div v-else-if="type === 'form'" class="skeleton-form">
            <div v-for="i in fields" :key="i" class="skeleton-form-field">
                <div class="skeleton-line skeleton-label"></div>
                <div class="skeleton-line skeleton-input"></div>
            </div>
        </div>

        <!-- Health Report Skeleton -->
        <div v-else-if="type === 'health-report'" class="skeleton-health-report">
            <!-- Header -->
            <div class="skeleton-health-header">
                <div class="skeleton-line skeleton-header-title"></div>
                <div class="skeleton-line skeleton-header-subtitle"></div>
                <div class="skeleton-guide-card">
                    <div class="skeleton-line skeleton-guide-title"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                </div>
            </div>

            <!-- Step 1: Student Selection -->
            <div class="skeleton-health-step">
                <div class="skeleton-step-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-step-title"></div>
                </div>
                <div class="skeleton-student-grid">
                    <div class="skeleton-student-card">
                        <div class="skeleton-line skeleton-card-title"></div>
                        <div class="skeleton-line skeleton-search-input"></div>
                        <div class="skeleton-line skeleton-student-item"></div>
                        <div class="skeleton-line skeleton-student-item"></div>
                        <div class="skeleton-line skeleton-student-item"></div>
                    </div>
                    <div class="skeleton-student-card">
                        <div class="skeleton-line skeleton-card-title"></div>
                        <div class="skeleton-line skeleton-form-field"></div>
                        <div class="skeleton-line skeleton-form-field"></div>
                        <div class="skeleton-line skeleton-form-field"></div>
                    </div>
                </div>
                <div class="skeleton-sort-section">
                    <div class="skeleton-line skeleton-sort-label"></div>
                    <div class="skeleton-line skeleton-sort-select"></div>
                </div>
            </div>

            <!-- Step 2: Health Fields -->
            <div class="skeleton-health-step">
                <div class="skeleton-step-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-step-title"></div>
                </div>
                <div class="skeleton-field-grid">
                    <div v-for="i in 6" :key="i" class="skeleton-field-button"></div>
                </div>
                <div class="skeleton-field-status">
                    <div class="skeleton-line skeleton-status-text"></div>
                </div>
            </div>

            <!-- Step 3: Generate Report -->
            <div class="skeleton-health-step">
                <div class="skeleton-generate-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-generate-title"></div>
                    <div class="skeleton-generate-button"></div>
                </div>
                <div class="skeleton-summary-grid">
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oral Health Report Skeleton -->
        <div v-else-if="type === 'oral-health-report'" class="skeleton-oral-health-report">
            <!-- Header -->
            <div class="skeleton-health-header">
                <div class="skeleton-line skeleton-header-title"></div>
                <div class="skeleton-line skeleton-header-subtitle"></div>
                <div class="skeleton-guide-card">
                    <div class="skeleton-line skeleton-guide-title"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                    <div class="skeleton-line skeleton-guide-step"></div>
                </div>
            </div>

            <!-- Student Selection (same as health report) -->
            <div class="skeleton-health-step">
                <div class="skeleton-step-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-step-title"></div>
                </div>
                <div class="skeleton-student-grid">
                    <div class="skeleton-student-card">
                        <div class="skeleton-line skeleton-card-title"></div>
                        <div class="skeleton-line skeleton-search-input"></div>
                        <div class="skeleton-line skeleton-student-item"></div>
                        <div class="skeleton-line skeleton-student-item"></div>
                    </div>
                    <div class="skeleton-student-card">
                        <div class="skeleton-line skeleton-card-title"></div>
                        <div class="skeleton-line skeleton-form-field"></div>
                        <div class="skeleton-line skeleton-form-field"></div>
                    </div>
                </div>
            </div>

            <!-- Oral Health Fields with Tabs -->
            <div class="skeleton-health-step">
                <div class="skeleton-step-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-step-title"></div>
                </div>
                <div class="skeleton-tab-header">
                    <div class="skeleton-tab"></div>
                    <div class="skeleton-tab"></div>
                </div>
                <div class="skeleton-oral-fields">
                    <div v-for="i in 5" :key="i" class="skeleton-oral-field-card">
                        <div class="skeleton-line skeleton-oral-field-label"></div>
                        <div class="skeleton-range-slider">
                            <div class="skeleton-slider-track"></div>
                            <div class="skeleton-slider-values">
                                <div class="skeleton-line skeleton-slider-value"></div>
                                <div class="skeleton-line skeleton-slider-value"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-field-status">
                    <div class="skeleton-line skeleton-status-text"></div>
                </div>
            </div>

            <!-- Generate Section -->
            <div class="skeleton-health-step">
                <div class="skeleton-generate-header">
                    <div class="skeleton-step-number"></div>
                    <div class="skeleton-line skeleton-generate-title"></div>
                    <div class="skeleton-generate-button"></div>
                </div>
                <div class="skeleton-summary-grid">
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                    <div class="skeleton-summary-card">
                        <div class="skeleton-line skeleton-summary-label"></div>
                        <div class="skeleton-line skeleton-summary-value"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Skeleton -->
        <div v-else-if="type === 'calendar'" class="skeleton-calendar">
            <!-- Header -->
            <div class="skeleton-calendar-header">
                <div class="skeleton-line skeleton-calendar-title"></div>
                <div class="skeleton-calendar-button"></div>
            </div>

            <!-- Filters -->
            <div class="skeleton-calendar-filters">
                <div class="skeleton-line skeleton-filter"></div>
                <div class="skeleton-line skeleton-filter"></div>
                <div class="skeleton-line skeleton-filter"></div>
                <div class="skeleton-line skeleton-filter"></div>
            </div>

            <!-- Calendar and Sidebar Grid -->
            <div class="skeleton-calendar-grid">
                <!-- Calendar -->
                <div class="skeleton-calendar-main">
                    <div class="skeleton-calendar-nav">
                        <div class="skeleton-line skeleton-nav-item"></div>
                        <div class="skeleton-line skeleton-nav-title"></div>
                        <div class="skeleton-line skeleton-nav-item"></div>
                    </div>
                    <div class="skeleton-calendar-weekdays">
                        <div v-for="i in 7" :key="i" class="skeleton-weekday"></div>
                    </div>
                    <div class="skeleton-calendar-days">
                        <div v-for="i in 35" :key="i" class="skeleton-calendar-day"></div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="skeleton-calendar-sidebar">
                    <!-- Today's Schedules -->
                    <div class="skeleton-sidebar-card">
                        <div class="skeleton-line skeleton-sidebar-title"></div>
                        <div v-for="i in 3" :key="i" class="skeleton-schedule-item">
                            <div class="skeleton-line skeleton-schedule-title"></div>
                            <div class="skeleton-line skeleton-schedule-time"></div>
                            <div class="skeleton-tag"></div>
                        </div>
                    </div>

                    <!-- Upcoming Schedules -->
                    <div class="skeleton-sidebar-card">
                        <div class="skeleton-line skeleton-sidebar-title"></div>
                        <div v-for="i in 2" :key="i" class="skeleton-schedule-item">
                            <div class="skeleton-line skeleton-schedule-title"></div>
                            <div class="skeleton-line skeleton-schedule-date"></div>
                            <div class="skeleton-tag"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Skeleton -->
        <div v-else-if="type === 'messages'" class="skeleton-messages">
            <!-- Header -->
            <div class="skeleton-messages-header">
                <div class="skeleton-line skeleton-messages-title"></div>
                <div class="skeleton-messages-button"></div>
            </div>

            <!-- Tabs -->
            <div class="skeleton-messages-tabs">
                <div class="skeleton-tab active"></div>
                <div class="skeleton-tab"></div>
                <div class="skeleton-tab"></div>
                <div class="skeleton-tab"></div>
            </div>

            <!-- Messages List -->
            <div class="skeleton-messages-list">
                <div v-for="i in 5" :key="i" class="skeleton-message-item">
                    <div class="skeleton-message-avatar"></div>
                    <div class="skeleton-message-content">
                        <div class="skeleton-message-header">
                            <div class="skeleton-line skeleton-message-sender"></div>
                            <div class="skeleton-line skeleton-message-time"></div>
                        </div>
                        <div class="skeleton-line skeleton-message-subject"></div>
                        <div class="skeleton-line skeleton-message-preview"></div>
                        <div class="skeleton-line skeleton-message-preview"></div>
                    </div>
                    <div class="skeleton-message-meta">
                        <div class="skeleton-tag"></div>
                        <div class="skeleton-tag"></div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="skeleton-pagination">
                <div class="skeleton-line skeleton-pagination-controls"></div>
            </div>
        </div>

        <!-- Generic Skeleton -->
        <div v-else class="skeleton-generic">
            <div v-for="i in lines" :key="i" class="skeleton-line" :style="{ width: getLineWidth(i) }"></div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'generic',
        validator: (value) => ['table', 'card', 'list', 'dashboard-card', 'form', 'health-report', 'oral-health-report', 'calendar', 'messages', 'generic'].includes(value)
    },
    rows: {
        type: Number,
        default: 5
    },
    columns: {
        type: Number,
        default: 4
    },
    lines: {
        type: Number,
        default: 3
    },
    items: {
        type: Number,
        default: 5
    },
    fields: {
        type: Number,
        default: 4
    }
})

const getLineWidth = (index) => {
    const widths = ['100%', '90%', '75%', '85%', '95%']
    return widths[index % widths.length]
}
</script>

<style scoped>
.skeleton-loader {
    width: 100%;
}

/* Base Skeleton Styles */
.skeleton-line,
.skeleton-cell,
.skeleton-header-cell,
.skeleton-card-content,
.skeleton-avatar,
.skeleton-dashboard-icon,
.skeleton-dashboard-content,
.skeleton-label,
.skeleton-input {
    background-color: #f0f0f0;
    border-radius: 4px;
    box-sizing: border-box;
    flex-shrink: 0;
}


/* Table Skeleton */
.skeleton-table {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.skeleton-table-header {
    display: grid;
    grid-template-columns: repeat(var(--columns, 4), 1fr);
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
    min-width: 0;
}

.skeleton-table-row {
    display: grid;
    grid-template-columns: repeat(var(--columns, 4), 1fr);
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    min-width: 0;
}

.skeleton-table-row:last-child {
    border-bottom: none;
}

.skeleton-cell {
    height: 20px;
    min-width: 0;
    width: 100%;
}

.skeleton-header-cell {
    height: 24px;
    background: #e5e7eb;
    min-width: 0;
    width: 100%;
}

/* Card Skeleton */
.skeleton-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
}

.skeleton-card-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.skeleton-line {
    height: 16px;
    margin-bottom: 0.5rem;
    min-width: 0;
    flex-shrink: 0;
}

.skeleton-title {
    height: 20px;
    width: 80%;
}

.skeleton-subtitle {
    height: 14px;
    width: 60%;
}

/* List Skeleton */
.skeleton-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.skeleton-list-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
}

.skeleton-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    flex-shrink: 0;
}

.skeleton-list-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Dashboard Card Skeleton */
.skeleton-dashboard-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
}

.skeleton-dashboard-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    flex-shrink: 0;
}

.skeleton-dashboard-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-dashboard-title {
    height: 16px;
    width: 60%;
}

.skeleton-dashboard-value {
    height: 32px;
    width: 40%;
}

/* Form Skeleton */
.skeleton-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.skeleton-form-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-label {
    height: 16px;
    width: 30%;
}

.skeleton-input {
    height: 40px;
    width: 100%;
}

/* Health Report Skeleton */
.skeleton-health-report {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.skeleton-health-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.skeleton-header-title {
    height: 32px;
    width: 60%;
}

.skeleton-header-subtitle {
    height: 20px;
    width: 45%;
}

.skeleton-guide-card {
    padding: 1rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-guide-title {
    height: 18px;
    width: 70%;
}

.skeleton-guide-step {
    height: 16px;
    width: 85%;
}

.skeleton-health-step {
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.skeleton-step-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.skeleton-step-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
}

.skeleton-step-title {
    height: 24px;
    width: 40%;
}

.skeleton-student-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.skeleton-student-card {
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.skeleton-card-title {
    height: 18px;
    width: 70%;
}

.skeleton-search-input {
    height: 40px;
    width: 100%;
}

.skeleton-student-item {
    height: 16px;
    width: 90%;
}

.skeleton-form-field {
    height: 40px;
    width: 100%;
}

.skeleton-sort-section {
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.skeleton-sort-label {
    height: 16px;
    width: 30%;
}

.skeleton-sort-select {
    height: 40px;
    width: 40%;
}

.skeleton-field-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
}

.skeleton-field-button {
    height: 36px;
    width: 100%;
    border-radius: 6px;
}

.skeleton-field-status {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 6px;
}

.skeleton-status-text {
    height: 16px;
    width: 60%;
}

.skeleton-generate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.skeleton-generate-title {
    height: 24px;
    width: 35%;
    flex: 1;
    margin: 0 1rem;
}

.skeleton-generate-button {
    height: 44px;
    width: 200px;
    border-radius: 8px;
}

.skeleton-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.skeleton-summary-card {
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-summary-label {
    height: 12px;
    width: 50%;
}

.skeleton-summary-value {
    height: 20px;
    width: 70%;
}

/* Oral Health Report Skeleton */
.skeleton-oral-health-report {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.skeleton-tab-header {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.skeleton-tab {
    height: 36px;
    width: 120px;
    border-radius: 6px;
}

.skeleton-oral-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 1rem;
}

.skeleton-oral-field-card {
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.skeleton-oral-field-label {
    height: 16px;
    width: 60%;
}

.skeleton-range-slider {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-slider-track {
    height: 6px;
    width: 100%;
    border-radius: 3px;
}

.skeleton-slider-values {
    display: flex;
    justify-content: space-between;
}

.skeleton-slider-value {
    height: 12px;
    width: 30px;
}

/* Calendar Skeleton */
.skeleton-calendar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.skeleton-calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.skeleton-calendar-title {
    height: 32px;
    width: 30%;
}

.skeleton-calendar-button {
    height: 40px;
    width: 140px;
    border-radius: 6px;
}

.skeleton-calendar-filters {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.skeleton-filter {
    height: 40px;
    width: 100%;
}

.skeleton-calendar-grid {
    display: grid;
    grid-template-columns: 3fr 1fr;
    gap: 1.5rem;
}

.skeleton-calendar-main {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.skeleton-calendar-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
}

.skeleton-nav-item {
    height: 24px;
    width: 24px;
    border-radius: 4px;
}

.skeleton-nav-title {
    height: 24px;
    width: 200px;
}

.skeleton-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    padding: 0 1rem;
}

.skeleton-weekday {
    height: 32px;
    width: 100%;
    border-radius: 4px;
}

.skeleton-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    padding: 0 1rem;
}

.skeleton-calendar-day {
    height: 100px;
    width: 100%;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

.skeleton-calendar-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.skeleton-sidebar-card {
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.skeleton-sidebar-title {
    height: 20px;
    width: 60%;
}

.skeleton-schedule-item {
    padding: 0.75rem;
    background: white;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-schedule-title {
    height: 16px;
    width: 80%;
}

.skeleton-schedule-time {
    height: 14px;
    width: 50%;
}

.skeleton-schedule-date {
    height: 14px;
    width: 60%;
}

.skeleton-tag {
    height: 20px;
    width: 60px;
    border-radius: 12px;
    align-self: flex-start;
}

/* Messages Skeleton */
.skeleton-messages {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.skeleton-messages-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.skeleton-messages-title {
    height: 32px;
    width: 25%;
}

.skeleton-messages-button {
    height: 40px;
    width: 130px;
    border-radius: 6px;
}

.skeleton-messages-tabs {
    display: flex;
    gap: 0.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.skeleton-tab {
    height: 36px;
    width: 100px;
    border-radius: 6px;
    opacity: 0.6;
}

.skeleton-tab.active {
    opacity: 1;
}

.skeleton-messages-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-message-item {
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.skeleton-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
}

.skeleton-message-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.skeleton-message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.skeleton-message-sender {
    height: 16px;
    width: 40%;
}

.skeleton-message-time {
    height: 12px;
    width: 25%;
}

.skeleton-message-subject {
    height: 14px;
    width: 70%;
}

.skeleton-message-preview {
    height: 12px;
    width: 90%;
}

.skeleton-message-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
    min-width: 80px;
}

.skeleton-pagination {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

.skeleton-pagination-controls {
    height: 40px;
    width: 300px;
}

/* Generic Skeleton */
.skeleton-generic {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
</style>

