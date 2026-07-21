# Usage - Postman API Testing

Base URL: `http://localhost:8000/api`

---

## 1. Create Article

| Method | URL                     |
| ------ | ----------------------- |
| `POST` | `{{base_url}}/articles` |

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**

```json
{
    "title": "Belajar Laravel 13",
    "content": "Laravel 13 adalah framework PHP terbaru dengan banyak fitur menarik...",
    "status": "published"
}
```

**Response (201):**

```json
{
    "data": {
        "id": 1,
        "title": "Belajar Laravel 13",
        "content": "Laravel 13 adalah framework PHP terbaru dengan banyak fitur menarik...",
        "status": "published",
        "created_at": "2026-07-21T00:00:00.000000Z",
        "updated_at": "2026-07-21T00:00:00.000000Z"
    }
}
```

---

## 2. List All Articles

| Method | URL                     |
| ------ | ----------------------- |
| `GET`  | `{{base_url}}/articles` |

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Belajar Laravel 13",
            "content": "Laravel 13 adalah framework PHP terbaru...",
            "status": "published",
            "created_at": "2026-07-21T00:00:00.000000Z",
            "updated_at": "2026-07-21T00:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/articles?page=1",
        "last": "http://localhost:8000/api/articles?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

**Query Params (opsional):**
| Param | Value | Keterangan |
|-------|-------|------------|
| `page` | `2` | Halaman |
| `per_page` | `5` | Item per halaman |

---

## 3. Show Single Article

| Method | URL                       |
| ------ | ------------------------- |
| `GET`  | `{{base_url}}/articles/1` |

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "title": "Belajar Laravel 13",
        "content": "Laravel 13 adalah framework PHP terbaru...",
        "status": "published",
        "created_at": "2026-07-21T00:00:00.000000Z",
        "updated_at": "2026-07-21T00:00:00.000000Z"
    }
}
```

---

## 4. Update Article

| Method | URL                       |
| ------ | ------------------------- |
| `PUT`  | `{{base_url}}/articles/1` |

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**

```json
{
    "title": "Belajar Laravel 13 (Updated)",
    "status": "draft"
}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "title": "Belajar Laravel 13 (Updated)",
        "content": "Laravel 13 adalah framework PHP terbaru...",
        "status": "draft",
        "created_at": "2026-07-21T00:00:00.000000Z",
        "updated_at": "2026-07-21T00:00:00.000000Z"
    }
}
```

> `PATCH` juga bisa digunakan dengan cara yang sama.

---

## 5. Delete Article

| Method   | URL                       |
| -------- | ------------------------- |
| `DELETE` | `{{base_url}}/articles/1` |

**Response (200):**

```json
{
    "message": "Article deleted"
}
```

---

## 6. Summarize with AI

| Method | URL                                 |
| ------ | ----------------------------------- |
| `POST` | `{{base_url}}/articles/1/summarize` |

**Headers:**

```
Accept: application/json
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "summary": "Artikel ini membahas tentang Laravel 13, framework PHP terbaru yang memiliki berbagai fitur menarik untuk pengembangan web modern."
    }
}
```

> **Catatan:** Menggunakan model `opencode` pada app/Http/Controllers/ArticleController.php via endpoint `http://localhost:20128/v1` (9router).

---

## Setup Collection di Postman

### 1. Buat Collection Baru

- Klik **New > Collection**
- Nama: `Laravel 13 API`

### 2. Set Variable Collection

Klik **Variables** pada collection, tambahkan:

| Variable   | Initial Value               | Current Value               |
| ---------- | --------------------------- | --------------------------- |
| `base_url` | `http://localhost:8000/api` | `http://localhost:8000/api` |

### 3. Buat Request

Klik **Add Request** di dalam collection, isi:

| Request        | Method   | URL                                 |
| -------------- | -------- | ----------------------------------- |
| Create Article | `POST`   | `{{base_url}}/articles`             |
| List Articles  | `GET`    | `{{base_url}}/articles`             |
| Show Article   | `GET`    | `{{base_url}}/articles/1`           |
| Update Article | `PUT`    | `{{base_url}}/articles/1`           |
| Delete Article | `DELETE` | `{{base_url}}/articles/1`           |
| Summarize      | `POST`   | `{{base_url}}/articles/1/summarize` |

### 4. Run Server

```bash
php artisan serve
```

Akses di `http://localhost:8000`.
