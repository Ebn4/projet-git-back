<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
//use Str marche egalement mais c'est mieux de faire appel a Str depuis sa source
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    public function index()
    {
        //return ArticleResource::collection(Article::paginate(1));
        return ArticleResource::collection(Article::withCount(['likes'])->get());
        // return Article::all();
    }


    public function store(ArticleRequest $request)
    {

        try {
            $article = Article::create([
                "title" => $request->title,
                "slug" => Str::slug($request->title),
                "photo" => $request->photo,
                "auteur" => $request->auteur,
                "content" => $request->content,
            ]);

            $article->categories()->attach($request->categories);
            $article->tags()->attach($request->tags);

            return response()->json($article->load('categories', 'tags'), 201);
        } catch (\Exception $th) {
            return response()->json($th->getMessage(),500);
        }


    }


    public function show(Article $article)
    {
        // Vérifier s'il existe une vue pour cet article
        $vue = $article->vues()->first();

        if ($vue) {
            $vue->increment('nbr_vue'); // Incrémente nbr_vue si une vue existe déjà
        } else {
            $article->vues()->create(['nbr_vue' => 1]); // Crée une nouvelle vue
        }

        return new ArticleResource($article->load('categories', 'vues')); // Charger aussi 'vues'
    }

    public function update(ArticleRequest $request, Article $article)
    {
        try {
            $article->update([
                "title" => $request->title,
                "slug" => Str::slug($request->title),
                "photo" => $request->photo,
                "auteur" => $request->auteur,
                "content" => $request->content,
            ]);

            $article->categories()->attach($request->category_id);
            $article->tags()->attach($request->tags);

            return response()->json(new ArticleResource($article->load('categories')));
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        try {
            // Détacher toutes les catégories
            $article->categories()->detach();
            $article->tags()->detach();
            // Supprimer l'article
            $article->delete();

            return response()->json(['message' => 'Article supprimé avec succès'], 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getLatestTreeArticle(Article $article){
        try {
            $article = Article::latest()->take(3)->get();
        $getTreeArticles = ArticleResource::collection($article);
        return response()->json([
            'data' => $getTreeArticles,
        ]);
        } catch (\Exception $message) {
            return response()->json(['error'=> $message->getMessage()], 500);
        }

    }


}
