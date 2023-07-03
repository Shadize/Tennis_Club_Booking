<template>
    <div class="terrain-list">
        <div class="header">
            <h1>Liste des Terrains</h1>
            <button class="terrain-button" @click="afficherTerrainCreateDialog()">
                Créer un terrain
            </button>
        </div>
        <table class="terrain-table">
            <thead>
                <tr>
                    <th>ID du terrain</th>
                    <th>Date de création</th>
                    <th class="action-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="terrain in terrains" :key="terrain.id">
                    <td>{{ terrain.id }}</td>
                    <td>{{ formatDate(terrain.created_at) }}</td>
                    <td class="action-button-container">
                        <button @click="afficherSuppressionTerrainDialog(terrain)" class="supprimer-button">Supprimer</button>
                        <button @click="afficherBlocageTerrainDialog(terrain)" class="bloquer-button" :disabled="loading">
                            Bloquer
                        </button>
                        <button @click="afficherHistoryTerrainDialog(terrain)" class="history-button">Voir les blocages</button>
                        <button @click="afficherHistoryReservationsTerrainDialog(terrain)" class="history-reservations-button">Voir les réservations</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Composant de dialogue de confirmation pour la suppression -->
        <SuppressionTerrainDialog ref="SuppressionTerrainDialog" @supprimer="fetchTerrains" />
        <!-- Composant de dialogue de confirmation pour le blocage/déblocage -->
        <BlocageTerrainDialog ref="BlocageTerrainDialog" />
        <!-- Composant de formulaire de création d'un terrain -->
        <TerrainCreateDialog ref="TerrainCreateDialog" @ajouter="fetchTerrains" />
        <HistoryTerrainDialog ref="HistoryTerrainDialog" />
        <HistoryReservationsTerrainDialog ref="HistoryReservationsTerrainDialog" />
    </div>
</template>


<script>
import SuppressionTerrainDialog from "./Dialogs/SuppressionTerrainDialog.vue";
import BlocageTerrainDialog from "./Dialogs/BlocageTerrainDialog.vue";
import TerrainCreateDialog from "./Dialogs/TerrainCreateDialog.vue";
import HistoryTerrainDialog from "./Dialogs/HistoriqueTerrainDialog.vue";
import HistoryReservationsTerrainDialog from "./Dialogs/HistoriqueReservationTerrainDialog.vue";

import moment from 'moment/min/moment-with-locales';
import 'moment/locale/fr';
moment.locale('fr'); 

export default {
    components: {
        SuppressionTerrainDialog,
        BlocageTerrainDialog,
        TerrainCreateDialog,
        HistoryTerrainDialog,
        HistoryReservationsTerrainDialog,
    },
    data() {
        return {
            terrains: [], // Tableau pour stocker les terrains récupérés depuis l'API
            loading: false, // Indicateur pour le chargement des requêtes
            role: null, // Sert à voir le rôle du membre si il est admin ou non 
        };
    },

    created(){
        this.checkrole();
    },
    mounted() {
        // Appel à l'API pour récupérer les terrains lors du montage du composant
        this.fetchTerrains();
    },
    methods: {
        // Récupère les terrains depuis l'API
        fetchTerrains() {
            this.$axios
                .get("/terrains", { params: { with: 'blockers' } })
                .then((response) => {
                    this.terrains = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        async checkrole() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.role = response.data.rôle;
                    });
            }
            catch (error) {
                console.error("Erreur lors de la récupération du user :", error);
            }
            if(this.role!== 'admin')
            {
                this.$router.push("/home");
            }
        },

        // Affiche le dialogue de confirmation pour bloquer/débloquer un terrain
        afficherBlocageTerrainDialog(terrain) {
            this.$refs.BlocageTerrainDialog.afficher(terrain);
        },

        // Affiche le dialogue de confirmation pour la suppression d'un terrain
        afficherSuppressionTerrainDialog(terrain) {
            this.$refs.SuppressionTerrainDialog.afficher(terrain);
        },

        // Affiche le formulaire de création d'un terrain
        afficherTerrainCreateDialog() {
            this.$refs.TerrainCreateDialog.afficher();
        },

        // Affiche le dialogue d'historique des blocages pour un terrain
        afficherHistoryTerrainDialog(terrain) {
            this.$refs.HistoryTerrainDialog.afficher(terrain);
        },

        afficherHistoryReservationsTerrainDialog(terrain){
            this.$refs.HistoryReservationsTerrainDialog.afficher(terrain);
        },

        // Formate la date au format 'DD MMMM YYYY'
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY');
        }
    },
};
</script>

<style scoped>
@import "./Terrains.css"
</style>
