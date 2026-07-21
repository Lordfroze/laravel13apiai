# Planning - Laravel 13 CRUD API + AI SDK

## Spesifikasi

- **Framework:** Laravel 13
- **Database:** MySQL (`laravel13api`, user: `root`, password: ``)
- **AI SDK:** Opecode API / 9router

---

## Step-by-Step

### 1. Install Laravel 13

```bash
composer create-project laravel/laravel:^13.0 .
```

### 2. Konfigurasi Database (`.env`)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel13api
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Buat Database MySQL

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravel13api;"
```

### 4. Generate Model + Migration + Controller + Resource

Buat CRUD untuk entity **Article** (contoh):

```bash
php artisan make:model Article -mc --api
```

Flag `-m` → migration, `-c` → controller, `--api` → API controller (tanpa create/edit views).

Hasil:
| File | Path |
|------|------|
| Model | `app/Models/Article.php` |
| Migration | `database/migrations/xxxx_xx_xx_create_articles_table.php` |
| Controller | `app/Http/Controllers/ArticleController.php` |

### 5. Definisikan Schema Migration (`up()`)

```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content')->nullable();
    $table->string('status')->default('draft'); // draft | published
    $table->timestamps();
});
```

Jalankan: `php artisan migrate`

### 6. Tambahkan `$fillable` di Model

```php
protected $fillable = ['title', 'content', 'status'];
```

### 7. Buat Form Request untuk Validasi

```bash
php artisan make:request StoreArticleRequest
php artisan make:request UpdateArticleRequest
```

Tambahkan rules `required|string|max:255` untuk title, dsb.

### 8. Buat API Resource

```bash
php artisan make:resource ArticleResource
```

Gunakan `ArticleResource` di controller untuk transform respons JSON.

### 9. Implementasi Controller (`ArticleController`)

| Method                                                    | Route                               | Fungsi             |
| --------------------------------------------------------- | ----------------------------------- | ------------------ |
| `index()`                                                 | `GET /api/articles`                 | List semua artikel |
| `store(StoreArticleRequest $request)`                     | `POST /api/articles`                | Buat artikel baru  |
| `show(Article $article)`                                  | `GET /api/articles/{article}`       | Detail artikel     |
| `update(UpdateArticleRequest $request, Article $article)` | `PUT/PATCH /api/articles/{article}` | Update artikel     |
| `destroy(Article $article)`                               | `DELETE /api/articles/{article}`    | Hapus artikel      |

### 10. Register API Routes (`routes/api.php`)

```php
Route::apiResource('articles', ArticleController::class);
```

Akses via Postman/Browser pada prefix `/api/articles`.

### 11. Install & Setup AI SDK

```bash
composer require openai-php/laravel
```

Publish config: `php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"`

**.env:**

```
OPENAI_API_KEY=sk-xxx
```

atau pakai Laravel AI first-party package jika tersedia di Laravel 13.

### 12. Buat AI-Powered Endpoint (Opsional)

Contoh: generate ringkasan artikel otomatis.

**Controller method baru:**

```php
use OpenAI\Laravel\Facades\OpenAI;

public function summarize(Article $article) {
    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => "Ringkas artikel ini: {$article->content}"],
        ],
    ]);

    return response()->json([
        'summary' => $response->choices[0]->message->content,
    ]);
}
```

**Route:**

```php
Route::post('articles/{article}/summarize', [ArticleController::class, 'summarize']);
```

### 13. Testing

```bash
php artisan route:list                      # cek route
php artisan make:test ArticleApiTest --api  # buat test
php artisan test                            # jalankan test
```

### 14. Struktur Final

```
laravel13api/
├── app/
│   ├── Http/
│   │   ├── Controllers/ArticleController.php
│   │   ├── Requests/StoreArticleRequest.php
│   │   ├── Requests/UpdateArticleRequest.php
│   │   └── Resources/ArticleResource.php
│   └── Models/Article.php
├── database/migrations/xxxx_create_articles_table.php
└── routes/api.php
```

---

## API Endpoint Summary

| Method    | Endpoint                       | Deskripsi      | Rate Limit     |
| --------- | ------------------------------ | -------------- | -------------- |
| GET       | `/api/articles`                | List artikel   | 100/min        |
| POST      | `/api/articles`                | Buat artikel   | 20/min         |
| GET       | `/api/articles/{id}`           | Detail artikel | 100/min        |
| PUT/PATCH | `/api/articles/{id}`           | Update artikel | 20/min         |
| DELETE    | `/api/articles/{id}`           | Hapus artikel  | 20/min         |
| POST      | `/api/articles/{id}/summarize` | Ringkas dgn AI | 5/min          |

---

### 15. Security — Rate Limiting

Melindungi API dari Brute Force dan DDoS menggunakan middleware `throttle` bawaan Laravel.

**Named Rate Limiters** — `app/Providers/AppServiceProvider.php`

| Limiter  | Batas     | Target Endpoint                     |
| -------- | --------- | ----------------------------------- |
| `api`    | 100/min   | Semua route API                     |
| `write`  | 20/min    | `store`, `update`, `destroy`        |
| `strict` | 5/min     | `summarize` (AI endpoint mahal)     |

**Penerapan** — `routes/api.php`

```php
Route::middleware('throttle:api')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store'])->middleware('throttle:write');
    // ...
    Route::post('/articles/{article}/summarize', [ArticleController::class, 'summarize'])->middleware('throttle:strict');
});
```

Rate limit dihitung per IP (jika tidak login) atau per user ID (jika login via Sanctum).

---

## Tools / Dependencies

- PHP 8.4+
- Composer
- MySQL 8+
- Postman / curl (testing API)
- OpenAI API key (untuk AI fitur)
