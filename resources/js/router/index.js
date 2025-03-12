import { createRouter, createWebHistory } from 'vue-router';
import HealthExamination from '../components/HealthExamination.vue';

const routes = [
    {
        path: '/health-examination',
        name: 'HealthExamination',
        component: HealthExamination
    },
    {
        path: '/health-examination/:id',
        name: 'HealthExam.Show',
        component: () => import('../Pages/HealthExam/Show.vue')
    },
    {
        path: '/health-examination/create/:studentId',
        name: 'HealthExamination.Create',
        component: () => import('../Pages/HealthExamination/Create.vue')
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
