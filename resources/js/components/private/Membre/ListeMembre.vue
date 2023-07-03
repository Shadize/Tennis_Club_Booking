<template>

    <div class="terrain-list">
        <div class="toolbar">
            <h1>Liste des Membres</h1>

            <div class="toolbar-sort-buttons">
                <button @click="sortByName" class="toolbar-button">Trier par Nom/Prénom</button>
                <button @click="sortByClassement" class="toolbar-button">Trier par Classement</button>
                <button @click="sortByAffiliation" class="toolbar-button">Trier par N Affiliation</button>
            </div>

            <div class="toolbar-search">
                <input type="text" id="recherche" v-model="this.nomrech" class="search-input" placeholder="Rechercher un nom/prénom">
                <button @click="rechercher" class="search-button">Rechercher</button>
            </div>

            <div class="toolbar-select">
                <label for="classement">Classement : </label>
                <select v-model="this.classement" class="select-input">
                    <option value="">Tous les classements</option>
                    <option v-for="classement in classement_List" :value="classement">{{ classement }}</option>
                </select>

                <label for="catégorie">Catégorie : </label>
                <select v-model="this.categorie" class="select-input">
                    <option value="">Toutes les catégories</option>
                    <option v-for="categorie in categorieList" :value="categorie">{{ categorie.nom }}</option>
                </select>
            </div>
        </div>
        <table class="tableau">
            <thead>
            <tr class="ligne_tableau ligne_head">
                <th class="case_tableau case_head">N Affiliation</th>
                <th class="case_tableau case_head">Nom</th>
                <th class="case_tableau case_head">Prénom</th>
                <th class="case_tableau case_head">classement</th>
            </tr>
            </thead>
            <tbody>
            <tr class="ligne_tableau souligner"  v-for="membre in membres" :key="membre.numero_Affiliation">
                <td class="case_tableau">{{ membre.numero_Affiliation }}</td>
                <td class="case_tableau">{{ membre.nom }}</td>
                <td class="case_tableau">{{ membre.prenom }}</td>
                <td class="case_tableau">{{ membre.classement }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>

import Register from "../../public/Register/Register.vue";
import SupprimerMembre from "../../private/Membre/SupprimerMembre.vue";
import ModifMembre from "../../private/Membre/ModifMembreAdmin.vue";

export default {
    components: {
        Register,
        SupprimerMembre,
        ModifMembre,
    },
    data() {
        return {
            membres: [], // Les terrains seront récupérés depuis l'API
            myId:{
                id:""
            },
            nomrech:"",
            role: {
                rôle:""
            },
            classement:"",
            membres_backup: [],
            membres_backup2: [],
            categorieList:[],
            fpcs:[],
            categorie:"",
            catList: [
                    'JF/JG-9 ans',
                    'JF-11 ans',
                    'JF-13 ans',
                    'JF-15 ans',
                    'JF-17 ans',
                    'Dames 25',
                    'Dames 35',
                    'Dames 45',
                    'Dames 55',
                    'Dames',
                    'JG-11 ans',
                    'JG-13 ans',
                    'JG-15 ans',
                    'JG-17 ans',
                    'Messieurs 35',
                    'Messieurs 55',
                    'Messieurs 60',
                    'Messieurs 65',
                    'Messieurs 70',
                    'Messieurs'
                    ],
            classement_List:['A', 'B-15.4' , 'B-15.2' , 'B-15.1', 'B15', 'B-4/6', 'B-2/6' , 'B0' , 'B+2/6' , 'B+4/6' , "C15" ,
                'C15.1' , 'C15.2' ,'C15.3', 'C15.4' , 'C30' , 'C30.1','C30.2' , 'C30.3'  ,'C30.4'  , 'C30.5', 'N.C'],
            classementList:{
                'A':1,
                'B-15.4':2 ,
                'B-15.2':3 ,
                'B-15.1':4,
                'B15':5,
                'B-4/6':6,
                'B-2/6':7 ,
                'B0':8 ,
                'B+2/6':9 ,
                'B+4/6':10 ,
                "C15":11,
                'C15.1':12 ,
                'C15.2':13,
                'C15.3':14,
                'C15.4':15 ,
                'C30' :16,
                'C30.1':17,
                'C30.2':18 ,
                'C30.3':19  ,
                'C30.4' :20 ,
                'C30.5':21,
                'N.C':22
            },
            valid:0,
            coti:0

        };
    },
    async created() {

        // Appel à l'API pour récupérer les terrains
        await this.checkrole();
        await this.fetchMembres();
        await this.fetchCat();

    },
    methods: {

        async checkrole() {
            try {
                await this.$axios.get("/user")
                    .then(response => {
                        this.myId.id = response.data.id;
                        this.role.rôle = response.data.rôle;
                    });
                await this.$axios.post("/getMembreByCredential", this.myId)
                    .then(response => {
                         this.valid = response.data.membre.actif;
                         this.coti = response.data.membre.ordre_De_Cotisation;
                    })
            }
            catch (error) {
                console.error("Erreur lors de la récupération du user :", error);
            }
            if((this.valid === 0 || this.coti === 0) && this.role.rôle === 'membre')
            {
                this.$router.push("/home");
            }

        },


        fetchMembres() {
            this.$axios
                .post("/listeMembreDispo")
                .then((response) => {
                    this.membres = response.data;
                    this.membres_backup = this.membres.slice()
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        sortByName() {
            this.membres.sort(this.byClassement)
            this.membres.sort(this.byName)

        },

        sortByClassement() {
            this.membres.sort(this.byClassement)
        },

        sortByAffiliation() {
            this.membres.sort(this.byAffiliation)
        },

        byAffiliation(membre1, membre2) {
            if (membre1.numero_Affiliation > membre2.numero_Affiliation){
                return 1;
            }
            else {
                if (membre1.numero_Affiliation === membre2.numero_Affiliation){
                    return 0;
                }
                else
                    return -1;
            }
        },

        byClassement(membre1, membre2) {

            if (this.classementList[membre1.classement] > this.classementList[membre2.classement]){
                return 1;
            }
            else {
                if (this.classementList[membre1.classement] === this.classementList[membre2.classement]){
                    return 0;
                }
                else
                    return -1;
            }

        },


        byName(membre1, membre2) {

            const membre1nom = membre1.nom.toString().toUpperCase();
            const membre2nom = membre2.nom.toString().toUpperCase();
            const membre1prenom = membre1.prenom.toString().toUpperCase();
            const membre2prenom = membre2.prenom.toString().toUpperCase();

            if (membre1nom > membre2nom)
            {
                return 1;
            }
            else
            {
                if (membre1nom === membre2nom)
                {
                    if (membre1prenom > membre2prenom)
                    {
                        return 1;
                    }
                    else {
                        if (membre1prenom === membre2prenom)
                        {
                            return 0;
                        }
                        else
                            return -1;
                    }
                }
                else
                    return -1;

            }

        },

        rechercher() {
            this.membres = this.membres_backup.slice()
            if(this.classement === "" && this.nomrech === "" && this.categorie === ""){
            }
            else
            {
                this.perClassement();
                this.seekByName();
                this.seekByCategorie();
            }
        },

        perClassement() {
            const membres = [];
            if(this.classement === "");
            else {

                this.membres.forEach(membre => {
                    if (membre.classement === this.classement) {
                        membres.push(membre);
                    }
                })
                this.membres = membres.slice()
            }

        },

        seekByName() {
            if(this.nomrech === "");
            else {
                const rech = this.nomrech.toLowerCase();
                this.membres = this.membres.filter(membre => (
                    membre.nom.toLowerCase() + ' ' + membre.prenom.toLowerCase() + ' ' + membre.nom.toLowerCase())
                    .includes(rech));
            }

        },
        async seekByCategorie() {
            await this.$axios.post("/listoffpcbycat", this.categorie)
                .then((response) =>{
                    this.fpcs = response.data;
                })

            const membres = [];
            if(this.categorie === "");
            else
            {
                this.fpcs.forEach(fpc =>{
                    this.membres.forEach(membre=>{
                        console.log(fpc.membre_Id)
                        console.log(membre.numero_Affiliation)
                        if(membre.numero_Affiliation === fpc.membre_Id)
                            membres.push(membre)
                    })
                })
                this.membres = membres.slice();
            }
            console.log(membres)
        },

        async fetchCat() {
            this.$axios
                .get("/listeCatégorie")
                .then((response) => {
                    this.categorieList = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },


    },


};
</script>

<style>
@import '../../common/Tableau.CSS';

.toolbar {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.toolbar h1 {
    text-align: center;
    color: black;
    margin-bottom: 30px;

}

.toolbar-sort-buttons {
    display: flex;
    justify-content: space-evenly;
    margin: 10px 0;
}

.toolbar-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.toolbar-button:hover {
    background-color: #0056b3;
}

.toolbar-search {
    display: flex;
    justify-content: center;
    margin: 10px 0;
}

.search-input {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-right: 10px;
}

.search-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-button:hover {
    background-color: #0056b3;
}

.toolbar-select {
    display: flex;
    justify-content: space-evenly;
    margin: 10px 0;
}

.select-input {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Responsive styles */
@media (max-width: 768px) {
    .toolbar-sort-buttons, .toolbar-select {
        flex-direction: column;
    }

    .toolbar-button, .search-input, .search-button, .select-input {
        margin-bottom: 10px;
    }

    .toolbar-search {
        flex-direction: column;
        align-items: center;
    }

    .search-input {
        margin-right: 0;
    }
}

</style>
