import { createRouter, createWebHistory } from 'vue-router';
import HealthExamination from '../components/HealthExamination.vue';

const routes = [
    {
        path: '/health-examination',
        name: 'HealthExamination',
        component: HealthExamination
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
