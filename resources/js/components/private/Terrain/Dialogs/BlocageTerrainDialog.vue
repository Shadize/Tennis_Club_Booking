<template>
  <div class="blocage-dialog" v-if="visible"> <!-- Affiche la boîte de dialogue si visible est vrai -->
    <div class="blocage-dialog-content">
      <form @submit.prevent="submitForm" class="blocage-form"> <!-- Écoute l'événement de soumission du formulaire -->
        <h2>Nouveau blocage</h2>
        <div class="form-field">
          <label for="dateDebut">Date de début:</label>
          <input id="dateDebut" type="date" v-model="dateDebut"> <!-- Associe la valeur de l'entrée à la variable dateDebut -->
        </div>
        <div class="form-field">
          <label for="dateFin">Date de fin:</label>
          <input id="dateFin" type="date" v-model="dateFin"> <!-- Associe la valeur de l'entrée à la variable dateFin -->
        </div>
        <div class="form-field">
          <label>Raison:</label>
          <textarea id="raison" v-model="raison" rows="4" cols="50" placeholder="Entrez votre texte" required></textarea> <!-- Associe la valeur du textearea à la variable raison -->
        </div>
        <div class="form-buttons">
          <button type="button" @click="reset">Annuler</button> <!-- Appelle la méthode reset lors du clic -->
          <button type="submit" :disabled="dateDebut === '' 
                                || dateFin === '' 
                                || raison === ''">
        Confirmer</button> <!-- Désactive le bouton de confirmation si l'un des champs est vide -->
        </div>
        <div class="error-message" v-if="errorMessage">{{ errorMessage }}</div> <!-- Affiche le message d'erreur s'il est défini -->
      </form>
    </div>
  </div>
</template>

<script>
import moment from 'moment/min/moment-with-locales'; // Importe la bibliothèque Moment.js pour la manipulation des dates
import 'moment/locale/fr'; // Importe la configuration française pour Moment.js
moment.locale('fr'); // Définit la locale française pour Moment.js

