<script>
export default {
  data() {
    return {
      semesters: [],
      loading: true,
      error: null
    }
  },
  mounted() {
    fetch('http://localhost:8080/semesters')
      .then(res => res.json())
      .then(json => {
        if(json.status === 'success') {
          this.semesters = json.data
        } else {
          this.error = json.error || 'erreur lors de la récupération'
        }
      })
      .catch(err => this.error = err.message)
      .finally(() => this.loading = false)
  }
}
</script>

<template>
  <div class="semesters-wrapper">
    <div class="semesters-container">
      <div class="page-header">
        <h1>Semestres disponibles</h1>
        <p class="subtitle">Sélectionnez un semestre pour voir les étudiants</p>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Chargement des semestres...</p>
      </div>

      <div v-if="error" class="error-state">
        <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
        </svg>
        <p>{{ error }}</p>
      </div>

      <div v-if="!loading && !error" class="semesters-content">
        <div v-if="semesters.length > 0" class="semesters-grid">
          <router-link 
            v-for="semester in semesters" 
            :key="semester.idSemester"
            :to="`/students/${semester.idSemester}`"
            class="semester-card"
          >
            <div class="card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" stroke-width="2"/>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke-width="2"/>
              </svg>
            </div>
            <div class="card-content">
              <h3 class="semester-year">{{ semester.yearName }}</h3>
              <p class="semester-name">{{ semester.semesterName }}</p>
            </div>
            <div class="card-arrow">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="9 18 15 12 9 6" stroke-width="2"/>
              </svg>
            </div>
          </router-link>
        </div>

        <div v-else class="empty-state">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <line x1="8" y1="12" x2="16" y2="12" stroke-width="2"/>
          </svg>
          <h3>Aucun semestre disponible</h3>
          <p>Il n'y a pas de semestres à afficher pour le moment</p>
          <slot name="no-semesters"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.semesters-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 40px 20px;
}

.semesters-container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  text-align: center;
  margin-bottom: 48px;
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

.page-header h1 {
  margin: 0 0 12px 0;
  font-size: 36px;
  font-weight: 700;
  color: #2d3748;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  margin: 0;
  font-size: 16px;
  color: #718096;
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

/* Semesters Grid */
.semesters-content {
  animation: fadeIn 0.6s ease-out;
}

.semesters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
}

.semester-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 24px;
  background: #ffffff;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
  text-decoration: none;
  transition: all 0.3s ease;
  cursor: pointer;
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.semester-card:nth-child(1) { animation-delay: 0.1s; }
.semester-card:nth-child(2) { animation-delay: 0.2s; }
.semester-card:nth-child(3) { animation-delay: 0.3s; }
.semester-card:nth-child(4) { animation-delay: 0.4s; }
.semester-card:nth-child(5) { animation-delay: 0.5s; }

.semester-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(102, 126, 234, 0.2);
  border-color: #667eea;
}

.card-icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  color: #ffffff;
}

.card-icon svg {
  width: 24px;
  height: 24px;
}

.card-content {
  flex: 1;
}

.semester-year {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: 700;
  color: #2d3748;
}

.semester-name {
  margin: 0;
  font-size: 14px;
  color: #718096;
}

.card-arrow {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  color: #a0aec0;
  transition: transform 0.3s ease;
}

.semester-card:hover .card-arrow {
  transform: translateX(4px);
  color: #667eea;
}

.card-arrow svg {
  width: 100%;
  height: 100%;
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
  .semesters-wrapper {
    padding: 24px 16px;
  }

  .page-header h1 {
    font-size: 28px;
  }

  .semesters-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .semester-card {
    padding: 20px;
  }
}
</style>