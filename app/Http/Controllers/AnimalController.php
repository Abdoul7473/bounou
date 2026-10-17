<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Embauche;
use App\Models\Reproduction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnimalController extends Controller
{
    public function animal_index(Request $request){
        $animals = Animal::all();
        return Inertia::render('animals/index',[
            'animals' => $animals
        ]);
    }
    public function animal_create(Request $request)
    {
        $image = $request->photo;
        preg_match("/data:image\/(.*?);base64,/", $image, $extension);

        $extension = $extension[1];
        $image = preg_replace(
            "/^data:image\/\w+;base64,/",
            '',
            $image
        );
        $nom = str_replace(' ', '', $request->identifiant);
        $image = str_replace(' ', '', $image);

        $nom_fichier = $nom . '.' . $extension;

        file_put_contents(
            public_path('animals/' . $nom_fichier),
            base64_decode($image)
        );
        $animal = Animal::create([
            'identifiant' => $request->identifiant,
            'categorie' => $request->categorie,
            'race' => $request->race,
            'sexe' => $request->sexe,
            'date_naiss' => $request->date_naiss,
            'age' => $request->age,
            'poids' => $request->poids,
            'type' => $request->type,
            'observation' =>$request->observation,
            'photo' => $nom_fichier

        ]);
        return redirect()->route('animal.index')->with('success',  'animal crée avec success!');
    }
    public function reproduction_index(Request $request){
        $reproductions = Reproduction::with('belier','productrice')->get();
        $productrices = Animal::where('sexe','Femelle')->get();
        $beliers = Animal::where('sexe','Male')->get();
        return Inertia::render('animals/reproduction',[
            'reproductions' => $reproductions,
            'productrices' => $productrices,
            'beliers' => $beliers
        ]);
    }
    public function reproduction_store(Request $request){
        Reproduction::create([
            'productrice' => $request->productrice,
            'belier' => $request->belier,
            'date' => $request->date,
            'nbr_anneau' => $request->nbr_anneau,
            'diagnostic' => $request->diagnostic,
            'date_prevue'  => $request->date_prevue

        ]);
        return redirect()->route('reproduction.index')->with('success',  'réproduction créée avec success!');

    }
    public function embauche_index(Request $request){
        $animals = Animal::where('type','Embauche')->get();
        return Inertia::render('animals/embauche',[
            'embauches' => Embauche::with('animal')->get(),
            'animals' => $animals
        ]);
    }
    public function embauche_store(Request $request){
        if($request->id){
            $embauche = Embauche::find($request->id);
            $embauche->prix_vente = $request->prix_vente;
            $embauche->poids_sortie = $request->poids_sortie;
            $embauche->statut = 1;
            $embauche->update();
        }
        else{
            Embauche::create([
            'animal_id' => $request->animal_id,
            'prix_init' => $request->prix_init,
            'poids_entre' => $request->poids_entre,
            'date_entre' => $request->date_entre,
            'date_prevue' => $request->date_prevue,
            'statut' => 0
        ]);
        }
        
        return redirect()->route('embauche.index')->with('success',  'Embauche créée avec success!');

    }
}
