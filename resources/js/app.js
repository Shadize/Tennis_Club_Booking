// Importation du fichier bootstrap, de createApp de vue, du composant App, du router et d'axios
import './bootstrap'; // Importation du fichier bootstrap qui contient les styles et les scripts de Bootstrap
import { createApp } from 'vue'; // Importation de la fonction createApp de Vue
import App from './components/App.vue'; // Importation du composant racine de l'application
import router from './router'; // Importation de l'instance du router
import axios from './axios'; // Importation de l'instance axios configurée
import Home from './components/private/Home/Home.vue'
import Banniere from "./components/common/Banniere/Banniere.vue";

// Création d'une instance d'application Vue
const app = createApp(App);
const banniere = createApp(Banniere);

// Configuration de l'instance pour l'utilisation d'axios
app.config.globalProperties.$axios = axios;
banniere.config.globalProperties.$axios = axios;

// Utilisation du router et montage de l'application Vue
app.use(router).mount('#app');
banniere.use(router).mount('#banniere');
