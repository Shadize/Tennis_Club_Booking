<template>
  <div class="confirmation-dialog" v-if="visible">
    <div class="confirmation-dialog-content">
      <!-- Bouton de fermeture -->
      <button class="close-button" @click="annuler">x</button>

      <!-- Titre de la boîte de dialogue -->
      <h1 class="dialog-title">Liste des blocages</h1>

      <!-- Tableau pour afficher la liste des blocages -->
      <table class="terrain-table">
        <thead>
          <tr>
            <!-- En-têtes du tableau -->
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Raison</th>
            <th>Bloqué par</th>
            <th class="action-column">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Boucle à travers chaque blocage dans le tableau blocages -->
          <tr v-for="blocage in blocages" :key="blocage.id">
            <!-- Affichage des détails du blocage dans les cellules du tableau -->
            <td class="date">{{ formatDate(blocage.dateDebut) }}</td>
            <td class="date">{{ formatDate(blocage.dateFin) }}</td>
            <td class="raison">{{ blocage.raison }}</td>
            <td class="nom">{{ blocage.user.membre.nom }} {{ blocage.user.membre.prenom }}</td>
            <td class="action-button-container">
              <!-- Si le blocage n'est pas sélectionné pour la suppression, afficher le bouton de suppression -->
              <div v-if="selectedBlocageId !== blocage.id">
                <button class="supprimer-button" @click="askForConfirmation(blocage.id)"
                  :disabled="isBeforeToday(blocage.dateDebut)">Supprimer</button>
              </div>
              <!-- Si le blocage est sélectionné pour la suppression, afficher les boutons de confirmation et d'annulation -->
              <div v-else>
                <button class="confirmer-button" @click="supprimerBlocage(blocage.id)">Confirmer</button>
                <button class="annuler-button" @click="cancelDelete">Annuler</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import moment from 'moment/min/moment-with-locales';
import 'moment/locale/fr';
moment.locale('fr');

export default {
  data() {
    return {
      // Indique si la boîte de dialogue est visible ou non
      visible: false,
      // Tableau pour stocker la liste des blocages
      blocages: null,
      // ID du terrain sélectionné (utilisé pour récupérer les blocages)
      selectedTerrain: null,
      // ID du blocage sélectionné pour confirmation de suppression
      selectedBlocageId: null,
    };
  },

  mounted() {

  },

  methods: {
    // Affiche la boîte de dialogue et récupère les blocages pour le terrain sélectionné
    afficher(terrain) {
      this.selectedTerrain = terrain.id;
      this.visible = true;
      this.fetchBlocages();
    },
    // Ferme la boîte de dialogue et réinitialise les variables
    annuler() {
      this.visible = false;
      this.blocages = null;
      this.selectedTerrain = null;
    },

    // Récupère les blocages pour le terrain sélectionné
    async fetchBlocages() {
      await this.$axios.get(`/blockages/${this.selectedTerrain}`)
        .then((response) => {
          this.blocages = response.data;

          // Trie les blocages par date de début par ordre décroissant
          this.blocages.sort((a, b) => {
            const dateA = new Date(a.dateDebut);
            const dateB = new Date(b.dateDebut);

            return dateB - dateA;

          });
        })
        .catch((error) => {
          console.error(error);
        });
    },

    // Formate la date en utilisant la bibliothèque moment.js
    formatDate(date) {
      return moment(date).format('DD MMMM YYYY');
    },

    // Définit l'ID du blocage sélectionné pour la confirmation de suppression
    askForConfirmation(id) {
      this.selectedBlocageId = id;
    },

    // Annule la suppression du blocage
    cancelDelete() {
      this.selectedBlocageId = null;
    },

    // Supprime le blocage sélectionné
    async supprimerBlocage(id) {
      await this.$axios.delete(`/bloquers/${id}`);

      // Réinitialise l'ID du blocage sélectionné et récupère les blocages mis à jour
      this.selectedBlocageId = null;
      this.fetchBlocages();
    },

    // Vérifie si une date est antérieure au jour courant
    isBeforeToday(date) {
      return moment(date).isBefore(moment(), 'day');
    },
  },
};
</script>

<style scoped>
.nom {
  min-width: 130px;
}

.confirmation-dialog {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.confirmation-dialog-content {
  position: relative;
  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 50%;
  overflow: auto;
}

.date {
  min-width: 120px;
}

.raison {
  min-width: 130px;
}

.dialog-title {
  font-size: 24px;
  margin: 0;
  text-align: center;
  margin-top: 0;
}

.terrain-table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-top: 20px;
}

.terrain-table th,
.terrain-table td {
  padding: 10px;
  text-align: left;
}

.terrain-table th {
  font-weight: bold;
  background-color: #f5f5f5;
}

.terrain-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.terrain-table tbody tr:hover {
  background-color: #f1f1f1;
}

.action-column {
  text-align: center;
  width: 1%;
  white-space: nowrap;
}

.action-button-container {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  min-width: 170px;
}

.action-button-container button {
  flex-shrink: 0;
}

.supprimer-button {
  background-color: #dc3545;
  color: #fff;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.supprimer-button:hover {
  opacity: 0.8;
}


.supprimer-button:disabled {
  background-color: #ddd;
  cursor: not-allowed;
}

.confirmer-button {
  background-color: #28a745;
  color: #fff;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-right: 5px;
}

.confirmer-button:hover {
  opacity: 0.8;
}

.annuler-button {
  background-color: #dc3545;
  color: #fff;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.annuler-button:hover {
  opacity: 0.8;
}

.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 5px;
  background-color: transparent;
  color: #333;
  border: none;
  font-size: 18px;
  cursor: pointer;
}
</style>
