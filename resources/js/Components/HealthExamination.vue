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

<style scoped>
.health-examination {
  padding: 20px;
}

.search-container {
  margin-bottom: 20px;
}

.search-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.students-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.student-item {
  padding: 15px;
  border: 1px solid #eee;
  border-radius: 4px;
  background-color: white;
}

.student-name {
  font-weight: bold;
  margin-bottom: 5px;
}

.student-details {
  display: flex;
  gap: 15px;
  color: #666;
  font-size: 14px;
}
</style>
