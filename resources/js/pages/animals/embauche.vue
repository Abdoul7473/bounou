<template>
<admin-layout>
    <v-banner class="mb-4">
        <div class="d-flex flex-wrap justify-space-between">
            <h5 class="text-h5 font-weight-bold">Embauches</h5>
            <v-breadcrumbs :items="breadcrumbs" class="pa-0"></v-breadcrumbs>
        </div>
    </v-banner>
    <div class="d-flex flex-wrap align-center">

        <v-btn color="primary" @click="create">
            <v-icon dark left> mdi-plus </v-icon> Ajouter
        </v-btn>
    </div>
    <br>
    <v-card >
        <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Récherche" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="embauches" :search="search" >
            <template v-slot:item.date_entre="{item}">
                {{formatDateTime(item.date_entre)}}
            </template>
            <template v-slot:item.date_prevue="{item}">
                {{formatDateTime(item.date_prevue)}}
            </template>
            <template v-slot:item.animal="{item}">
                {{item.animal.identifiant}} => {{ item.animal.race }}
            </template>
            <template v-slot:item.poids_sortie="{item}">
                <div v-if="item.poids_sortie" align="center">
                    {{ item.poids_sortie }}
                </div>
                <div v-else align="center">
                    -
                </div>
            </template>
            <template v-slot:item.prix_vente="{item}" >
                <div v-if="item.prix_vente" align="center" >
                    {{ item.prix_vente }}
                </div>
                <div v-else align="center">
                    -
                </div>
            </template>
            <template v-slot:item.statut="{item}">
                <div v-if="item.statut == 0">
                    <v-chip color="red" align="center">
                        Non ventu
                    </v-chip>
                </div>
                <div v-if="item.statut == 1">
                    <v-chip color="primary" align="center">
                        Vendu
                    </v-chip>
                </div>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon class="mr-2" mall color="green" @click="Vendre(item)" v-if="item.statut == 0">
                    mdi-format-list-bulleted-square
                </v-icon>
            </template>
        </v-data-table>
    </v-card>
    <v-dialog v-model="dialog" persistent max-width="700px" scrollable>
        <v-card>
            <v-card-title>
                <span class="text-h5" v-if="form.id">
                    <b> vente - {{ info }} </b>
                </span>
                <span class="text-h5" v-else>
                    <v-icon large>mdi-sheep</v-icon>Nouvelle embauche
                </span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <div v-if="form.id">

                        <v-alert text color="info">
                            <h3 class="text-h5">
                                Prix d'achat initial
                            </h3>
                            <div class="text-h4"> <b>{{ form.prix_init }} FCFA</b> </div>
                        </v-alert>
                        <v-row>
                            <v-col cols="12" md="12">
                                <v-text-field label="Poids de sortie(kg)*" type="number" v-model="form.poids_sortie" persistent-hint required></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6" md="12">
                                <v-text-field label="Prix de vente(FCFA)*" v-model="form.prix_vente" type="number" persistent-hint required></v-text-field>
                            </v-col>

                        </v-row>
                    </div>
                    <v-row v-else>
                        <v-col cols="12" sm="6" md="12">
                            <v-select :items="animals" v-model="form.animal_id" label="Animal*" :item-text="item => `${item.identifiant} - ${item.race} `" item-value="id" required></v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field label="Prix d'achat(FCFA)*" v-model="form.prix_init" type="number" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Poids d'entrée(kg)*" type="number" v-model="form.poids_entre" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field label="Date d'entrée*" v-model="form.date_entre" type="date" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field label="Date prévue de finition*" v-model="form.date_prevue" type="date" persistent-hint required></v-text-field>
                        </v-col>
                    </v-row>
                </v-container>
                <small>*indiquent des champs obligatoires</small>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" @click="dialog = false">
                    Annuler
                </v-btn>
                <v-btn color="success" :disabled="(!form.date_entre || !form.date_prevue || !form.prix_init || !form.poids_entre || !form.animal_id) && (!form.prix_vente || !form.poids_sortie)" @click="submit()">
                    Enregistrer
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</admin-layout>
</template>

