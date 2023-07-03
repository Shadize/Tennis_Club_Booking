<template>
    <div class="reservation-dialog" v-if="visible">
        <div class="reservation-dialog-content">
        <form >
            <!-- Champ pour le nom de l'utilisateur -->
            <div class="form-group">
                <label for="numero_Affiliation">numero_Affiliation :</label>
                <input type="text" id="numero_Affiliation" v-model="membre.numero_Affiliation" readonly />
            </div>
            <div class="form-group">
                <label for="nom">nom :</label>
                <input type="text" id="nom" v-model="membre.nom" minlength="1" maxlength="255" />
            </div>
            <div class="form-group">
                <label for="prenom">prenom :</label>
                <input type="text" id="prenom" v-model="membre.prenom" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
                <label for="sexe">sexe :</label>
                <select v-model="membre.sexe">
                    <option value="G">Homme</option>
                    <option value="F">Femme</option>
                    <option value="G">Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="date_De_Naissance">date_De_Naissance :</label>
                <input type="date" id="date_De_Naissance" v-model="membre.date_De_Naissance" min="1899-01-01"/>
            </div>
            <div class="form-group">
                <label for="email">email :</label>
                <input type="email" id="email" v-model="membre.email" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
                <label for="telephone">telephone :</label>
                <input type="text" id="telephone" v-model="membre.telephone" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
                <label for="rue">rue :</label>
                <input type="text" id="rue" v-model="membre.rue" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
                <label for="code_Postal">code_Postal :</label>
                <input type="text" id="code_Postal" v-model="membre.code_Postal" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
                <label for="localite">localite :</label>
                <input type="text" id="localite" v-model="membre.localité" minlength="1" maxlength="255"/>
            </div>
            <div class="form-group">
            <label for="role">role :</label>
            <select v-model="role">
                <option v-for="role in roleList" :value="role">{{ role }}</option>
            </select>
            </div>
            <div class="form-group">
                <label for="classement">classement :</label>
                <select v-model="membre.classement">
                    <option value="N.C">Sélectionnez une option</option>
                    <option v-for="classement in classementList" :value="classement">{{ classement }}</option>
                </select>

            </div>
            <br/>
            <table class="categories-table" >
<!--                style="width:50% ; align-self:center"-->
                <thead>
                <tr class="ligne_tableau ligne_head" >
                    <th class="case_tableau case_head" style="padding:2px;margin: 5px">Catégorie</th>
                </tr>
                </thead>
                <tbody>
                <tr class="ligne_tableau souligner"  v-for="fpc in fpcs" :key="fpc">
                    <td class="case_tableau">{{ fpc }}</td>
                </tr>
                </tbody>
            </table>
        </form>
            <div class="button-group">
        <button @click="confirm" class="btn-primary">Update</button>
        <button @click="returnTo" class="btn-secondary">Retour</button>
            </div>

        <Confirmer ref="Confirmer" @confirm="update" />
    </div>
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

            role: {
                rôle:""
            },
            fpcs: [],
            mem:[],
            cat:[],
            list:[],
            seek:0,
            id: {
                id:""
            },
            roleList:['membre','admin'],
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
                ordre_De_Cotisation:true,
                classement:"",

            },
            visible: false,
            classementList:['A', 'B-15.4' , 'B-15.2' , 'B-15.1', 'B15', 'B-4/6', 'B-2/6' , 'B0' , 'B+2/6' , 'B+4/6' , "C15" ,
            'C15.1' , 'C15.2' ,'C15.3', 'C15.4' , 'C30' , 'C30.1','C30.2' , 'C30.3'  ,'C30.4'  , 'C30.5', 'N.C']
        };
    },
    async created() {
      await this.fetchCat();
      await this.checkrole();

    },

    methods: {

        afficher(membre) {
            this.visible = true;
            this.membre = membre
            this.membre.date_De_Naissance = this.formattedDate(this.membre.date_De_Naissance);
            this.affichercat();
            this.getUser();
        },

        confirm() {
            this.$refs.Confirmer.afficher();
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
            const request = {
                'user_Id' : this.membre.user_Id,
                'role' : this.role
            }
            await this.$axios.post("/updateMembre", this.membre)
            await this.$axios.post("/updaterole", request)
            this.mem.push(this.membre);
            await this.attribuercat();
            this.returnTo();
        },
        returnTo() {
            this.visible = false
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

        async getUser() {
            await this.$axios.post("/getuser", this.membre)
                .then((response) => {
                    this.role = response.data.rôle
                    })
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

        async attribuercat()
        {

            await this.$axios.post("/supprimerfpc", this.membre)
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

    }
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

.categories-table {
    width: 60%;
    margin: 7px auto;
    border-collapse: collapse;
}

.categories-table thead {
    background-color: #337ab7;
    color: white;
}

.categories-table th,
.categories-table td {
    padding: 7px;
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

.form-group {
    margin-bottom: 5px;
    display: flex;
}

.form-group label {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center;
    font-size: 14px;
    margin-bottom: 3px;
}

.form-group input,
.form-group select {
    flex:1;
    width: 50%;
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.button-group {
    margin-top: 7px;
    text-align: right;
    display: flex;
    justify-content: space-between;
}

.button-group button {
    padding: 5px 7px;
    font-size: 14px;
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
