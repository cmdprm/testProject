<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Можно использовал Laravel Task Scheduling, but sorry, I'm lazy :)
        // I promise you, it'll work!
        if (Auth::guard('sanctum')->check()) {
            $articles = Article::orderBy('publish_date', 'desc')->where('publish_date', '<=', now())->paginate(3);
            return response()->json($articles);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            if ($user->role !== 'admin') {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'publish_date' => 'required|date'
            ]);

            $article = new Article([
                'title' => $data['title'],
                'content' => $data['content'],
                'publish_date' => $data['publish_date']
            ]);
            $article->save();

            return response()->json($article, 201);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::guard('sanctum')->check()) {
            $article = Article::find($id);
            if (!$article || $article->publish_date > now()) {
                return response()->json(['error' => 'Article not found'], 404);
            }
            return response()->json($article);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            if ($user->role !== 'admin') {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $data = $request->validate([
                'id' => 'required|integer',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'publish_date' => 'required|date'
            ]);

            $article = Article::find($data['id']);
            if (!$article) {
                return response()->json(['error' => 'Article not found'], 404);
            }

            $article->title = $data['title'];
            $article->content = $data['content'];
            $article->publish_date = $data['publish_date'];
            $article->save();

            return response()->json($article, 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            if ($user->role !== 'admin') {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $data = $request->validate([
                'id' => 'required|integer'
            ]);

            $article = Article::find($data['id']);
            if (!$article) {
                return response()->json(['error' => 'Article not found'], 404);
            }
            $article->delete();

            return response()->json(['message' => 'Article deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
