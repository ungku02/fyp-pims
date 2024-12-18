<template>
  <div class="calendar-container d-flex justify-content-center">
    <!-- Header: Month and Year -->
    <div class="calendar-header d-flex justify-content-between align-items-center">
      <button class="btn btn-light btn-sm" @click="previousMonth">&lt;</button>
      <h6 class="text-center m-0">{{ currentMonth }} {{ currentYear }}</h6>
      <button class="btn btn-light btn-sm" @click="nextMonth">&gt;</button>
    </div>

    <!-- Days of the Week -->
    <div class="calendar-days d-flex">
      <div v-for="day in daysOfWeek" :key="day" class="day-name">
        {{ day }}
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
      <div
        v-for="(day, index) in calendarDays"
        :key="index"
        class="calendar-day"
        :class="{ 'current-day': isToday(day), 'empty-day': !day }"
      >
        {{ day }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

// Month and Year state
const currentMonthIndex = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

// Days of the week
const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// Computed property for current month name
const currentMonth = computed(() => {
  return new Date(currentYear.value, currentMonthIndex.value).toLocaleString("default", {
    month: "long",
  });
});

// Helper: Get days in the month
const getDaysInMonth = (month, year) => {
  return new Date(year, month + 1, 0).getDate();
};

// Computed property to generate calendar days
const calendarDays = computed(() => {
  const firstDayOfMonth = new Date(currentYear.value, currentMonthIndex.value, 1).getDay();
  const daysInMonth = getDaysInMonth(currentMonthIndex.value, currentYear.value);
  const days = Array(firstDayOfMonth).fill(null); // Fill empty slots before the 1st

  for (let i = 1; i <= daysInMonth; i++) {
    days.push(i);
  }

  return days;
});

// Navigate to previous month
const previousMonth = () => {
  if (currentMonthIndex.value === 0) {
    currentMonthIndex.value = 11;
    currentYear.value--;
  } else {
    currentMonthIndex.value--;
  }
};

// Navigate to next month
const nextMonth = () => {
  if (currentMonthIndex.value === 11) {
    currentMonthIndex.value = 0;
    currentYear.value++;
  } else {
    currentMonthIndex.value++;
  }
};

// Check if a day is today
const isToday = (day) => {
  const today = new Date();
  return (
    day &&
    day === today.getDate() &&
    currentMonthIndex.value === today.getMonth() &&
    currentYear.value === today.getFullYear()
  );
};
</script>

<style scoped>
.calendar-container {
  display: flex;
  flex-direction: column;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  width: 220px; /* Reduced width */
  font-size: 12px; /* Smaller text size */
}

.calendar-header {
  margin-bottom: 8px;
}

.calendar-days,
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px; /* Reduced gap */
}

.day-name {
  text-align: center;
  font-weight: bold;
  font-size: 10px; /* Smaller day labels */
}

.calendar-day {
  text-align: center;
  padding: 6px; /* Smaller padding */
  border-radius: 4px;
  background-color: #f9f9f9;
  font-size: 10px;
}

.calendar-day.current-day {
  background-color: #e2ebf3;
  font-weight: bold;
  border: 1px solid #2a4965;
}

.calendar-day.empty-day {
  visibility: hidden;
}
</style>
