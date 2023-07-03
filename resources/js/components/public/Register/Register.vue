<template>
    <div class="reservation-dialog" v-if="visible">
        <div class="reservation-dialog-content">

      <form class="reservation-form" @submit.prevent="register">
          <h2>Ajouter un membre</h2>
        <!-- Champ pour le nom de l'utilisateur -->
          <div class="form-field">
        <div>
          <label for="numero_Affiliation">numero_Affiliation :</label>
          <input type="number" id="numero_Affiliation" v-model="form.numero_Affiliation" min="1000000" max="9999999" />
        </div>
          <div>
              <label for="nom">nom :</label>
              <input type="text" id="nom" v-model="form.nom"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="prenom">prenom :</label>
              <input type="text" id="prenom" v-model="form.prenom"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="sexe">sexe :</label>
              <select v-model="form.sexe">
                  <option value="G">Homme</option>
                  <option value="F">Femme</option>
                  <option value="G">Autre</option>
              </select>
          </div>
          <div>
              <label for="date_De_Naissance">date_De_Naissance :</label>
              <input type="date" id="date_De_Naissance" v-model="form.date_De_Naissance"  min="1899-01-01" />
          </div>
          <div>
              <label for="email">email :</label>
              <input type="email" id="email" v-model="form.email"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="telephone">telephone :</label>
              <input type="text" id="telephone" v-model="form.telephone"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="rue">rue :</label>
              <input type="text" id="rue" v-model="form.rue"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="code_Postal">code_Postal :</label>
              <input type="text" id="code_Postal" v-model="form.code_Postal"  minlength="1" maxlength="255"/>
          </div>
          <div>
              <label for="localite">localite :</label>
              <input type="text" id="localite" v-model="form.localite"  minlength="1" maxlength="255"/>
          </div>
             <div>
                  <label for="classement">classement :</label>
                 <select v-model="form.classement">
                     <option value="N.C">Sélectionnez une option</option>
                     <option v-for="classement in classementList" :value="classement">{{ classement }}</option>
                 </select>
              </div>
        <!-- Champ pour le mot de passe de l'utilisateur -->
        <div>
          <label for="password">Mot de passe :</label>
          <input type="password" id="password" v-model="form.password" required />
        </div>
        <!-- Champ pour la confirmation du mot de passe de l'utilisateur -->
        <div>
          <label for="password_confirmation">Confirmation du mot de passe :</label>
          <input type="password" id="password_confirmation" v-model="form.password_confirmation"  />
        </div>
          </div>
          <div class="form-buttons">
        <!-- Bouton pour soumettre le formulaire d'inscription -->
        <button type="submit">Ajouter Membre</button>
          <button @click="annuler">annuler</button>
          </div>
      </form>

        </div>
    </div>
</template>

<script>


  export default {
    name: "register",
    data() {
      // Définition des données pour le modèle Vue.js
      return {
          mem: [],
          cat: [],
          visible: false,
          role: {
              rôle:""
          },
        form: {
            numero_Affiliation: "",
            nom: "",
            prenom: "",
            sexe: "",
            date_De_Naissance: "",
            telephone: "",
            rue: "",
            code_Postal: "",
            localite: "",
            email: "",
            classement:"N.C",
            password: "",
            password_confirmation: "",
            id:"",
        },
          classementList:['A', 'B-15.4' , 'B-15.2' , 'B-15.1', 'B15', 'B-4/6', 'B-2/6' , 'B0' , 'B+2/6' , 'B+4/6' , "C15" ,
              'C15.1' , 'C15.2' ,'C15.3', 'C15.4' , 'C30' , 'C30.1','C30.2' , 'C30.3'  ,'C30.4'  , 'C30.5', 'N.C']
      };
    },
      async created() {
         await this.fetchCat()
         await this.checkrole()
      },

    methods: {

        afficher() {
            this.visible = true;
        },
        annuler() {
            this.visible = false;
        },

      async register() {
        try {
            if(this.role.rôle !== 'admin')
                this.$router.push("/home");
            else
            {
            if((await this.$axios.post("/check_Id", this.form)).data)
            {            // On crée le User
                const response2 = await this.$axios.post("/register", this.form);

                // On récupère l'ID du user qui vient d'être créé et
                // le transmet dans la variable qu'on transmet au controller pour créer un membre
                this.form.id = response2.data.user.id


                // On crée le membre
                const response = await this.$axios.post("/membreC", this.form);

                // Redirection vers la page de connexion après une inscription réussie
                this.visible = false;
                this.mem.push(response.data.membre)
                await this.attribuercat();
              location.reload();
            }
            else
                console.log('le membre existe déjà')
        } }catch (error) {
          console.error("Erreur lors de l'inscription :", error);
        }
      },
        async attribuercat()
        {
            const date = new Date();
            const yearnow = date.getFullYear()
            this.mem.forEach(membre => {
                const dtnmbr = new Date(membre.date_De_Naissance);
                const yearmbr = dtnmbr.getFullYear();
                const age = yearnow - yearmbr;
                this.cat.forEach(catégorie => {
                    if(age < catégorie.age_Max && age > catégorie.age_Min && (membre.sexe === catégorie.sexe || catégorie.sexe === 'N'))
                    {
                        const fpc = {'categorie_Id': catégorie.id, 'membre_Id': membre.numero_Affiliation}
                        this.$axios.post("/addfpc", fpc)
                            .catch((error) => {
                                console.error(error);
                            });
                    }
                })
            })
            console.log('opération terminée')

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

        async checkrole() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.role.rôle = response.data.rôle;
                    });
            }
            catch (error) {
                console.error("Erreur lors de la récupération du user :", error);
            }
            if(this.role.rôle !== 'admin')
            {
                this.$router.push("/home");
            }
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
