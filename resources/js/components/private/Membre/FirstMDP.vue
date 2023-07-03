<template>
    <div class="change-password-container">
      <h2>Bienvenue sur Tennis Club Booking</h2>
      <h2>Veuillez modifier votre mot de passe</h2>
      <form class="change-password-form">
        <div class="form-group">
          <label for="ancien_mdp">Mot de passe actuel :</label>
          <input type="password" id="ancien_mdp" v-model="form.ancien_mdp" />
        </div>
        <div class="form-group">
          <label for="nouveau_mdp">Nouveau Mot de Passe :</label>
          <input type="password" id="nouveau_mdp" v-model="form.nouveau_mdp" />
        </div>
        <div class="form-group">
          <label for="mdp_confirm">Confirmez votre Nouveau Mot de Passe :</label>
          <input type="password" id="mdp_confirm" v-model="form.mdp_confirm" />
        </div>
      </form>
      <div class="error-message" v-if="Erreur">{{ erreurMessage }}</div>
      <button @click="confirm" class="change-password-button">Changer mon mot de passe</button>
      <br />
      <button @click="returnTo" class="logout-button">Déconnexion</button>
      <Confirmer ref="Confirmer" @confirm="changePassword" />
    </div>
  </template>


<script>
import Confirmer from "../../common/Confirmer.vue";

export default {
    name: "changePassword",
    components: {
        Confirmer,
    },
    data() {
        // Définition des données pour le modèle Vue.js
        return {
            form: {
                id: "",
                ancien_mdp: "",
                nouveau_mdp: "",
                mdp_confirm: "",
            },
            bool: false,
            Erreur : false,
            erreurMessage : ''

            // rôle:"test"
        };
    },

    methods: {
        //Change le mdp avec qlqs règles a suivre
        async confirm() {
            try {
                if(this.form.ancien_mdp === this.form.nouveau_mdp || this.form.nouveau_mdp !== this.form.mdp_confirm)
                {
                    if(this.form.ancien_mdp === this.form.nouveau_mdp)
                    {
                        this.Erreur = true;
                        this.erreurMessage = 'Veuillez choisir un nouveau mot de passe'
                    }
                    else
                    {
                        this.Erreur = true;
                        this.erreurMessage = 'Nouveau mot de passe incorrect'
                    }
                }
                else
                    this.$refs.Confirmer.afficher();
            }
            catch (error) {
                console.error("Erreur lors de la récupération du membre :", error);
            }
        },
        async changePassword() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.form.id = response.data.id;
                    });
                await this.$axios.post("updatepassword", this.form)
                    .then(response => {
                        this.bool = response.data.check
                    });
                if(this.form.ancien_mdp === this.form.nouveau_mdp || this.bool) {
                    await this.$router.push("/home");
                    //Permet d'afficher la bannière
                    if (this.$route.path === '/home') {
                        window.location.reload();
                    }
                }
                else {
                    this.Erreur = true;
                    this.erreurMessage = 'Mot de passe incorrect'
                }
            }
            catch (error) {
                console.error("Erreur lors de la récupération du membre :", error);
            }
        },
        async returnTo() {
            try {
                // Envoi d'une requête POST à l'API pour la déconnexion de l'utilisateur
                await this.$axios.post("/logout");
                // Suppression du token d'authentification de l'utilisateur du stockage local
                localStorage.removeItem("authToken");
                // Redirection vers la page de connexion après une déconnexion réussie
                this.$router.push("/");
            } catch (error) {
                console.error("Erreur lors de la déconnexion :", error);
            }
        },


    }

};
</script>


<style scoped>
.change-password-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f8f8;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 18px;
  margin-bottom: 10px;
}

.change-password-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 20px;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  display: block;
}

input {
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.error-message {
  color: red;
  margin-top: 10px;
}

.change-password-button {
  background-color: #4caf50;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 20px;
}

.change-password-button:hover {
  background-color: #45a049;
}

.logout-button {
  background-color: #e53935;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 10px;
}

.logout-button:hover {
  background-color: #c62828;
}
</style>
