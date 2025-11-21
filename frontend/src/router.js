import { createRouter, createWebHistory } from 'vue-router'
import SemesterList from './components/SemesterList.vue'
import StudentList from './components/StudentList.vue'
import Login from './components/Login.vue'

const routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/semesters',
        name: 'Semesters',
        component: SemesterList
    },
    {
        path: '/students/:idSemester',
        name: 'Students',
        component: StudentList,
        props: true
    }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
