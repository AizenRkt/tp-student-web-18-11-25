<script>
export default {
  props: ['idSemester'],
  data() {
    return {
      students: [],
      loading: true,
      error: null,
      year: new Date().getFullYear(),
      showModal: false,
      selectedStudent: null,
      releve: null,
      loadingReleve: false,
      releveError: null
    }
  },
  methods: {
    getInitials(student) {
      const firstInitial = student.firstNames ? student.firstNames.charAt(0).toUpperCase() : '';
      const lastInitial = student.lastName ? student.lastName.charAt(0).toUpperCase() : '';
      return firstInitial + lastInitial;
    },
    fetchStudents() {
      this.loading = true;
      this.error = null;
      fetch(`http://localhost:8080/students/semester/${this.idSemester}/year/${this.year}`)
        .then(res => res.json())
        .then(json => {
          if(json.status === 'success') {
            this.students = json.data;
          } else {
            this.error = json.error || 'Erreur lors de la récupération';
          }
        })
        .catch(err => this.error = err.message)
        .finally(() => this.loading = false);
    },
    onYearChange(event) {
      this.year = event.target.value;
      this.fetchStudents();
    },
    async openReleve(student) {
      this.selectedStudent = student;
      this.showModal = true;
      this.loadingReleve = true;
      this.releveError = null;
      this.releve = null;

      try {
        const response = await fetch(`http://localhost:8080/students/${student.idStudent}/semester/${this.idSemester}/releve`);
        const json = await response.json();
        
        if(json.status === 'success') {
          this.releve = json.data;
        } else {
          this.releveError = json.error || 'Erreur lors de la récupération du relevé';
        }
      } catch(err) {
        this.releveError = err.message;
      } finally {
        this.loadingReleve = false;
      }
    },
    closeModal() {
      this.showModal = false;
      this.selectedStudent = null;
      this.releve = null;
      this.releveError = null;
    }
  },
  mounted() {
    this.fetchStudents();
  }
}
</script>

<template>
  <div class="students-wrapper">
    <div class="students-container">
      <!-- Back button -->
      <router-link to="/semesters" class="back-button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <polyline points="15 18 9 12 15 6" stroke-width="2"/>
        </svg>
        Retour aux semestres
      </router-link>

      <!-- Page Header -->
      <div class="page-header">
        <div class="header-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="2"/>
            <circle cx="9" cy="7" r="4" stroke-width="2"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-width="2"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="2"/>
          </svg>
        </div>
        <div class="header-content">
          <h1>Étudiants du semestre</h1>
          <div class="semester-filter">
            <span class="semester-badge">Semestre {{ idSemester }}</span>
            <select v-model="year" @change="onYearChange" class="year-select">
              <option v-for="y in 10" :key="y" :value="2020 + y">{{ 2020 + y }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Chargement des étudiants...</p>
      </div>

      <!-- Error State -->
      <div v-if="error" class="error-state">
        <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
        </svg>
        <p>{{ error }}</p>
      </div>

      <!-- Students Content -->
      <div v-if="!loading && !error" class="students-content">
        <div v-if="students.length > 0">
          <!-- Stats bar -->
          <div class="stats-bar">
            <div class="stat-item">
              <span class="stat-label">Total étudiants</span>
              <span class="stat-value">{{ students.length }}</span>
            </div>
          </div>

          <!-- Students Table -->
          <div class="table-container">
            <table class="students-table">
              <thead>
                <tr>
                  <th class="col-avatar"></th>
                  <th class="col-id">Numéro d'inscription</th>
                  <th class="col-name">Nom complet</th>
                  <th class="col-moyenne">Moyenne</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="(student, index) in students" 
                  :key="student.idStudent"
                  class="student-row"
                  :style="{ animationDelay: `${index * 0.03}s` }"
                >
                  <td class="col-avatar">
                    <div class="student-avatar">
                      <span>{{ getInitials(student) }}</span>
                    </div>
                  </td>
                  <td class="col-id">{{ student.registrationNumber }}</td>
                  <td class="col-name">
                    <span class="student-fullname">{{ student.lastName }} {{ student.firstNames }}</span>
                  </td>
                  <td class="col-moyenne">
                    <button 
                      @click="openReleve(student)" 
                      class="moyenne-button"
                      :class="{ 
                        'moyenne-pass': parseFloat(student.moyenne) >= 10,
                        'moyenne-fail': parseFloat(student.moyenne) < 10
                      }"
                      :title="`Cliquez pour voir le relevé de notes`"
                    >
                      {{ parseFloat(student.moyenne).toFixed(2) }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="2"/>
            <circle cx="9" cy="7" r="4" stroke-width="2"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-width="2"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="2"/>
          </svg>
          <h3>Aucun étudiant trouvé</h3>
          <p>Ce semestre ne contient pas encore d'étudiants</p>
        </div>
      </div>
    </div>

    <!-- Modal Relevé de Notes -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <div class="modal-title-section">
            <h2>Relevé de notes</h2>
            <p v-if="selectedStudent" class="student-name-modal">
              {{ selectedStudent.lastName }} {{ selectedStudent.firstNames }}
            </p>
          </div>
          <button @click="closeModal" class="close-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" stroke-width="2"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke-width="2"/>
            </svg>
          </button>
        </div>

        <div class="modal-body">
          <!-- Loading -->
          <div v-if="loadingReleve" class="modal-loading">
            <div class="spinner"></div>
            <p>Chargement du relevé...</p>
          </div>

          <!-- Error -->
          <div v-else-if="releveError" class="modal-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="12" cy="12" r="10" stroke-width="2"/>
              <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
              <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
            </svg>
            <p>{{ releveError }}</p>
          </div>

          <!-- Relevé Content -->
          <div v-else-if="releve" class="releve-content">
            <div class="moyenne-generale">
              <span class="moyenne-label">Moyenne générale</span>
              <span class="moyenne-value" :class="{ 'moyenne-value-pass': releve.moyenne >= 10, 'moyenne-value-fail': releve.moyenne < 10 }">
                {{ releve.moyenne.toFixed(2) }} / 20
              </span>
            </div>

            <div class="subjects-table-container">
              <table class="subjects-table">
                <thead>
                  <tr>
                    <th>Matière</th>
                    <th class="text-center">Crédits</th>
                    <th class="text-center">Note</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="subject in releve.releve" :key="subject.idSubject">
                    <td class="subject-name">{{ subject.subjectName }}</td>
                    <td class="text-center subject-credits">{{ subject.credits }}</td>
                    <td class="text-center">
                      <span 
                        class="grade-badge" 
                        :class="{ 
                          'grade-pass': parseFloat(subject.grade) >= 10,
                          'grade-fail': parseFloat(subject.grade) < 10 && parseFloat(subject.grade) > 0,
                          'grade-empty': parseFloat(subject.grade) === 0
                        }"
                      >
                        {{ parseFloat(subject.grade).toFixed(2) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.students-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 40px 20px;
}

.students-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Back Button */
.back-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  margin-bottom: 24px;
  background: #ffffff;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  color: #4a5568;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: #667eea;
  border-color: #667eea;
  color: #ffffff;
  transform: translateX(-4px);
}

