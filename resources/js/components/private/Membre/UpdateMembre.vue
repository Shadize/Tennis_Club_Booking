<template>
    <div class="update-member">
      <h2>Modification d'informations</h2>
      <form>
        <div class="form-group">
          <label for="numero_Affiliation">Numéro d'affiliation :</label>
          <input type="text" id="numero_Affiliation" v-model="membre.numero_Affiliation" readonly />
        </div>
        <div class="form-group">
          <label for="nom">Nom :</label>
          <input type="text" id="nom" v-model="membre.nom" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="prenom">Prénom :</label>
          <input type="text" id="prenom" v-model="membre.prenom" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="sexe">Sexe :</label>
          <select v-model="membre.sexe">
            <option value="G">Homme</option>
            <option value="F">Femme</option>
            <option value="G">Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label for="date_De_Naissance">Date de naissance :</label>
          <input type="date" id="date_De_Naissance" v-model="membre.date_De_Naissance" min="1899-01-01" />
        </div>
        <div class="form-group">
          <label for="email">Email :</label>
          <input type="email" id="email" v-model="membre.email" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="telephone">Téléphone :</label>
          <input type="text" id="telephone" v-model="membre.telephone" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="rue">Rue :</label>
          <input type="text" id="rue" v-model="membre.rue" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="code_Postal">Code postal :</label>
          <input type="text" id="code_Postal" v-model="membre.code_Postal" minlength="1" maxlength="255" />
        </div>
        <div class="form-group">
          <label for="localite">Localité :</label>
          <input type="text" id="localite" v-model="membre.localité" minlength="1" maxlength="255" />
        </div>
      </form>
      <div class="button-group">
        <button class="btn-primary" @click="confirm">Mettre à jour</button>
        <button class="btn-secondary" @click="returnTo">Retour</button>
      </div>
      <Confirmer ref="Confirmer" @confirm="update" />
    </div>
  </template>

<script>

import Confirmer from "../../common/Confirmer.vue";

export default {
    name: "updateMembre",
    components: {
        Confirmer,
    },
    data() {
        // Définition des données pour le modèle Vue.js
        return {
            info: {
                numero_Affiliation: ""
            },
            id: {
                id:""
            },

            membre: {
                numero_Affiliation: "",
                nom: "",
                prenom: "",
                sexe: "",
                date_De_Naissance: "",
                telephone: "",
                rue: "",
                code_Postal: "",
                localité: "",
                email: "",
                actif:"",
                user_Id:"",
            },
            rôle:"test",
            cat:[],
        };
    },
    async created() {
        await this.getInfo();
        await this.fetchCat();
    },

    methods: {

        confirm() {
            this.$refs.Confirmer.afficher();
        },
        async getInfo() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.id.id = response.data.id;
                    });

                await this.$axios.post("getMembreByCredential", this.id)
                    .then(response => {
                        this.info.numero_Affiliation = response.data.membre.numero_Affiliation;
                    });
                await this.$axios.post("/getMembre", this.info)
                    .then(response => {
                        this.membre = response.data.membre;
                    });
                this.membre.date_De_Naissance = this.formattedDate(this.membre.date_De_Naissance);
                await this.$axios.post("getuser", this.membre)
                    .then(response => {
                        this.rôle = response.data.rôle;
                    });
            }
            catch (error) {
                console.error("Erreur lors de la récupération du membre :", error);
            }

        },

        formattedDate(date) {
            const dateObj = new Date(date);
            const year = dateObj.getFullYear();
            const month = `${dateObj.getMonth() + 1}`.padStart(2, '0');
            const day = `${dateObj.getDate()}`.padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        async update()
        {
            await this.$axios.post("/updateMembre", this.membre)
            await this.attribuercat()
            this.returnTo();
    },
        returnTo() {
        this.$router.push("/infosMembre");
    },

        async attribuercat()
        {
        try {
            await this.$axios.post("/supprimerfpc", this.membre)
        }
        catch (error) {
            console.error(error);
        }
            const date = new Date();
            const yearnow = date.getFullYear()
            const dtnmbr = new Date(this.membre.date_De_Naissance);
            const yearmbr = dtnmbr.getFullYear();
            const age = yearnow - yearmbr;
            this.cat.forEach(catégorie => {
                if(age < catégorie.age_Max && age > catégorie.age_Min && (this.membre.sexe === catégorie.sexe || catégorie.sexe === 'N'))
                {
                    const fpc = {'categorie_Id': catégorie.id, 'membre_Id': this.membre.numero_Affiliation}
                    this.$axios.post("/addfpc", fpc)
                        .catch((error) => {
                            console.error(error);
                        });
                }
            })
        },

        async fetchCat() {
                this.$axios
                    .get("/listeCatégorie")
                    .then((response) => {
                        this.cat = response.data;
                    })
                .catch((error) => {
                    console.error(error);
                });
        },

}
};
</script>

<style scoped>
.update-member {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f8f8;
  border-radius: 8px;
}

.update-member h2 {
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

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
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