export default {
  data() {
    return {
      visible: false, // Indique si la boîte de dialogue est visible ou non
      blocages: [], // Tableau de blocages
      dateDebut: '', // Date de début sélectionnée
      dateFin: '', // Date de fin sélectionnée
      raison: '', // Raison du blocage
      selectedTerrain: null, // Terrain sélectionné
      isValidForm: false, // Indique si le formulaire est valide ou non
      errorMessage: null, // Message d'erreur
      reservations: [], // Réservations
      currentUser: null, // Utilisateur actuel
    };
  },

  mounted() {
    this.getCurrentUser(); // Appelle la méthode pour obtenir l'utilisateur actuel lors du montage du composant
  },

  methods: {
    async getCurrentUser() {
      const response = await this.$axios.get("/user"); // Effectue une requête pour obtenir l'utilisateur actuel
      this.currentUser = response.data.id; // Stocke l'ID de l'utilisateur actuel
    },

    afficher(terrain) {
      this.selectedTerrain = terrain.id; // Stocke l'ID du terrain sélectionné
      this.visible = true; // Affiche la boîte de dialogue
    },

    reset() {
      this.selectedTerrain = null; // Réinitialise le terrain sélectionné
      this.dateDebut = ''; // Réinitialise la date de début
      this.dateFin = ''; // Réinitialise la date de fin
      this.raison = ''; // Réinitialise la raison
      this.visible = false; // Masque la boîte de dialogue
      this.errorMessage = null; // Réinitialise le message d'erreur
    },

    async submitForm() {
      const data = {
        terrain_Bloquer: this.selectedTerrain, // Terrain à bloquer
        dateDebut: this.dateDebut, // Date de début
        dateFin: this.dateFin, // Date de fin
        raison: this.raison, // Raison du blocage
        bloquer_Par: this.currentUser, // Utilisateur qui effectue le blocage
      };

      await this.formValidation(data); // Valide le formulaire et attend la fin de la validation

      if (this.isValidForm) {
        this.reset(); // Réinitialise le formulaire si celui-ci est valide
      }
    },

    async formValidation(data) {
      // Vérification des champs vides
      if (!data.dateDebut || !data.dateFin || !data.raison || !data.terrain_Bloquer) {
        this.errorMessage = "Veuillez remplir tous les champs"; // Affiche un message d'erreur si un champ est vide
        this.isValidForm = false; // Le formulaire n'est pas valide
        return;
      }

      // Vérification des dates

      // Vérifie si la date de début est antérieure à la date actuelle
      if (moment(data.dateDebut).isBefore(moment(), 'day')) {
        this.errorMessage = "La date de début ne peut pas être antérieure à la date actuelle"; // Affiche un message d'erreur si la date de début est incorrecte
        this.isValidForm = false; // Le formulaire n'est pas valide
        return;
      }

      // Affiche un message d'erreur si la date de fin est antérieure à la date de début
      if (new Date(data.dateDebut) > new Date(data.dateFin)) {
        this.errorMessage = "La date de fin ne peut pas être avant la date de début"; 
        this.isValidForm = false; // Le formulaire n'est pas valide
        return;
      }

      // Vérification de l'existence d'un autre blocage
      try {
        const response = await this.$axios.get('/checkBlocage', { params: { id: data.terrain_Bloquer, dateDebut: data.dateDebut, dateFin: data.dateFin } }); // Vérifie s'il existe déjà un blocage pendant cette période
        if (response.data.exists) {
          this.errorMessage = "Un blocage existe déjà pendant cette période"; // Affiche un message d'erreur si un blocage existe déjà
          this.isValidForm = false; // Le formulaire n'est pas valide
          return;
        }

        // Vérification des réservations existantes et suppression si nécessaire
        const reservationResponse = await this.$axios.get(`/reservationsByTerrain/${data.terrain_Bloquer}`); // Obtient les réservations pour le terrain sélectionné
        if (reservationResponse.data.length > 0) {
          this.reservations = reservationResponse.data; // Stocke les réservations
          for (let reservation of this.reservations) {
            if (reservation.date >= data.dateDebut && reservation.date <= data.dateFin) {
              await this.$axios.delete(`/reservations/${reservation.id}`); // Supprime les réservations qui se chevauchent avec la période de blocage
            }
          }
        }

        // Création du blocage
        await this.$axios.post('/create-blocage', data); // Effectue une requête pour créer le blocage
        this.errorMessage = null; // Réinitialise le message d'erreur
        this.isValidForm = true; // Le formulaire est valide
      } catch (err) {
        console.error("Error:", err);
        this.errorMessage = "Une erreur est survenue lors de la création du blocage"; // Affiche un message d'erreur en cas d'erreur lors de la création du blocage
        this.isValidForm = false; // Le formulaire n'est pas valide
      }
    },
  },
};
</script>

<style scoped>
.blocage-dialog {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.blocage-dialog-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
}

.blocage-form {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.blocage-form h2 {
  margin-bottom: 20px;
}

.form-field {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-field label {
  margin-right: 10px;
  width: 100px;
}

.form-field select,
.form-field input,
.form-field textarea {
  width: 200px;
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.form-buttons {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.form-buttons button {
  padding: 10px 20px;
  margin-left: 10px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.form-buttons button:first-child {
  background-color: #ccc;
  color: #333;
}

.form-buttons button:last-child {
  background-color: #1ac030;
  color: #fff;
}

.form-buttons button:hover {
  background-color: #4dff65;
}

.form-buttons button:first-child:hover {
  background-color: #e22121;
  color: #fff;
}

.form-buttons button:disabled {
  background-color: #ccc;
  color: #333;
  cursor: not-allowed;
}

.error-message {
  color: red;
  margin-top: 10px;
}
</style>