.back-button svg {
  width: 20px;
  height: 20px;
}

/* Page Header */
.page-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 32px;
  padding: 32px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  animation: fadeInDown 0.6s ease-out;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.header-icon {
  flex-shrink: 0;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  color: #ffffff;
}

.header-icon svg {
  width: 32px;
  height: 32px;
}

.header-content {
  flex: 1;
}

.page-header h1 {
  margin: 0 0 12px 0;
  font-size: 32px;
  font-weight: 700;
  color: #2d3748;
}

.semester-filter {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.semester-badge {
  display: inline-block;
  padding: 6px 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
}

.year-select {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: #ffffff;
  color: #4a5568;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  outline: none;
}

.year-select:hover {
  border-color: #667eea;
}

.year-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e2e8f0;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #4a5568;
  font-size: 16px;
}

/* Error State */
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  background: #fff5f5;
  border-radius: 16px;
  border: 2px solid #feb2b2;
  animation: fadeIn 0.5s ease-out;
}

.error-icon {
  width: 64px;
  height: 64px;
  color: #e53e3e;
  margin-bottom: 16px;
}

.error-state p {
  margin: 0;
  color: #c53030;
  font-size: 16px;
  font-weight: 500;
}

/* Stats Bar */
.stats-bar {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  animation: fadeIn 0.6s ease-out;
}

.stat-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: #ffffff;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
}

.stat-label {
  font-size: 14px;
  color: #718096;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #667eea;
}

/* Table Container */
.table-container {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  animation: fadeIn 0.6s ease-out;
}

.students-table {
  width: 100%;
  border-collapse: collapse;
}

.students-table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
}

