<template>
    <!-- Le template contient le contenu de la page d'accueil. -->
    <div class="home-container">
      <h1>Page d'accueil</h1>
      <div class="button-group">
        <button @click="infosMembre">Afficher infos</button>
        <button @click="logout">Déconnexion</button>
        <button @click="goToList">Membre liste</button>
      </div>

      <div class="image-container">
      <img src="../../common/Assets/tennis6.png" alt="Description de l'image" class="home-image" />
    </div>

      <Register ref="Register" />

      <!-- Le titre de la page d'accueil est affiché ici. -->
    </div>
  </template>

  <script>
  import Register from "../../public/Register/Register.vue";
  import SupprimerMembre from "../Membre/SupprimerMembre.vue";
  import ModifMembre from "../Membre/ModifMembreAdmin.vue";

  export default {
    components: {
      Register,
      SupprimerMembre,
      ModifMembre,
    },
    name: "Home",
    data() {
      return {
        membres: [],
        catégories: [],
        test: [
          { nom: 'JF/JG-9 ans', age_Max: 9, age_Min: 0, sexe: "N" },
          { nom: 'JF-11 ans', age_Max: 11, age_Min: 10, sexe: "F" },
          { nom: 'JF-13 ans', age_Max: 13, age_Min: 12, sexe: "F" },
          { nom: 'JF-15 ans', age_Max: 15, age_Min: 14, sexe: "F" },
          { nom: 'JF-17 ans', age_Max: 17, age_Min: 16, sexe: "F" },
          { nom: 'Dames 25', age_Max: 200, age_Min: 25, sexe: "F" },
          { nom: 'Dames 35', age_Max: 200, age_Min: 35, sexe: "F" },
          { nom: 'Dames 45', age_Max: 200, age_Min: 45, sexe: "F" },
          { nom: 'Dames 55', age_Max: 200, age_Min: 55, sexe: "F" },
          { nom: 'Dames', age_Max: 200, age_Min: 16, sexe: "F" },
          { nom: 'JG-11 ans', age_Max: 11, age_Min: 10, sexe: "G" },
          { nom: 'JG-13 ans', age_Max: 13, age_Min: 12, sexe: "G" },
          { nom: 'JG-15 ans', age_Max: 15, age_Min: 14, sexe: "G" },
          { nom: 'JG-17 ans', age_Max: 17, age_Min: 16, sexe: "G" },
          { nom: 'Messieurs 35', age_Max: 200, age_Min: 35, sexe: "G" },
          { nom: 'Messieurs 55', age_Max: 200, age_Min: 55, sexe: "G" },
          { nom: 'Messieurs 60', age_Max: 200, age_Min: 60, sexe: "G" },
          { nom: 'Messieurs 65', age_Max: 200, age_Min: 65, sexe: "G" },
          { nom: 'Messieurs 70', age_Max: 200, age_Min: 70, sexe: "G" },
          { nom: 'Messieurs', age_Max: 200, age_Min: 16, sexe: "G" },
        ],
      };
    },
    created() {
      this.fetchMembres();
      this.fetchCatégories();
    },
    methods: {
      register() {
        this.$refs.Register.afficher();
      },
      infosMembre() {
        this.$router.push("/infosMembre");
      },
      logout() {
        this.$router.push("/logout");
      },
      async goToList() {
        this.$router.push("/listeMembre");
      },
      async fetchMembres() {
        this.$axios
          .get("/listeMembre")
          .then((response) => {
            this.membres = response.data;
          })
          .catch((error) => {
            console.error(error);
          });
      },
      async fetchCatégories() {
        this.$axios
          .get("/listeCatégorie")
          .then((response) => {
            this.catégories = response.data;
          })
          .catch((error) => {
            console.error(error);
          });
      },
    },
  };
  </script>

  <style scoped>
  .home-container {
    text-align: center;
  }

  .button-group {
    margin-top: 20px;
  }

  button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10px;
  }

  button:hover {
    opacity: 0.8;
  }

  h1 {
    font-size: 24px;
    margin-bottom: 20px;
  }

  body {
  background-image: url(../../common/Assets/tennis.jpg);
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.image-container {
  margin-top: 50px;
  text-align: center;
}

.home-image {
    height: auto;
    border-radius: 8px;
    width: 60%;
}


  /* Add your custom styles here */
  </style>
