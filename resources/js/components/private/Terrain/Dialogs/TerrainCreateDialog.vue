<template>
    <div class="reservation-dialog" v-if="visible"> <!-- Affiche la boîte de dialogue si visible est vrai -->
        <div class="reservation-dialog-content">
            <form class="reservation-form" @submit.prevent="ajouter"> <!-- Écoute l'événement de soumission du formulaire -->
                <h2>Créer un nouveau terrain</h2>
                <p>Voulez-vous vraiment ajouter un nouveau terrain?</p>
                <div class="form-buttons">
                    <button type="button" @click="annuler">Annuler</button> <!-- Appelle la méthode annuler lors du clic -->
                    <button type="submit">Confirmer</button> <!-- Soumet le formulaire lors du clic -->
                </div>
            </form>
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            visible: false, // Indique si la boîte de dialogue est visible ou non
        };
    },
    methods: {
        afficher() {
            this.visible = true; // Affiche la boîte de dialogue
        },
        annuler() {
            this.visible = false; // Masque la boîte de dialogue
        },
        ajouter() {
            // Effectuer les actions nécessaires pour ajouter la réservation
            this.$axios
                .post('/terrains') // Effectue une requête POST pour créer un nouveau terrain
                .then(response => {
                    console.log('Terrain créé avec succès');
                    this.$emit("ajouter"); // Émet un événement "ajouter" pour notifier le composant parent
                    this.visible = false; // Masque la boîte de dialogue
                })
                .catch(error => {
                    console.error("Une erreur s'est produite lors de la création du terrain", error);
                });
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

.form-buttons button:first-child:hover {
  background-color: #e22121;
  color: #fff;
}

.form-buttons button:last-child {
  background-color: #1ac030;
  color: #fff;
}

.form-buttons button:last-child:hover {
  background-color: #4dff65;
}

.form-buttons button:hover {
    background-color: #eaeaea;
}
</style>
