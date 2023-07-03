<template xmlns="http://www.w3.org/1999/html">
    <div class="user-info">
        <h2>Informations</h2>
        <div>
            <label>Numéro d'affiliation :</label>
            <span>{{ membre.numero_Affiliation }}</span>
        </div>
        <div>
            <label>Nom :</label>
            <span>{{ membre.nom }}</span>
        </div>
        <div>
            <label>Prénom :</label>
            <span>{{ membre.prenom }}</span>
        </div>
        <div>
            <label>Sexe :</label>
            <span>{{ membre.sexe === 'G'? 'Homme' : (membre.sexe ==='F' ? 'Femme' : 'Autre') }}</span>
        </div>
        <div>
            <label>Date de naissance :</label>
            <span>{{ membre.date_De_Naissance }}</span>
        </div>
        <div>
            <label>Email :</label>
            <span>{{ membre.email }}</span>
        </div>
        <div>
            <label>Téléphone :</label>
            <span>{{ membre.telephone }}</span>
        </div>
        <div>
            <label>Rue :</label>
            <span>{{ membre.rue }}</span>
        </div>
        <div>
            <label>Code postal :</label>
            <span>{{ membre.code_Postal }}</span>
        </div>
        <div>
            <label>Localité :</label>
            <span>{{ membre.localité }}</span>
        </div>
        <div>
            <label>Ordre de cotisation :</label>
            <span>{{ membre.ordre_De_Cotisation ? 'Oui' : 'Non' }}</span>
        </div>
        <div>
            <label>Classement :</label>
            <span>{{ membre.classement }}</span>
        </div>
        <table class="categories-table">
    <thead>
        <tr>
            <th>Catégorie</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="fpc in fpcs" :key="fpc">
            <td>{{ fpc }}</td>
        </tr>
    </tbody>
</table>

        <button @click="update">Modifier mes informations</button>
        <button @click="changePassword">Changer mon mot de passe</button>
        <button @click="returnTo">Retour</button>
    </div>
</template>


<script>
export default {
    name: "infosMembre",
    data() {
        // Définition des données pour le modèle Vue.js
        return {
            info: {
                numero_Affiliation: "1234567"
            },

            id: {
                id:""
            },
            fpcs: [],
            cat:[],
            list:[],
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
                ordre_De_Cotisation:"",
                classement:"",
            },
            // rôle:"test"
        };
    },
     async created() {
        await this.getInfo();
         await this.fetchCat();
         await this.affichercat();
     },

    methods: {
        async getInfo() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.id.id = response.data.id;
                    });

                await this.$axios.post("/getMembreByCredential", this.id)
                    .then(response => {
                        this.info.numero_Affiliation = response.data.membre.numero_Affiliation;
                    });

                await this.$axios.post("/getMembre", this.info)
                    .then(response => {
                        this.membre = response.data.membre;
                    });

                this.membre.date_De_Naissance = this.formattedDate(this.membre.date_De_Naissance);
                await this.$axios.post("/getuser", this.membre)
                    .then(response => {
                       this.rôle = response.data.rôle;
                    });
            }
            catch (error) {
                             console.error("Erreur lors de la récupération du membre :", error);
            }

        },
//Formattage de la date pour pouvoir l'afficher
        formattedDate(date) {
            const dateObj = new Date(date);
            const year = dateObj.getFullYear();
            const month = `${dateObj.getMonth() + 1}`.padStart(2, '0');
            const day = `${dateObj.getDate()}`.padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        update() {
            this.$router.push("/updateMembre");
        },

        changePassword() {
            this.$router.push("/changePassword");
        },
        returnTo() {
            this.$router.push("/home");
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

        async affichercat() {
            this.fpcs = [];
            this.list = [];
            await this.$axios.post("/listoffpc", this.membre)
                .then((response) =>{
                    this.list = response.data;
                })
            this.list.forEach(fpc =>{
                this.cat.forEach(cat =>{
                    if(cat.id === fpc.categorie_Id)
                    {
                        this.fpcs.push(cat.nom);
                        return;
                    }
                })


            })
        },

    }

};
</script>

<style scoped>
.user-info {
    width: 80%;
    margin: 0 auto;
    background-color: #f9f9f9;
    border: 1px solid #e3e3e3;
    border-radius: 5px;
    padding: 20px;
}

.user-info label {
    font-weight: bold;
    color: #333;
    margin-right: 10px;
}

.user-info span {
    color: #666;
}

.user-info ul {
    list-style-type: none;
    padding-left: 0;
}

.user-info button {
    margin-top: 20px;
    margin-right: 10px;
    padding: 10px 20px;
    color: #fff;
    background-color: #337ab7;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.user-info button:hover {
    background-color: #286090;
}

.categories-table {
    width: 33%;
    margin: 20px auto;
    border-collapse: collapse;
}

.categories-table thead {
    background-color: #337ab7;
    color: white;
}

.categories-table th, 
.categories-table td {
    padding: 15px;
    border: 1px solid #ddd;
    text-align: center;
}

.categories-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.categories-table tbody tr:hover {
    background-color: #ddd;
    cursor: pointer;
}
</style>

