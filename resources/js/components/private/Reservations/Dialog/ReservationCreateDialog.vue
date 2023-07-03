<template>
  <div class="reservation-dialog" v-if="visible">
    <div class="reservation-dialog-content">
      <form @submit.prevent="submitForm" class="reservation-form">
        <h2>{{ reservationMode === 'member' ? 'Nouvelle réservation' : 'Réserver pour un membre' }}</h2>

        <!-- Selection du terrain -->
        <div class="form-field">
          <label for="terrain">Terrain:</label>
          <select id="terrain" v-model="selectedTerrain">
            <option v-for="terrainOption in terrains" :value="terrainOption.id" :key="terrainOption">{{ terrainOption.id
            }}
            </option>
          </select>
        </div>

        <!-- Type de reservation-->
        <div class="form-field" v-if="selectedTerrain">
          <label for="reservationType">Type de réservation:</label>
          <select id="reservationType" v-model="reservationType" @change="resetMembre(reservationType)">
            <option value="simple">Simple</option>
            <option value="double">Double</option>
          </select>
        </div>

        <!-- Date de reservation -->
        <div class="form-field" v-if="reservationType">
          <label for="date">Date:</label>
          <input type="date" id="date" v-model="date">
        </div>

        <!-- Durée de la reservation -->
        <div class="form-field" v-if="date !== ''">
          <label for="duree">Durée:</label>
          <select id="duree" v-model="duree">
            <option value="1" v-if="reservationType === 'simple'">1 heure</option>
            <option value="2" v-if="reservationType === 'simple'">2 heures</option>
            <option value="2" v-if="reservationType === 'double'">2 heures</option>
            <option value="4" v-if="reservationType === 'double'">4 heures</option>
          </select>
        </div>

        <!-- Plage horaire à selectionner -->
        <div class="form-field" v-if="duree">
          <label for="plageHoraire">Plage horaire:</label>
          <select id="plageHoraire" v-model="plageHoraire" @change="selectionnerHeure(plageHoraire)">
            <option
              v-for="horaire in reservationType === 'simple' ? (duree === '1' ? plageHoraire1heure : plageHoraire2heure) : (duree === '2' ? plageHoraire2heure : plageHoraire4heure)"
              :value="horaire" :key="horaire.heureDebut">
              {{ horaire.heureDebut + ' - ' + horaire.heureFin }}
            </option>
          </select>

        </div>

        <!-- Membre pour qui l'admin fait la réservation si il s'agit une réservatoin pour membre -->
        <div v-if="reservationMode === 'admin' && (reservationType === 'simple' || reservationType === 'double')"
          class="form-field">
          <label for="membre1">Membre:</label>
          <select id="membre1" v-model="membre1">
            <option v-for="membre in filterMembres(null)" :value="membre.user_Id" :key="membre.user_Id">
              {{ membre.nom + ' ' + membre.prenom }}
            </option>
          </select>
        </div>

        <!-- Partenaire si réservation simple -->
        <div v-if="reservationType === 'simple' && membre1 !== null" class="form-field">
          <label for="membre2">Partenaire:</label>
          <select id="membre2" v-model="membre2">
            <option v-for="membre in filterMembres([membre1])" :value="membre.user_Id" :key="membre.user_Id">
              {{ membre.nom + ' ' + membre.prenom }}
            </option>
          </select>
        </div>

        <!-- Partenaire 2 -->
        <div v-if="reservationType === 'double' && membre1 !== null" class="form-field">
          <label for="membre2">Partenaire 2:</label>
          <select id="membre2" v-model="membre2">
            <option v-for="membre in filterMembres([membre1, membre3, membre4])" :value="membre.user_Id"
              :key="membre.user_Id">
              {{ membre.nom + ' ' + membre.prenom }}
            </option>
          </select>
        </div>

        <!-- Partenaire 3 -->
        <div v-if="reservationType === 'double' && membre1 !== null" class="form-field">
          <label for="membre3">Partenaire 3:</label>
          <select id="membre3" v-model="membre3">
            <option v-for="membre in filterMembres([membre1, membre2, membre4])" :value="membre.user_Id"
              :key="membre.user_Id">
              {{ membre.nom + ' ' + membre.prenom }}
            </option>
          </select>
        </div>

        <!-- Partenaire 4 -->
        <div v-if="reservationType === 'double' && membre1 !== null" class="form-field">
          <label for="membre4">Partenaire 4:</label>
          <select id="membre4" v-model="membre4">
            <option v-for="membre in filterMembres([membre1, membre2, membre3])" :value="membre.user_Id"
              :key="membre.user_Id">
              {{ membre.nom + ' ' + membre.prenom }}
            </option>
          </select>
        </div>


        <!-- Bouttons d'actions -->
        <div class="form-buttons">
          <button type="button" @click="annuler">Annuler</button>
          <button type="submit" :disabled="selectedTerrain === '' ||
            date === '' ||
            heureDebut === '' ||
            heureFin === '' ||
            reservationType === '' ||
            duree === null ||
            plageHoraire === null ||
            membre1 === null ||
            membre2 === null ||
            (reservationType !== 'simple' && (membre3 === null || membre4 === null))">
            Valider</button>
        </div>

        <!-- Message d'erreur -->
        <div class="error-message" v-if="errorMessage">{{ errorMessage }}</div>
      </form>
    </div>
  </div>
