<template>
  <div class="reservation-list">
    <div class="header">
      <h1 class="header-title">Liste des Réservations</h1>
      <div class="header-buttons">
        <button class="reservation-button" @click="afficherDialogReservation('admin')" v-if="currentUserRole === 'admin'">
          Reserver pour un membre
        </button>
        <button class="reservation-button" @click="afficherDialogReservation('member')"
          :disabled="!membreEnOrdreDeCotisation">
          Faire une réservation
        </button>
      </div>

    </div>
    <table class="reservation-table">
      <thead>
        <tr>
          <th>N° de terrain</th>
          <th>Type</th>
          <th>Date</th>
          <th>Heure de début</th>
          <th>Heure de fin</th>
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
          <td>{{ reservation.terrain_Id }}</td>
          <td>{{ reservation.reservation_type }}</td>
          <td>{{ formatDate(reservation.date) }}</td>
          <td>{{ formatHeure(reservation.heureDebut) }}</td>
          <td>{{ formatHeure(reservation.heureFin) }}</td>
          <td>{{ reservation.user.membre.nom + ' ' + reservation.user.membre.prenom }}</td>
          <td>{{ reservation.user2.membre.nom + ' ' + reservation.user2.membre.prenom }}</td>
          <td>{{ reservation.reservation_type === 'double' ? reservation.user3.membre.nom + ' ' +
            reservation.user3.membre.prenom : '' }}</td>
          <td>{{ reservation.reservation_type === 'double' ? reservation.user4.membre.nom + ' ' +
            reservation.user4.membre.prenom : '' }}</td>
          <td>
            <button @click="afficherConfirmation(reservation)" :disabled="!peutSupprimerReservation(reservation)">
              Annuler
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <ReservationCreateDialog ref="ReservationCreateDialog" @ajouter="fetchReservations"
      @annuler="annulerCreationReservation" />
    <SuprresionReservationDialog ref="SuprresionReservationDialog" @supprimer="supprimerReservation">
    </SuprresionReservationDialog>
  </div>
</template>
  
  
<script>
import SuprresionReservationDialog from "../../private/Reservations/Dialog/SuprresionReservationDialog.vue";
import ReservationCreateDialog from "../../private/Reservations/Dialog/ReservationCreateDialog.vue";

