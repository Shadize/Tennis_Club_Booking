<template>
    <div class="change-password">
      <h2>Modifier le mot de passe</h2>
      <form>
        <div class="form-group">
          <label for="ancien_mdp">Mot de passe actuel :</label>
          <input type="password" id="ancien_mdp" v-model="form.ancien_mdp" minlength="5" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="nouveau_mdp">Nouveau mot de passe :</label>
          <input type="password" id="nouveau_mdp" v-model="form.nouveau_mdp" minlength="5" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="mdp_confirm">Confirmez votre nouveau mot de passe :</label>
          <input type="password" id="mdp_confirm" v-model="form.mdp_confirm" minlength="5" maxlength="255" />
        </div>
      </form>
      <div class="error-message" v-if="Erreur">{{ erreurMessage }}</div>
      <div class="button-group">
        <button class="btn-primary" @click="confirm">Changer mon mot de passe</button><br/>
        <button class="btn-secondary" @click="returnTo">Retour</button>
      </div>
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
        };
    },

    methods: {
        //Confirm le changement de mdp (avec qlqs règles a suivre)
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
                    if(this.form.ancien_mdp === this.form.nouveau_mdp || this.bool)
                    this.returnTo();
                    else {
                        this.Erreur = true;
                        this.erreurMessage = 'Mot de passe incorrect'
                    }
                }
            catch (error) {
                console.error("Erreur lors de la récupération du membre :", error);
            }
        },
        returnTo() {
            this.$router.push("/infosMembre");
        },


    }

};
</script>

<style scoped>
.change-password {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f8f8;
  border-radius: 8px;
}

.change-password h2 {
  font-size: 24px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.error-message {
  color: red;
  margin-bottom: 10px;
}

.button-group {
  margin-top: 20px;
  text-align: right;
  display: flex;
  justify-content: space-between;
}

.button-group button {
  padding: 10px 20px;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
}

.button-group .btn-primary {
  background-color: #007bff;
  color: #fff;
  border: none;
}

.button-group .btn-secondary {
  background-color: #ccc;
  color: #fff;
  border: none;
  margin-right: 10px;
}

.button-group button:hover {
  opacity: 0.8;
}
</style>