.students-table th {
  padding: 16px 20px;
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.col-avatar {
  width: 80px;
  text-align: center;
}

.col-id {
  width: 180px;
}

.col-name {
  flex: 1;
}

.col-moyenne {
  width: 140px;
  text-align: center;
}

.student-row {
  border-bottom: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.student-row:hover {
  background: #f7fafc;
}

.student-row:last-child {
  border-bottom: none;
}

.students-table td {
  padding: 16px 20px;
}

.student-avatar {
  width: 48px;
  height: 48px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  color: #ffffff;
  font-size: 16px;
  font-weight: 700;
}

.col-id {
  color: #718096;
  font-weight: 600;
  font-size: 14px;
}

.student-fullname {
  color: #2d3748;
  font-weight: 600;
  font-size: 15px;
}

.moyenne-button {
  padding: 8px 20px;
  border: none;
  border-radius: 20px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.moyenne-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.moyenne-pass {
  background: #c6f6d5;
  color: #22543d;
}

.moyenne-pass:hover {
  background: #9ae6b4;
}

.moyenne-fail {
  background: #fed7d7;
  color: #742a2a;
}

.moyenne-fail:hover {
  background: #fcb4b4;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  animation: fadeIn 0.3s ease-out;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: #ffffff;
  border-radius: 16px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 24px 32px;
  border-bottom: 2px solid #e2e8f0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
}

.modal-title-section h2 {
  margin: 0 0 4px 0;
  font-size: 24px;
  font-weight: 700;
}

.student-name-modal {
  margin: 0;
  font-size: 16px;
  opacity: 0.9;
}

.close-button {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #ffffff;
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.close-button svg {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 32px;
  overflow-y: auto;
}

.modal-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.modal-loading p {
  color: #4a5568;
  font-size: 16px;
}

.modal-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  color: #c53030;
}

.modal-error svg {
  width: 48px;
  height: 48px;
  margin-bottom: 12px;
}

.modal-error p {
  margin: 0;
  font-size: 16px;
}

.releve-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.moyenne-generale {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
  border-radius: 12px;
  border: 2px solid #e2e8f0;
}

.moyenne-label {
  font-size: 16px;
  font-weight: 600;
  color: #4a5568;
}

.moyenne-value {
  font-size: 32px;
  font-weight: 700;
  padding: 8px 20px;
  border-radius: 8px;
}

.moyenne-value-pass {
  background: #c6f6d5;
  color: #22543d;
}

.moyenne-value-fail {
  background: #fed7d7;
  color: #742a2a;
}

.subjects-table-container {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}

.subjects-table {
  width: 100%;
  border-collapse: collapse;
}

.subjects-table thead {
  background: #f7fafc;
}

.subjects-table th {
  padding: 14px 16px;
  text-align: left;
  font-size: 13px;
  font-weight: 700;
  color: #4a5568;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e2e8f0;
}

.subjects-table tbody tr {
  border-bottom: 1px solid #e2e8f0;
  transition: background 0.2s ease;
}

.subjects-table tbody tr:hover {
  background: #f7fafc;
}

.subjects-table tbody tr:last-child {
  border-bottom: none;
}

.subjects-table td {
  padding: 14px 16px;
}

.subject-name {
  font-weight: 500;
  color: #2d3748;
}

.subject-credits {
  color: #718096;
  font-weight: 600;
}

.text-center {
  text-align: center;
}

.grade-badge {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 14px;
}

.grade-pass {
  background: #c6f6d5;
  color: #22543d;
}

.grade-fail {
  background: #fed7d7;
  color: #742a2a;
}

.grade-empty {
  background: #e2e8f0;
  color: #718096;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  background: #ffffff;
  border-radius: 16px;
  border: 2px dashed #cbd5e0;
  animation: fadeIn 0.5s ease-out;
}

.empty-icon {
  width: 80px;
  height: 80px;
  color: #a0aec0;
  margin-bottom: 24px;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  font-size: 22px;
  font-weight: 600;
  color: #2d3748;
}

.empty-state p {
  margin: 0;
  font-size: 15px;
  color: #718096;
}

/* Responsive */
@media (max-width: 768px) {
  .students-wrapper {
    padding: 24px 16px;
  }

  .page-header {
    flex-direction: column;
    text-align: center;
    padding: 24px;
  }

  .page-header h1 {
    font-size: 24px;
  }

  .semester-filter {
    justify-content: center;
  }

  .stats-bar {
    flex-direction: column;
  }

  .table-container {
    overflow-x: auto;
  }

  .students-table th,
  .students-table td {
    padding: 12px 16px;
  }

  .col-avatar {
    width: 60px;
  }

  .col-id {
    width: 140px;
  }

  .student-avatar {
    width: 40px;
    height: 40px;
    font-size: 14px;
  }

  .modal-content {
    max-height: 95vh;
  }

  .modal-header {
    padding: 20px;
  }

  .modal-body {
    padding: 20px;
  }

  .moyenne-generale {
    flex-direction: column;
    gap: 12px;
    text-align: center;
  }

  .moyenne-value {
    font-size: 28px;
  }
}
</style>