<script>
import AdminLayout from "../../layouts/AdminLayout.vue";
import PictureInput from 'vue-picture-input'
export default {
    components: {
        AdminLayout,
        PictureInput
    },
    props: ["animals", "embauches"],
    data() {
        return {
            search: null,
            row: null,
            headers: [{
                    text: "Animal",
                    value: "animal",
                    sortable: false
                },
                {
                    text: "Prix d'achat (FCFA)",
                    value: "prix_init"
                },
                {
                    text: "Poids d'entré (Kg)",
                    value: "poids_entre"
                },
                {
                    text: "Date d'entrée",
                    value: "date_entre"
                },
                {
                    text: "Date prévue de finition",
                    value: "date_prevue"
                },
                {
                    text: "Prix de vente (FCFA)",
                    value: "prix_vente"
                },
                {
                    text: "Poids de sortie (Kg)",
                    value: "poids_sortie"
                },
                {
                    text: "Statut",
                    value: "statut"
                },
                {
                    text: "Actions",
                    value: "action",
                    sortable: false
                },
            ],
            breadcrumbs: [{
                    text: "App",
                    disabled: false,
                    href: "/home",
                },
                {
                    text: "Employee",
                    disabled: true,
                    href: "/employee",
                },
            ],
            dialog: false,
            form: this.$inertia.form({
                id: null,
                animal_id: null,
                date_entre: null,
                date_prevue: null,
                prix_init: null,
                poids_entre: null,
                poids_sortie: null,
                prix_vente: null
            }),
            diagnostics: [{
                    'libelle': 'Confirmée'
                },
                {
                    'libelle': 'En attente'
                },
                {
                    'libelle': 'Non gestante'
                }
            ],
            date_prevue: null,
            info: null
        };
    },
    methods: {
        close() {
            this.dialog = false;
            this.form.reset();
        },
        create() {
            this.dialog = true;
            this.form.reset();
            this.form.clearErrors();
        },
        submit() {
            this.$alert.confirm('Etes-vous sûr ?', "De vouloir enregistrer cet embauche?", () => {

                this.form.post(route("embauche.store", {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }), {
                    onSuccess: () => {
                        if (this.$page.props.flash.success) {
                            this.$alert.success(this.$page.props.flash.success)
                        }
                        if (this.$page.props.flash.error) {
                            this.$toast.error(this.$page.props.flash.error)
                        }
                        this.close()
                        this.form.reset();
                    },
                    onError: this.$alert.messages

                });
            })
        },
        Vendre(item) {
            this.dialog = true
            this.form.id = item.id
            this.form.prix_init = item.prix_init
            this.info = item.animal.identifiant

        },
        onChange(image) {
            if (image) {
                this.form.photo = image
            } else {}
        },
        GetDate(item) {
            const date = new Date(item);

            date.setDate(date.getDate() + 150);

            this.form.date_prevue = this.formatDate(date)

            const test = new Date(date);
            let options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            this.date_prevue = test.toLocaleDateString('fr-FR', options);
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${year}-${month}-${day}`;
        },
        formatDateTime(date) {
            const test = new Date(date);
            let options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return test.toLocaleDateString('fr-FR', options);
        }
    },
    created() {
        this.headers.forEach((item, i, items) => {
            if (i === 0) {
                item.class = 'primary white--text rounded-l-xl'
            } else if (i === items.length - 1) {
                item.class = 'primary white--text rounded-r-xl'
            } else {
                item.class = 'primary white--text'
            }
            item.divider = true
        })
    },
};
</script>

<style>
.custom-primary {
    background-color: rgba(225, 230, 210, 1) !important;
    color: black !important;
    /* pour s'assurer que le texte reste visible */
}
</style>
