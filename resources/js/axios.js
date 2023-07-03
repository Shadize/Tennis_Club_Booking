// Importation d'axios
import axios from 'axios';

// Création d'une instance axios
const instance = axios.create({
    // Définition de l'URL de base pour les requêtes
    baseURL: '/api',
    // Définition des en-têtes par défaut pour les requêtes
    headers: {
        'Content-Type': 'application/json',
    },
});

// Intercepteur de requêtes pour ajouter l'en-tête Authorization avec le token JWT
instance.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('authToken');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Exportation de l'instance axios
export default instance;