export default {
  components: {
    SuprresionReservationDialog,
    ReservationCreateDialog,
  },
  data() {
    return {
      currentUserRole: null,
      reservations: [], // Les réservations seront récupérées depuis l'API
      membreEnOrdreDeCotisation: false, // La variable indiquant si le membre est en ordre de cotisation
      creadentialId: "", // L'ID du membre récupéré depuis l'API
      filterOptions: [
        { value: "datetAsc", label: "Date croissante" },
        { value: "datetDesc", label: "Date décroissante" },
        { value: "heureDebutAsc", label: "Heure de début croissant" },
        { value: "heureDebutDesc", label: "Heure de début décroissant" },
        { value: "heureFinAsc", label: "Heure de fin croissant" },
        { value: "heureFinDesc", label: "Heure de fin décroissant" },
        { value: "terrainAsc", label: "Numéro de terrain croissant" },
        { value: "terrainDesc", label: "Numéro de terrain décroissant" }
      ],
      selectedFilter: "datetDesc"
    };
  },
  mounted() {
    // Appel à l'API pour récupérer les réservations
    this.getInfos().then(() => {
      this.fetchReservations();
    });
  },
  created() {
    // Logique à exécuter lors de la création du composant
  },
  methods: {

    // ---------- Méthodes de récupération des données ----------

    async getInfos() {
      try {
        // Récupérer l'ID du membre depuis l'API

        await this.$axios.get("/user")
          .then(response => {
            this.currentUserRole = response.data.rôle;
            this.creadentialId = response.data.id;
          });


        // Récupérer l'état de l'ordre de cotisation du membre depuis l'API
        const member = await this.$axios.post("/getMembreByCredential", { id: this.creadentialId });
        this.membreEnOrdreDeCotisation = member.data.membre.ordre_De_Cotisation == 1 ? true : false;
      } catch (error) {
        console.error(error);
      }
    },

    // Récupère toutes les réservations faites par l'utilisateur actuellement connecté
    async fetchReservations() {
      await this.$axios
        .get(`/reservationsByMember/${this.creadentialId}`)
        .then((response) => {
          this.reservations = response.data;
          this.selectedFilter = "datetDesc";
          this.applyFilter();
          this.selectedFilter = null;
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
        case "terrainAsc":
          this.reservations.sort((a, b) => a.terrain_Id - b.terrain_Id);
          break;
        case "terrainDesc":
          this.reservations.sort((a, b) => b.terrain_Id - a.terrain_Id);
          break;
        default:
          break;
      }
    },



    // ---------- Méthodes liées à l'ajout de réservations et à la boite de dialogue concernée ----------

    afficherDialogReservation(reservationMode) {
      this.$refs.ReservationCreateDialog.afficher(reservationMode);
    },

    annulerCreationReservation() {
      this.afficherDialog = false;
    },

    // ---------- Méthodes liées à la suppression de réservations et à la boite de dialogue concernée ----------

    supprimerReservation(reservation) {

      // Vérifie si la réservation est nulle, si c'est le cas, arrête l'exécution de la méthode
      if (reservation === null)
        return;

      // Appel à l'API pour supprimer la réservation en utilisant son ID
      this.$axios
        .delete(`/reservations/${reservation.id}`)
        .then((response) => {
          // Met à jour la propriété "supprimer_par" de la réservation avec l'identifiant actuel
          reservation.supprimer_par = this.creadentialId;

          // Appel à l'API pour mettre à jour les données de la réservation
          this.$axios.put(`/reservations`, reservation);
          // La réservation a été supprimée avec succès, récupère les nouvelles données de réservation
          this.fetchReservations();
        })
        .catch((error) => {
          // Une erreur s'est produite lors de la suppression de la réservation
          console.error("Une erreur s'est produite lors de la suppression de la réservation", error);
        });
    },

    peutSupprimerReservation(reservation) {

      const maintenant = new Date(); // Obtenir la date et l'heure actuelles
      const heureDebutReservation = new Date(reservation.date + ' ' + reservation.heureDebut); // Convertir la date et l'heure de début de réservation en objet Date
      const heureFinReservation = new Date(reservation.date + ' ' + reservation.heureFin); // Convertir la date et l'heure de fin de réservation en objet Date
      const vingtQuatreHeuresEnMs = 24 * 60 * 60 * 1000; // Constante pour 24 heures en millisecondes

      // Vérifier si la réservation est déjà passée par rapport à la date et l'heure actuelles
      if (heureFinReservation < maintenant) {
        return false; // La réservation est déjà terminée
      }

      // Vérifier s'il reste moins de 24 heures avant le début de la réservation
      if (heureDebutReservation - maintenant < vingtQuatreHeuresEnMs) {
        return false; // Il reste moins de 24 heures avant le début de la réservation
      }

      // Si la réservation est à plus de 24 heures à partir de la date et de l'heure actuelles, et si la date et l'heure ne sont pas encore passées, renvoyer true pour activer le bouton
      return true;
    },


    // Méthode qui permet d'ouvrir une boite de confirmation du navigateur non personnalisable
    confirmerSuppression(reservation) {
      if (confirm("Êtes-vous sûr de vouloir supprimer cette réservation ?")) {
        this.supprimerReservation(reservation);
      }
    },

    // Méthode qui affiche ma boite de dialogue personnnalisée "SuprresionReservationDialog"
    afficherConfirmation(reservation) {
      this.$refs.SuprresionReservationDialog.afficher(reservation);
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





    // ---------- Méthodes liées à la navigation ----------

    naviguerVersCreationReservation() {
      // Redirection vers la page "CreateReservation"
      this.$router.push("/Createeservations");
    },
  }
};
</script>

<style scoped>
@import url("./Reservations.css");
</style>

  