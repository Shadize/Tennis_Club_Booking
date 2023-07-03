<template>
    <div class="banniere" >
    <aside class="bann, menu">

        <button @click="goToHome" class="logo">Home</button>
        <button @click="goToList">Membres</button>
        <button @click="goToReservation">Mes réservations</button>
        <button @click="goToTerrain" v-if="admin">Terrains</button>
    </aside>
    <aside class="bann, profil">
        <button @click="infosMembre">Mon profil</button>
        <button @click="logout">déconnexion</button>
    </aside>
    </div>
</template>

<script>
// Le script contient la logique de la page d'accueil.


export default {
    // Le nom de la page d'accueil est défini ici.
    name: "banniere",
    data() {
        return {
            role:{rôle:""},
            //Modifie les redirection et l'affichage du bouton terrains
            admin:false,
            idCheck:"",
        }
    },
    async created() {
        await this.checkrole();
    },

    methods:
        {
            //Méthode de redirections
            infosMembre() {
                this.$router.push("/infosMembre");
            },
            logout() {
                this.$router.push("/logout");
            },
            async getUser() {
                this.$router.push("/listeMembreAdmin");
            },
            //Redirige vers listeAdmin si l'utilisateur est un admin sinon, go to list membre
            async goToList(){
                if(this.admin)
                    this.$router.push("/listeMembreAdmin");
                else
                this.$router.push("/listeMembre");
            },
            goToHome(){
                this.$router.push("/home");
            },
            goToReservation(){
                this.$router.push("/reservations");
            },
            async goToTerrain(){
                if(this.admin)
                this.$router.push("/terrains");
            },
            //Check si l'utilisateur est un admin
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
                if(this.role.rôle === 'admin')
                {
                    this.admin = true;
                }
                else
                {
                    this.admin = false;
                }
            },
        }
};

</script>

<style scoped>
@import '../../common/Banniere/Banniere.css';
</style>