</template>
  
<script>


export default {
  data() {
    return {

      currentUser: null,
      reservationMode: '', // Mode de réservation (admin ou membre)
      visible: false, // Indique si le dialogue de réservation est visible
      terrains: [], // Liste des terrains
      membres: [], // Liste des membres
      errorMessage: null, // Message d'erreur
      isValidForm: false, // Indique si le formulaire est valide

      // Données du formulaire de réservation
      selectedTerrain: '', // Terrain sélectionné
      date: '', // Date de réservation
      heureDebut: '', // Heure de début de réservation
      heureFin: '', // Heure de fin de réservation
      reservationType: '', // Type de réservation (simple ou double)
      membre1: null, // ID du membre 1
      membre2: null, // ID du membre 2 (pour réservation simple)
      membre3: null, // ID du membre 3 (pour réservation double)
      membre4: null, // ID du membre 4 (pour réservation double)

      duree: null, // Durée de la réservation

      // Plage horaire sélectionnée
      plageHoraire: {
        heureDebut: null,
        heureFin: null,
      },

      // Plages horaires disponibles
      plageHoraire1heure: [
        { heureDebut: '09:00', heureFin: '10:00' },
        { heureDebut: '10:00', heureFin: '11:00' },
        { heureDebut: '11:00', heureFin: '12:00' },
        { heureDebut: '12:00', heureFin: '13:00' },
        { heureDebut: '13:00', heureFin: '14:00' },
        { heureDebut: '14:00', heureFin: '15:00' },
        { heureDebut: '15:00', heureFin: '16:00' },
        { heureDebut: '16:00', heureFin: '17:00' },
        { heureDebut: '17:00', heureFin: '18:00' },
        { heureDebut: '18:00', heureFin: '19:00' },
        { heureDebut: '19:00', heureFin: '20:00' },
        { heureDebut: '20:00', heureFin: '21:00' },
        { heureDebut: '21:00', heureFin: '22:00' },
      ],

      plageHoraire2heure: [
        { heureDebut: '09:00', heureFin: '11:00' },
        { heureDebut: '10:00', heureFin: '12:00' },
        { heureDebut: '11:00', heureFin: '13:00' },
        { heureDebut: '12:00', heureFin: '14:00' },
        { heureDebut: '13:00', heureFin: '15:00' },
        { heureDebut: '14:00', heureFin: '16:00' },
        { heureDebut: '15:00', heureFin: '17:00' },
        { heureDebut: '16:00', heureFin: '18:00' },
        { heureDebut: '17:00', heureFin: '19:00' },
        { heureDebut: '18:00', heureFin: '20:00' },
        { heureDebut: '19:00', heureFin: '21:00' },
        { heureDebut: '20:00', heureFin: '22:00' },
      ],

      plageHoraire4heure: [
        { heureDebut: '09:00', heureFin: '13:00' },
        { heureDebut: '10:00', heureFin: '14:00' },
        { heureDebut: '11:00', heureFin: '15:00' },
        { heureDebut: '12:00', heureFin: '16:00' },
        { heureDebut: '13:00', heureFin: '17:00' },
        { heureDebut: '14:00', heureFin: '18:00' },
        { heureDebut: '15:00', heureFin: '19:00' },
        { heureDebut: '16:00', heureFin: '20:00' },
        { heureDebut: '17:00', heureFin: '21:00' },
        { heureDebut: '18:00', heureFin: '22:00' },
      ],
    };
  },

  created() {
    // On récupère toutes les informations nécessaires
    this.getCurrentUser();
    this.fetchMembers();
    this.fetchTerrains();
  },

  methods: {

    // ================== METHODES LIEES A LA GESTION DU COMPONENT ============================ //

    // Affiche le dialogue de réservation et initialise le mode de réservaiton
    afficher(reservationMode) {
      this.reservationMode = reservationMode;
      this.visible = true;
    },

    // Annule la réservation et réinitialiser le formulaire, ça fermera aussi notre component
    annuler() {
      this.resetForm();
    },


    // ================== METHODES DE RECUPERATION DE DONNEES ============================ //


    // Récupére l'utilisateur actuel
    async getCurrentUser() {
      await this.$axios.get("/user").then((response) => {
        this.currentUser = response.data;
        if (this.reservationMode !== "admin") {
          this.membre1 = this.currentUser.id;
        }
      });
    },

    // Récupére la liste des terrains
    fetchTerrains() {
      this.$axios.get(`/terrains`)
        .then((response) => {
          this.terrains = response.data;
        })
        .catch((error) => {
          console.error(error);
        });

    },

    // Récupére la liste des membres
    fetchMembers() {
      this.$axios
        .post(`/listeMembreDispo`)
        .then((response) => {
          this.membres = response.data;
          this.membresTrier = response.data;
        })
        .catch((error) => {
          console.error(error);
        });
    },


    // ================== METHODES DE CHECK LORS DE LA CREATION D'UNE RESERVATION ============================ //


    // Vérifie si le terrain est disponible à la date sélectionnée
    async checkTerrainDisponible(terrainId, date) {
      try {
        const response = await this.$axios.get(`/blockages/${terrainId}`);
        const blocages = response.data;

        for (const blocage of blocages) {
          // Vérifie si la date de réservation se situe entre la date de début et la date de fin du blocage
          if (date >= blocage.dateDebut && date <= blocage.dateFin) {
            return false; // Le terrain est bloqué à la date sélectionnée
          }
        }

        return true; // Le terrain est disponible à la date sélectionnée
      } catch (error) {
        console.error(error);
        throw error; // Lève une exception pour la capturer dans la fonction appelante
      }
    },

    // Vérifie si la réservation chevauche une autre réservation existante
    async checkReservationChevauche(data) {
      try {
        const response = await this.$axios.get('/getReservationsByDate', { params: { date: data.date } });
        const reservations = response.data;

        const heureDebut = new Date(`1970-01-01T${data.heureDebut}`);
        const heureFin = new Date(`1970-01-01T${data.heureFin}`);

        for (const reservation of reservations) {
          const reservationHeureDebut = new Date(`1970-01-01T${reservation.heureDebut}`);
          const reservationHeureFin = new Date(`1970-01-01T${reservation.heureFin}`);

          // Vérifie si les plages horaires se chevauchent pour le même terrain et la même date
          if (
            data.date === reservation.date &&
            data.terrain_Id === reservation.terrain_Id &&
            heureDebut < reservationHeureFin &&
            heureFin > reservationHeureDebut
          ) {
            return true; // La réservation chevauche une autre réservation existante
          }
        }

        return false; // Aucune réservation chevauchante
      } catch (error) {
        console.error(error);
        throw error;
      }
    },

    // Vérifie les limites de réservation par semaine pour un membre
    async checkLimiteReservations(data) {
      // Convertit la date de réservation en objet Date
      const reservationDate = new Date(data.date);

      // Obtient le jour de la semaine de la réservation (0 pour dimanche, 1 pour lundi, ..., 6 pour samedi)
      const reservationDayOfWeek = reservationDate.getDay();

      // Calcule la date de début de la semaine en soustrayant le jour de la semaine de la date de réservation
      const startOfWeek = new Date(reservationDate);
      startOfWeek.setDate(reservationDate.getDate() - reservationDayOfWeek);

      // Calcule la date de fin de la semaine en ajoutant la différence entre 6 et le jour de la semaine à la date de réservation
      const endOfWeek = new Date(reservationDate);
      endOfWeek.setDate(reservationDate.getDate() + (6 - reservationDayOfWeek));

      // Effectue une requête pour obtenir les réservations du membre entre la date de début et la date de fin de la semaine
      const response = await this.$axios.get('/getReservationsByDateBetween', {
        params: {
          membre_id: data.membre1,
          dateDebut: startOfWeek.toISOString(),
          dateFin: endOfWeek.toISOString(),
        },
      });

      // Récupère les réservations obtenues dans la réponse
      const reservations = response.data;

      // Compte le nombre de réservations pour la semaine en cours
      const reservationCount = reservations.length;

      if (reservationCount >= 2) {
        // Limite de réservations par semaine dépassée
        return false;
      }

      return true; // La limite de réservations par semaine n'est pas dépassée
    },


    // ================== METHODES LIEES AU FORMULAIRE ============================ //


    // Méthode appellée lors de la soumission du formulaire
    async submitForm() {
      const formData = {
        terrain_Id: this.selectedTerrain,
        date: this.date,
        heureDebut: this.heureDebut,
        heureFin: this.heureFin,
        actif: true,
        reservation_type: this.reservationType,
        reserver_par: this.currentUser.id,
        membre1: this.membre1,
        membre2: this.membre2,
        membre3: this.membre3,
        membre4: this.membre4,
      };
      await this.formValidation(formData);

      if (this.isValidForm) {
        this.resetForm();
        this.$emit("ajouter");
      }
    },

    async formValidation(data) {
      try {

        const today = new Date();
        const reservationDate = new Date(data.date);

        const [heure, minutes] = data.heureDebut.split(':');
        reservationDate.setHours(Number(heure), Number(minutes), 0);
        // Vérifie si la date de réservation est antérieure à la date d'aujourd'hui




        if (reservationDate < today) {
          this.errorMessage = "La date de réservation ne peut pas être antérieure à la date d'aujourd'hui.";
          this.isValidForm = false;
          return;
        }

        // Vérifie si le terrain est disponible à la date sélectionnée
        const terrainDisponible = await this.checkTerrainDisponible(data.terrain_Id, data.date);
        if (!terrainDisponible) {
          this.errorMessage = "Le terrain n'est pas disponible à la date sélectionnée.";
          this.isValidForm = false;
          return;
        }



        // Vérifie si la réservation chevauche une autre réservation existante
        const reservationChevauche = await this.checkReservationChevauche(data);
        if (reservationChevauche) {
          this.errorMessage = "La réservation chevauche une autre réservation existante.";
          this.isValidForm = false;
          return;
        }

        // Vérifie les limites de réservation par semaine pour un membre
        const limiteReservations = await this.checkLimiteReservations(data);
        if (!limiteReservations) {
          this.errorMessage = "Limite de réservation de la semaine atteinte.";
          this.isValidForm = false;
          return;
        }

        // Toutes les vérifications sont réussies, la réservation est valide
        await this.$axios.post('/reservations', data);
        this.isValidForm = true;
      } catch (error) {
        console.error(error);
        this.errorMessage = "Une erreur s'est produite lors de la validation de la réservation.";
        this.isValidForm = false;
      }

      this.isValidForm = true;
    },


    // Rénitialisation de toutes les données du formulaire
    resetForm() {
      this.errorMessage = null;
      this.visible = false;
      this.selectedTerrain = '';
      this.plageHoraire = null,
        this.duree = null,
        this.date = '';
      this.heureDebut = '';
      this.heureFin = '';
      this.reservationType = '';
      if (this.reservationMode === 'admin') {
        this.membre1 = null;
      }
      this.membre2 = null;
      this.membre3 = null;
      this.membre4 = null;
    },


    // ================== METHODES UTILITAIRES AU FORMULAIRE ============================ //


    // Méthode qui permet de filtrer la liste des membres à chaque fois qu'un d'entre eux à été selectionner
    // Afin de ne pas pouvoir sélectionner en tant qu'autre partenaire un membre déjà sélectionner 
    filterMembres(selectedMembres) {
      if (selectedMembres !== null) {
        return this.membres.filter(membre => !selectedMembres.includes(membre.user_Id));
      }
      else {
        return this.membres;
      }
    },

    // Méthode appellée lors d'une sélection d'une plage horaire
    selectionnerHeure(horaire) {
      this.heureDebut = horaire.heureDebut;
      this.heureFin = horaire.heureFin;
    },

    // Réinitialiser les membres et la durée lorsque le type de réservation change
    resetMembre(reservationType) {
      if (reservationType !== 'double') {
        this.membre3 = null;
        this.membre4 = null;
      }

      this.duree = null;
      this.plageHoraire = null;
    },
  },
};
</script>
  
<style scoped>
.reservation-dialog {
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

.reservation-dialog-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
}

.reservation-form {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.reservation-form h2 {
  margin-bottom: 20px;
}

.form-field {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-field label {
  margin-right: 40px;
  text-align: center;
}

.form-field select,
.form-field input {
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
  background-color: #ff4d4f;
  color: white;
}

.form-buttons button:first-child:hover {
  background-color: #f32b2f;
  color: white;
}

.form-buttons button:last-child {
  background-color: #1ac030;
  color: #fff;
}

.form-buttons button:last-child {
  background-color: #4dff65;
  color: #fff;
}

.error-message {
  color: red;
  margin-top: 10px;
}

.form-buttons button:disabled {
  background-color: #ccc;
  color: #333;
  cursor: not-allowed;
}
</style>
  