// Importation de createRouter et createWebHistory de vue-router et Home de ./components/Home.vue
import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/private/Home/Home.vue';
import Register from './components/public/Register/Register.vue';
import Login from './components/public/Login/Login.vue';
import Logout from './components/private/Logout/Logout.vue';
import Terrains from "./components/private/Terrain/Terrains.vue";
import InfosMembre from "./components/private/Membre/InfosMembre.vue";
import UpdateMembre from "./components/private/Membre/UpdateMembre.vue";
import ChangePassword from "./components/private/Membre/ChangePassword.vue";
import AfficherListeMembreAdmin from "./components/private/Membre/AfficherListeMembreAdmin.vue";
import firstMDP from "./components/private/Membre/FirstMDP.vue";
import listeMembre from "./components/private/Membre/ListeMembre.vue";
import Reservations from "./components/private/Reservations/Reservations.vue";

// Création des routes pour l'application
const routes = [
    {
        path: '/home',
        name: 'home',
        component: Home,
        beforeEnter: requireAuth,
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
    },
    {
        path: '/updateMembre',
        name: 'updateMembre',
        component: UpdateMembre,
        beforeEnter: requireAuth,
    },
    {
        path: '/infosMembre',
        name: 'infosMembre',
        component: InfosMembre,
        beforeEnter: requireAuth,
    },
    {
        path: '/listeMembreAdmin',
        name: 'afficherListeMembreAdmin',
        component: AfficherListeMembreAdmin,
        beforeEnter: requireAuth,
    },
    {
        path: '/firstMDP',
        name: 'firstMDP',
        component: firstMDP,
        beforeEnter: requireAuth,
    },
    {
        path: '/changePassword',
        name: 'ChangePassword',
        component: ChangePassword,
        beforeEnter: requireAuth,
    },
    {
        path: '/',
        name: 'login',
        component: Login,
    },
    {
        path: '/logout',
        name: 'logout',
        component: Logout,
        beforeEnter: requireAuth, // Ajout d'un guard (garde) qui vérifie si l'utilisateur est authentifié avant d'autoriser l'accès à la page
    },
    {
        path: '/terrains',
        name: 'Terrains',
        component: Terrains,
        beforeEnter: requireAuth,
    },
    {
        path: '/reservations',
        name: 'Reservations',
        component: Reservations,
        beforeEnter: requireAuth,
    },
    {
        path: '/listeMembre',
        name: 'listeMembre',
        component: listeMembre,
        beforeEnter: requireAuth,
    },

];

// Création de l'instance du router
const router = createRouter({
    // Création de l'historique de navigation
    history: createWebHistory(),
    // Ajout des routes
    routes,
});

// Définition d'une fonction requireAuth qui vérifie si l'utilisateur est authentifié
function requireAuth(to, from, next) {
    const token = localStorage.getItem("authToken");

    if (token) {
        next();
    } else {
        next("/"); // Redirection vers la page de connexion si l'utilisateur n'est pas authentifié
    }
}

// Exportation de l'instance du router
export default router;
