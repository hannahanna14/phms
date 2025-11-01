<template>
  <div class="health-examination">
    <div class="search-container">
      <input 
        type="text" 
        v-model="searchQuery" 
        placeholder="Search" 
        class="search-input"
      >
    </div>
    
    <div class="students-list">
      <div v-for="student in filteredStudents" :key="student.id" class="student-item">
        <div class="student-name">{{ student.full_name }}</div>
        <div class="student-details">
          <span>Sex: {{ student.sex }}</span>
          <span>Age: {{ student.age }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Import component styles
import '../../css/components/HealthExamination.css'

export default {
  name: 'HealthExamination',
  data() {
    return {
      students: [],
      searchQuery: ''
    }
  },
  computed: {
    filteredStudents() {
      return this.students.filter(student => 
        student.full_name.toLowerCase().includes(this.searchQuery.toLowerCase())
      )
    }
  },
  mounted() {
    this.fetchStudents()
  },
  methods: {
    async fetchStudents() {
      try {
        const response = await axios.get('/api/students')
        this.students = response.data
      } catch (error) {
        console.error('Error fetching students:', error)
      }
    }
  }
}
</script>

