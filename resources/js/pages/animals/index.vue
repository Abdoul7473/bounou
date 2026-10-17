<template>
<admin-layout>
    <v-banner class="mb-4">
        <div class="d-flex flex-wrap justify-space-between">
            <h5 class="text-h5 font-weight-bold">Animals</h5>
            <v-breadcrumbs :items="breadcrumbs" class="pa-0"></v-breadcrumbs>
        </div>
    </v-banner>
    <div class="d-flex flex-wrap align-center">

        <v-btn color="primary" @click="create">
            <v-icon dark left> mdi-plus </v-icon> Ajouter
        </v-btn>
    </div>
    <div class="pt-8">
        <v-row>
            <v-col v-for="(item, i) in animals" :key="i" cols="4" class="mx-auto pt-8">
                <v-card max-width="600">
                    <v-row>
                        <v-col cols="3">
                            <v-avatar size="105">
                                <v-img :src="'/animals/' + item.photo" alt="John"></v-img>
                            </v-avatar>
                        </v-col>
                        <v-col cols="9">
                            <v-row>
                                <v-card-text>
                                    <b class="pt-2">{{ item.identifiant }} <v-chip>Gestation</v-chip></b>
                                    <br>
                                    {{item.race}}
                                    <v-chip-group active-class="deep-purple accent-4 white--text" column>
                                        <v-chip>{{ item.sexe }}</v-chip>

                                        <v-chip>{{ item.age }} mois</v-chip>

                                        <v-chip>{{ item.type }}</v-chip>

                                        <v-chip>{{item.poids}} Kg</v-chip>
                                    </v-chip-group>
                                </v-card-text>
                            </v-row>
                        </v-col>
                    </v-row>

                </v-card>

            </v-col>
        </v-row>
    </div>
    <v-dialog v-model="dialog" persistent max-width="700px" scrollable>
        <v-card>
            <v-card-title>
                <span class="text-h5">
                    <v-icon large>mdi-sheep</v-icon>Nouveau animal
                </span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="6">
                            <picture-input ref="pictureInput" v-model="form.photo" width="600" height="600" margin="16" accept="image/jpeg,image/png" size="10" button-class="btn" :custom-strings="{
        upload: '<h1>Bummer!</h1>',
        drag: 'Image 😺'
      }" @change="onChange">
                            </picture-input>
                        </v-col>
                        <v-col cols="6">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field label="Identifiant*" v-model="form.identifiant" placeholder="Ex:OV-001" required></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-select :items="categories" v-model="form.categorie" label="Catégorie*" item-text="libelle" item-value="libelle" required></v-select>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field label="Race*" v-model="form.race" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-select :items="sexes" v-model="form.sexe" label="Sexe*" item-text="libelle" item-value="libelle" required></v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Date de naissance*" type="date" v-model="form.date_naiss" @input="GetAge(form.date_naiss)" persistent-hint required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Age (mois)*" hint="Calculé à partir de la date de naissance" disabled v-model="form.age" required></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field label="Poids(Kg)" type="number" v-model="form.poids" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-radio-group v-model="form.type" row>
                                <v-radio label="Réproduction" value="Réproduction"></v-radio>
                                <v-radio label="Embauche" value="Embauche"></v-radio>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="12" sm="12">
                            <v-textarea counter label="Notes(Optionnel)"></v-textarea>
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
                <v-btn color="success" :disabled="!form.photo" @click="submit()">
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
    props: ["animals"],
    data() {
        return {
            search: null,
            row: null,
            headers: [{
                    text: "No",
                    value: "index",
                    sortable: false
                },
                {
                    text: "Name",
                    value: "name"
                },
                {
                    text: "Email",
                    value: "email"
                },
                {
                    text: "Job Title",
                    value: "job_title"
                },
                {
                    text: "Created At",
                    value: "created_at"
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
                identifiant: null,
                race: null,
                categorie: null,
                sexe: null,
                date_naiss: null,
                age: null,
                poids: null,
                type: null,
                observation: null,
                photo: null
            }),
            sexes: [{
                    'libelle': 'Male'
                },
                {
                    'libelle': 'Femelle'
                }
            ],
            categories: [{
                    libelle: "Ovins"
                },
                {
                    libelle: "Caprins"
                },
                {
                    libelle: "Bovins"
                },
                {
                    libelle: "Volaille"
                }
            ],
            items: [{
                    color: '#1F7087',
                    src: 'https://cdn.vuetifyjs.com/images/cards/foster.jpg',
                    title: 'Supermodel',
                    artist: 'Foster the People',
                },
                {
                    color: '#952175',
                    src: 'https://cdn.vuetifyjs.com/images/cards/halcyon.png',
                    title: 'Halcyon Days',
                    artist: 'Ellie Goulding',
                },
            ],
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
            this.$alert.confirm('Etes-vous sûr ?', "De vouloir enregistrer cet animal?", () => {

                this.form.post(route("animal.create", {
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
        GetAge(item) {
            const debut = new Date(item);
            const fin = new Date();

            let mois = (fin.getFullYear() - debut.getFullYear()) * 12 +
                (fin.getMonth() - debut.getMonth());

            if (fin.getDate() < debut.getDate()) {
                mois--;
            }
            if (mois >= 0) {
                this.form.age = mois
            } else {
                this.form.age = 0
            }

        }
    },
};
</script>
