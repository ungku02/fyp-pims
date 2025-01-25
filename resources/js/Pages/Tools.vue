<script setup>
import { defineProps, computed, watch } from 'vue';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';
</script>

<template>
    <ProjectLayout>
  <div>
    <h1>Team Tools</h1>
    <ul>
      <li><a href="https://teams.microsoft.com" target="_blank">Microsoft Teams</a></li>
      <li><a href="https://www.office.com/launch/word" target="_blank">Microsoft Word</a></li>
      <li><a href="https://www.office.com/launch/excel" target="_blank">Microsoft Excel</a></li>
      <li><a href="https://www.office.com/launch/powerpoint" target="_blank">Microsoft PowerPoint</a></li>
      <li><a href="https://outlook.office.com" target="_blank">Microsoft Outlook</a></li>
    </ul>
  </div>
  </ProjectLayout>
</template>


<style scoped>
/* Add any styles for your tools page here */
</style>
