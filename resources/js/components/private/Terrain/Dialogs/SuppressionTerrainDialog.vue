<template>
  <div class="confirmation-dialog" v-if="visible"> <!-- Affiche la boîte de dialogue si visible est vrai -->
    <div class="confirmation-dialog-content">
      <p>Êtes-vous sûr de vouloir supprimer ce terrain ?</p>
      <div class="confirmation-dialog-buttons">
        <button @click="annuler">Annuler</button> <!-- Appelle la méthode annuler lors du clic -->
        <button @click="confirmer">Confirmer</button> <!-- Appelle la méthode confirmer lors du clic -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      visible: false, // Indique si la boîte de dialogue est visible ou non
      terrain: null, // Terrain à supprimer
    };
  },
  methods: {
    afficher(terrain) {
      this.terrain = terrain; // Stocke le terrain à supprimer
      this.visible = true; // Affiche la boîte de dialogue
    },
    annuler() {
      this.visible = false; // Masque la boîte de dialogue
    },
    confirmer() {
      this.supprimer(); // Appelle la méthode supprimer lors de la confirmation
    },
    async supprimer() {
      try {
        await this.deleteBlockagesForTerrain(); // Supprime les blocages associés au terrain
        await this.deleteReservationsForTerrain(); // Supprime les réservations associées au terrain
        await this.deleteTerrain(); // Supprime le terrain lui-même
        this.$emit("supprimer"); // Émet un événement "supprimer" pour notifier le composant parent
        this.visible = false; // Masque la boîte de dialogue
      } catch (error) {
        console.error("Une erreur s'est produite lors de la suppression", error);
      }
    },

    async deleteBlockagesForTerrain() {
      const response = await this.$axios.get(`/blockages/${this.terrain.id}`); // Obtient les blocages associés au terrain
      if (response.data) {
        const blockages = response.data; // Stocke les blocages
        for (let blockage of blockages) {
          await this.$axios.delete(`/bloquers/${blockage.id}`); // Supprime chaque blocage
        }
      }
    },

    async deleteReservationsForTerrain() {
      const response = await this.$axios.get(`/reservationsByTerrain/${this.terrain.id}`); // Obtient les réservations associées au terrain
      if (response.data) {
        const reservations = response.data; // Stocke les réservations
        for (let reservation of reservations) {
          await this.$axios.delete(`/reservations/${reservation.id}`); // Supprime chaque réservation
        }
      }
    },

    async deleteTerrain() {
      await this.$axios.delete(`/terrains/${this.terrain.id}`); // Supprime le terrain lui-même
    },
  },
};
</script>

<style scoped>
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
  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
}

.confirmation-dialog-content p {
  margin-bottom: 20px;
}

.confirmation-dialog-buttons {
  display: flex;
  justify-content: flex-end;
}

.confirmation-dialog-buttons button {
  padding: 10px 20px;
  margin-left: 10px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.confirmation-dialog-buttons button:first-child {
  background-color: #ccc;
  color: #333;
}

.confirmation-dialog-buttons button:first-child:hover {
  background-color: #e22121;
  color: #fff;
}

.confirmation-dialog-buttons button:last-child {
  background-color: #1ac030;
  color: #fff;
}

.confirmation-dialog-buttons button:last-child:hover {
  background-color: #4dff65;
}
</style>
