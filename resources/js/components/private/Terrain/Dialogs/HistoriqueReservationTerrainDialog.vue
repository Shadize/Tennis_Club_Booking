<template>
  <div class="history-reservation-terrain-dialog" v-if="visible">
    <div class="history-reservation-terrain-dialog-content">
      <!-- Bouton de fermeture -->
      <button class="close-button" @click="annuler">x</button>
      <h1 class="dialog-title">Historique de réservation du terrain : {{ selectedTerrain }}</h1>

      <table class="reservation-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Réserver par</th>
            <th>Membre 1</th>
            <th>Membre 2</th>
            <th>Membre 3</th>
            <th>Membre 4</th>
            <th>
              <div class="filter-dropdown">
                <select v-model="selectedFilter" @change="applyFilter" class="filter-select">
                  <option hidden disabled value="null">Trier par</option>
                  <option v-for="option in filterOptions" :value="option.value" :key="option.value">
                    {{ option.label }}
                  </option>
                </select>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="reservation in reservations" :key="reservation.id">
            <td>{{ reservation.reservation_type }}</td>
            <td>{{ formatDate(reservation.date) }}</td>
            <td>{{ formatHeure(reservation.heureDebut) }}</td>
            <td>{{ formatHeure(reservation.heureFin) }}</td>
            <td style="font-weight: bold; text-decoration: underline;">{{ reservation.reserver_par.membre.nom + ' ' +
              reservation.reserver_par.membre.prenom }}</td>
            <td>{{ reservation.user.membre.nom + ' ' + reservation.user.membre.prenom }}</td>
            <td>{{ reservation.user2.membre.nom + ' ' + reservation.user2.membre.prenom }}</td>
            <td>{{ reservation.reservation_type === 'double' ? reservation.user3.membre.nom + ' ' +
              reservation.user3.membre.prenom : '' }}</td>
            <td>{{ reservation.reservation_type === 'double' ? reservation.user4.membre.nom + ' ' +
              reservation.user4.membre.prenom : '' }}</td>
            <td>{{ '' }}</td>
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
      reservations: null,
      // ID du terrain sélectionné (utilisé pour récupérer les blocages)
      selectedTerrain: null,

      filterOptions: [
        { value: "datetAsc", label: "Date croissante" },
        { value: "datetDesc", label: "Date décroissante" },
        { value: "heureDebutAsc", label: "Heure de début croissant" },
        { value: "heureDebutDesc", label: "Heure de début décroissant" },
        { value: "heureFinAsc", label: "Heure de fin croissant" },
        { value: "heureFinDesc", label: "Heure de fin décroissant" },
        { value: "reservationSimple", label: "Réservation simple" },
        { value: "reservationDouble", label: "Réservation double" }
      ],
      selectedFilter: "datetDesc",
    };
  },

  mounted() {

  },

  methods: {
    // Affiche la boîte de dialogue et récupère les blocages pour le terrain sélectionné
    afficher(terrain) {
      this.selectedTerrain = terrain.id;
      this.visible = true;
      this.fetchReservations(this.selectedTerrain);
    },
    // Ferme la boîte de dialogue et réinitialise les variables
    annuler() {
      this.visible = false;
      this.blocages = null;
      this.selectedTerrain = null;
    },

    async fetchReservations(terrainId) {

      await this.$axios.get(`/reservationsByTerrain/${terrainId}`)
        .then((response) => {
          this.reservations = response.data;
          // Trie les blocages par date de début par ordre décroissant
          this.reservations.sort((a, b) => {
            const dateA = new Date(a.dateDebut);
            const dateB = new Date(b.dateDebut);

            return dateB - dateA;

          });
        })
        .catch((error) => {
          console.error(error);
        });

    },

    // Système de filtre
    applyFilter() {
      switch (this.selectedFilter) {
        case "datetAsc":
          this.reservations.sort((a, b) => a.date.localeCompare(b.date));
          break;
        case "datetDesc":
          this.reservations.sort((a, b) => b.date.localeCompare(a.date));
          break;
        case "heureDebutAsc":
          this.reservations.sort((a, b) => a.heureDebut.localeCompare(b.heureDebut));
          break;
        case "heureDebutDesc":
          this.reservations.sort((a, b) => b.heureDebut.localeCompare(a.heureDebut));
          break;
        case "heureFinAsc":
          this.reservations.sort((a, b) => a.heureFin.localeCompare(b.heureFin));
          break;
        case "heureFinDesc":
          this.reservations.sort((a, b) => b.heureFin.localeCompare(a.heureFin));
          break;
        case "reservationSimple":
          this.reservations.sort((a, b) => {
            if (a.reservation_type === 'simple' && b.reservation_type === 'simple') return 0;
            if (a.reservation_type === 'simple') return -1;
            if (b.reservation_type === 'simple') return 1;
            return 0;
          });
          break;
        case "reservationDouble":
          this.reservations.sort((a, b) => {
            if (a.reservation_type === 'double' && b.reservation_type === 'double') return 0;
            if (a.reservation_type === 'double') return -1;
            if (b.reservation_type === 'double') return 1;
            return 0;
          });
        default:
          break;
      }
    },

    // ---------- Méthodes liées au formattage de données ----------

    formatDate(date) {
      // Définition des options de formatage de date
      const options = { year: "numeric", month: "long", day: "numeric" };

      // Utilisation de la méthode toLocaleDateString pour formater la date en utilisant les options spécifiées
      const formattedDate = new Date(date).toLocaleDateString(undefined, options);

      // Retourne la date formatée
      return formattedDate;
    },

    formatHeure(heure) {
      // Découpe l'heure et les minutes en utilisant ":" comme séparateur, puis conserve seulement les deux premiers éléments du tableau résultant
      const [hour, minutes] = heure.split(":").slice(0, 2);

      // Retourne l'heure formatée avec "h" comme séparateur entre l'heure et les minutes
      return `${hour}h${minutes}`;
    },

  },
};
</script>


<style scoped>


.history-reservation-terrain-dialog {
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

.history-reservation-terrain-dialog-content {
  position: relative;
  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 70%;
  overflow: auto;
}


.dialog-title {
  font-size: 24px;
  margin: 0;
  text-align: center;
  margin-top: 0;
}

.reservation-table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-top: 20px;
}

.reservation-table th,
.reservation-table td {
  padding: 10px;
  text-align: left;
}

.reservation-table th {
  font-weight: bold;
  background-color: #f5f5f5;
}

.reservation-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.reservation-table tbody tr:hover {
  background-color: #f1f1f1;
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
}</style>