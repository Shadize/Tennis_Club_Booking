<template>
  <div class="login-container">
    <h2>Connexion</h2>
    <form @submit.prevent="login" class="login-form">
      <div class="form-group">
        <label for="id">Identifiant :</label>
        <input type="number" id="id" v-model="form.numero_Affiliation" required />
      </div>
      <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" v-model="form.password" required />
      </div>
      <div class="error-message" v-if="error">
        Mot de passe ou numéro d'affiliation incorrect
      </div>
      <button type="submit" class="login-button">Se connecter</button>
    </form>
  </div>
</template>

<script>


  export default {
    name: "Login",
    data() {
      // Définition des données pour le modèle Vue.js
      return {
        form: {
            numero_Affiliation: "",
          password: "",
        },
          request: {
            id:"",
              password: "",
          },
          idCheck:"",
          id:true,
          isPageReloading : false,
          error:false
      };
    },

      created() {
        this.checkToken();
      },

    methods: {


      async login() {
        try {
            this.request.password = this.form.password;
            await this.$axios.post("/getMembre", this.form)
                .then(response => {
                    this.request.id = response.data.membre.user_Id;
                });

          // Envoi d'une requête POST à l'API pour la connexion de l'utilisateur avec les données du formulaire
          const response = await this.$axios.post("/login", this.request);

          // Stockage du token d'authentification de l'utilisateur dans le stockage local
          localStorage.setItem("authToken", response.data.token);

          //On va check si le User c'est déjà connecté au moins une fois
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.id = response.data.mdp_Modifier;
                    });
            }
            catch (error) {
            }
            await this.redirection();

        } catch (error) {
          console.error("Erreur lors de la connexion :", error);
          this.error = true;
        }
      },

        async checkToken() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.idCheck = response.data.id;
                    });
            }
            catch (error) {
            }
            if(this.idCheck)
            {
                await this.redirection();
            }
        },

        async redirection() {
            //On va check si le User c'est déjà connecté au moins une fois
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.id = response.data.mdp_Modifier;
                    });
            }
            catch (error) {
            }
            // Redirection vers la page d'accueil après une connexion réussie
            if(this.id)
            {
                    await this.$router.push("/home");
                    if (this.$route.path === '/home') {
                        window.location.reload();
                    }
            }
            else
            {
                this.$router.push("/firstMDP")
            }
        },



    },
  };
</script>

<style scoped>
  .login-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f8f8;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .login-form {
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
    border-radius:8px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  .error-message {
    color: red;
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .login-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }

  .login-button:hover {
    background-color: #0056b3;
  }
</style>