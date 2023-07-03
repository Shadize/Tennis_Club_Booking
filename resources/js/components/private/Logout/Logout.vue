<template>
  <div class="terrain-list">
    <h2>Déconnexion</h2>
    <!-- Bouton pour la déconnexion de l'utilisateur -->
    <button @click="logout">Se déconnecter</button>
  </div>
</template>

<script>
import Banniere from "../../common/Banniere/Banniere.vue"
export default {
    components:{
        Banniere
    },
  name: "Logout",
  methods: {
    async logout() {
      try {
        // Envoi d'une requête POST à l'API pour la déconnexion de l'utilisateur
        await this.$axios.post("/logout");
        // Suppression du token d'authentification de l'utilisateur du stockage local
        localStorage.removeItem("authToken");
        // Redirection vers la page de connexion après une déconnexion réussie
          //this.$refs.Banniere.cacher()
        await this.$router.push("/");
        //Retire l'affichage de la bannière sur la page de Login
          if (this.$route.path === '/') {
              window.location.reload();
          }
      } catch (error) {
        console.error("Erreur lors de la déconnexion :", error);
      }
    },
  },
};
</script>

<style>
/* Styles pour la liste de terrain */
.terrain-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 20px;
}

/* Style pour le titre "Déconnexion" */
.terrain-list h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 20px;
}

/* Style pour le bouton "Se déconnecter" */
.terrain-list button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

/* Style au survol du bouton "Se déconnecter" */
.terrain-list button:hover {
  background-color: #0056b3;
}

/* Styles pour la bannière */
.banniere {
  /* Ajoutez ici les styles spécifiques à la bannière */
}
</style>
