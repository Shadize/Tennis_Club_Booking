<!--Composant permettant de réattribuer les catégories aux membres en DB-->

<template>
    <div class="reservation-dialog" v-if="visible">
        <div class="reservation-dialog-content">
            <p>Attribution des catégorie en cours, veuillez patientez</p>
            <p>En attente de l'attribution pour le membre {{this.membre}}</p>
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
            checkSpam:0,
            membre:"",
            role:{rôle:""},
            check: 0,
            membres:[],
            categorieList:[]
        };
    },

    methods: {

        afficher(membres,categories) {
            this.visible = true;
            this.membres = membres
            this.categorieList = categories
            this.checkrole()
            this.attribuercatégorie()
        },
        annuler() {
            this.visible = false;
        },
        confirmer() {
            this.$emit("confirm");
            this.visible = false;
        },

        async attribuercatégorie() {

            if(this.checkSpam === 0 && this.check === 0) {
                this.checkSpam = 1;
                this.$axios.get("/deletecategorie/fait_partie_catégories");
                const date = new Date();
                const yearnow = date.getFullYear();

                for (const membre of this.membres) {
                    const dtnmbr = new Date(membre.date_De_Naissance);
                    const yearmbr = dtnmbr.getFullYear();
                    const age = yearnow - yearmbr;
                    this.membre = membre.numero_Affiliation
                    for (const catégorie of this.categorieList) {
                        if (
                            age < catégorie.age_Max &&
                            age > catégorie.age_Min &&
                            (membre.sexe === catégorie.sexe || catégorie.sexe === 'N')
                        ) {
                            await this.sleep(200);
                            await this.addfpc(catégorie.id, membre.numero_Affiliation);
                            await console.log(catégorie.id, membre.numero_Affiliation);
                        }
                    }
                    console.log('membre numéro', membre.numero_Affiliation, 'terminé' )
                }
                alert('opération terminée');
                this.checkSpam = 0;
                this.visible = false;
            }

        },

        async addfpc(cat_Id,mem_Id)
        {
            const fpc = {'categorie_Id': cat_Id, 'membre_Id': mem_Id}
            await this.$axios.post("/addfpc", fpc)
                .catch((error) => {
                    console.error(error);
                });
        },

        async sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
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
                this.check = 1;
                this.$router.push("/home");
            }
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
