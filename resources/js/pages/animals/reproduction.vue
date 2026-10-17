<template>
<admin-layout>
    <v-banner class="mb-4">
        <div class="d-flex flex-wrap justify-space-between">
            <h5 class="text-h5 font-weight-bold">Réproductions</h5>
            <v-breadcrumbs :items="breadcrumbs" class="pa-0"></v-breadcrumbs>
        </div>
    </v-banner>
    <div class="d-flex flex-wrap align-center">

        <v-btn color="primary" @click="create">
            <v-icon dark left> mdi-plus </v-icon> Ajouter
        </v-btn>
    </div>
    <br>
    <v-card>
        <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Récherche" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="reproductions" :search="search">
            <template v-slot:item.date="{item}">
               {{formatDateTime(item.date)}}
            </template>
            <template v-slot:item.date_prevue="{item}">
               {{formatDateTime(item.date_prevue)}}
            </template>
            <template v-slot:item.belier="{item}">
               {{item.belier.identifiant}} => {{ item.belier.race }}
            </template>
            <template v-slot:item.productrice="{item}">
                {{item.productrice.identifiant}} => {{ item.productrice.race }}
            </template>
        </v-data-table>
    </v-card>
    <v-dialog v-model="dialog" persistent max-width="700px" scrollable>
        <v-card>
            <v-card-title>
                <span class="text-h5">
                    <v-icon large>mdi-sheep</v-icon>Nouvelle saillie
                </span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>

                        <v-col cols="12" sm="6" md="12">
                            <v-select :items="productrices" v-model="form.productrice" label="Femelle(animal)*" :item-text="item => `${item.identifiant} - ${item.race} `" item-value="id" required></v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="12">
                            <v-select :items="beliers" v-model="form.belier" label="Belier(animal)*" :item-text="item => `${item.identifiant} - ${item.race} `" item-value="id" required></v-select>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field label="date de saillie*" type="date" v-model="form.date" @input="GetDate(form.date)" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Nombre d'agneaux*" v-model="form.nbr_anneau" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-select :items="diagnostics" v-model="form.diagnostic" label="Diagnostics*" item-text="libelle" item-value="libelle" required></v-select>
                        </v-col>
                    </v-row>
                    <v-alert text color="info">
                        <h3 class="text-h6">
                            Date prévue de mise-bàs (J+150)
                        </h3>
                        <div v-if="date_prevue"> {{ date_prevue }}</div>
                    </v-alert>
                </v-container>
                <small>*indiquent des champs obligatoires</small>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" @click="dialog = false">
                    Annuler
                </v-btn>
                <v-btn color="success" :disabled="!form.date || !form.nbr_anneau || !form.diagnostic || !form.productrice || !form.belier" @click="submit()">
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
    props: ["productrices", "reproductions", "beliers"],
    data() {
        return {
            search: null,
            row: null,
            headers: [{
                    text: "Femelle",
                    value: "productrice",
                    sortable: false
                },
                {
                    text: "Belier(male)",
                    value: "belier"
                },
                {
                    text: "Diagnostic",
                    value: "diagnostic"
                },
                {
                    text: "Date",
                    value: "date"
                },
                {
                    text: "Mise bas",
                    value: "date_prevue"
                },
                {
                    text: "Agneaux",
                    value: "nbr_anneau"
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
            dialogDelete: false,
            isUpdate: false,
            isLoading: false,
            isLoadingTable: false,
            itemId: null,
            options: {},
            search: null,
            params: {},
            form: this.$inertia.form({
                productrice: null,
                belier: null,
                date: null,
                nbr_anneau: null,
                diagnostic: null,
                date_prevue: null,
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
            date_prevue: null
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
            this.$alert.confirm('Etes-vous sûr ?', "De vouloir enregistrer cette reproduction?", () => {

                this.form.post(route("reproduction.store", {
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
