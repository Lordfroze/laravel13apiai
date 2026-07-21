<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Support\Facades\Http;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(10);

        return ArticleResource::collection($articles);
    }

    public function store(StoreArticleRequest $request)
    {
        $article = Article::create($request->validated());

        return new ArticleResource($article);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->update($request->validated());

        return new ArticleResource($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted'], 200);
    }

    public function summarize(Article $article)
    {
        $response = Http::withToken(config('openai.api_key'))
            ->post(config('openai.base_uri') . '/chat/completions', [
                'model' => 'opencode',
                'messages' => [
                    ['role' => 'user', 'content' => "Simpulkan artikel ini dalam 2 kalimat:\n\nTitle: {$article->title}\n\nContent: {$article->content}"],
                ],
            ]);

        $body = $response->body();

        $parts = explode('data: [DONE]', $body);
        $json = rtrim($parts[0]);

        $data = json_decode($json, true);
        $summary = $data['choices'][0]['message']['content'] ?? '';

        return response()->json([
            'data' => [
                'id' => $article->id,
                'summary' => $summary,
            ],
        ]);
    }
}
