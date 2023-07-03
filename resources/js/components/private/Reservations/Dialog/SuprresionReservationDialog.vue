<template>
    <div class="confirmation-dialog" v-if="visible">
      <div class="confirmation-dialog-content">
        <p>Êtes-vous sûr de vouloir supprimer cette réservation ?</p>
        <div class="confirmation-dialog-buttons">
          <button @click="annuler">Annuler</button>
          <button @click="confirmer">Confirmer</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        visible: false,
        reservation: null,
      };
    },
    methods: {
      afficher(reservation) {
        this.reservation = reservation;
        this.visible = true;
      },
      annuler() {
        this.visible = false;
      },
      confirmer() {
        // Emet "supprimer" au component parent pour appeller ensuite la méthode de suppression avec la réservation choisie à supprimer
        this.$emit("supprimer", this.reservation);
        this.visible = false;
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

.confirmation-dialog-buttons button:last-child {
  background-color: #ff4d4f;
  color: #fff;
}

  </style>
  