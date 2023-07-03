<template>
    <div class="reservation-dialog" v-if="visible">
        <div class="reservation-dialog-content">
            <p>Etes-vous sûr de vouloir faire cela?</p>
            <div class="form-buttons">
                <!-- Bouton pour soumettre le formulaire d'inscription -->
                <button @click="confirmer">confirmer</button>
                <button @click="annuler">annuler</button>
            </div>
        </div>
    </div>
</template>

<script>


export default {
    name: "register",
    data() {
        // Définition des données pour le modèle Vue.js
        return {
            visible: false,
        };
    },

    methods: {

        afficher() {
            this.visible = true;
        },
        annuler() {
            this.visible = false;
        },
        confirmer() {
            this.$emit("confirm");
            this.visible = false;
        },

    },


};
</script>

<style>

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

.form-field {
    align-items: start;
    justify-content: left;
}

.form-field label {
    margin-right: 10px;
    width: 100px;
    white-space: nowrap;
}

.form-field select,
.form-field input {
    width: 150px;
    padding: 5px;
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
    background-color: #ff4d4f;
    color: #fff;
}

.form-buttons button:hover {
    background-color: #eaeaea;
}

.delete-player {
    padding: 0;
    width: 20px;
    height: 20px;
    font-size: 12px;
    border: none;
    background-color: #ff4d4f;
    color: #ffffff;
    border-radius: 50%;
    cursor: pointer;
    margin-left: 10px;
}

.delete-player:hover {
    background-color: #ff3333;
}

</style>
