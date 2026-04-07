# 🌿 EcoAventura Backend API

Laravel REST API for the EcoAventura platform - Eco-tourism and adventure.

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js (optional, for asset compilation)

## 🚀 Installation

### 1. Clone and install dependencies

```bash
# Clone repository
git clone <repository-url>
cd ecoAventura-backend

# Install PHP dependencies
composer install
```

### 2. Configure the environment

```bash
# Copy configuration file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure the database

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecoaventura
DB_USERNAME=root
DB_PASSWORD=

# Frontend URL (for CORS)
FRONTEND_URL=http://localhost:3000
```

### 4. Run migrations and seeders

```bash
# Run migrations
php artisan migrate

# Run seeders (test users + categories)
php artisan db:seed
```

### 5. Create a symbolic link for storage

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` to serve images.

### 6. Start the development server

```bash
php artisan serve
```

The server will be available at `http://localhost:8000`

---

## 👤 Test Users

| Email | Password | Role |
|-------|----------|-----|
| admin@ecoaventura.com | password | admin |
| partner@ecoaventura.com | password | partner |
| user@ecoaventura.com | password | user |

---

## 📡 API Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/register` | Register user | No |
| POST | `/api/login` | Log in | No |
| GET | `/api/me` | Authenticated user | Yes |
| POST | `/api/logout` | Log out | Yes |

### Categories (Public)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/categories` | List categories | No |
| GET | `/api/categories/{id}` | View category | No |

### Places (Public)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/places` | List approved places | No |
| GET | `/api/places/{slug}` | View place by slug | No |

#### Query parameters for `/api/places`:

- `category_id`: Filter by category
- `featured`: `true` for featured places
- `search`: Search by name, description, or address
- `per_page`: Number per page (default: 12)

### Places - Partner/Admin

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/partner/places` | My Places | Partner |
| POST | `/api/partner/places` | Create Place | Partner |
| PUT | `/api/partner/places/{id}` | Update Place | Partner |
| DELETE | `/api/partner/places/{id}` | Delete Place | Partner |

### Places - Admin

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/admin/places` | All Places | Admin |
| PATCH | `/api/admin/places/{id}/status` | Change Status | Admin |
| PUT | `/api/admin/places/{id}` | Update place | Admin |
| DELETE | `/api/admin/places/{id}` | Delete place | Admin |

---

## 📤 Image Upload

### Create place with images

```bash
POST /api/partner/places
Content-Type: multipart/form-data
Authorization: Bearer {token}

# Fields:
- category_id: integer (required)
- name: string (required)
- short_description: string (required)
- description: string (optional)
- address: string (optional)
- latitude: float (optional)
- longitude: float (optional)
- images[]: file (required, min:1, max:10)
- primary_image_index: integer (optional, default:0)
```

### Update place and images

```bash
PUT /api/partner/places/{id}
Content-Type: multipart/form-data
Authorization: Bearer {token}

# Fields:
- name: string (optional)
- short_description: string (optional)
- ...other fields
- new_images[]: file (optional, max:10)
- delete_images[]: integer[] (IDs of images to delete)
- primary_image_id: integer (ID of new primary image)
```

---
## 📊 Location Response

```json
{
  “data”: {
    “id”: 1,
    “name”: “El Salto Waterfall”,
    “slug”: “el-salto-waterfall”,
    “short_description”: “Beautiful 30-meter waterfall”,
    “description”: “...”,
    “address”: “Montaña Verde, km 45”,
    “latitude”: “10.1234567”,
    “longitude”: “-64.1234567”,
    “is_featured”: false,
    “status”: “approved”,
    “category”: {
      “id”: 4,
      “name”: “Waterfalls”
    },
    “user”: {
      “id”: 2,
      “name”: “Demo Member”
    },
    “images”: [
      {
        “id”: 1,
        “url”: “http://localhost:8000/storage/places/1/abc123.jpg”,
        “filename”: “waterfall.jpg”,
        “is_primary”: true,
        “order”: 0
      },
      {
        “id”: 2,
        “url”: “http://localhost:8000/storage/places/1/def456.jpg”,
        “filename”: “vista.jpg”,
        “is_primary”: false,
        “order”: 1
      }
    ],
    “primary_image_url”: “http://localhost:8000/storage/places/1/abc123.jpg”,
    “created_at”: “2025-12-15T10:00:00+00:00”,
    “updated_at”: “2025-12-15T10:00:00+00:00”
  }
}
```

---

## 🔐 Authentication

This API uses **Laravel Sanctum** for token-based authentication.

### Get token (Login)

```bash
POST /api/login
Content-Type: application/json

{
  “email”: “partner@ecoaventura.com”,
  “password”: “password”
}
```

Response:
```json
{
  “message”: “Login successful”,
  “user”: {...},
  “token”: “1|abc123def456...”
}
```

### Using the token in requests

```bash
GET /api/partner/places
Authorization: Bearer 1|abc123def456...
```

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php      # Authentication
│   │       ├── CategoryController.php  # Categories
│   │       ├── PlaceController.php     # Places + images
│   │       ├── AdminController.php     # Admin dashboard
│   │       ├── PartnerController.php   # Partner dashboard
│   │       └── UserController.php      # User dashboard
│   └── Middleware/
│       └── RoleMiddleware.php          # Role verification
├── Models/
│   ├── User.php
│   ├── Place.php
│   ├── PlaceImage.php
│   ├── Category.php
│   ├── Review.php
│   └── Favorite.php
database/
├── migrations/
│   ├── *_create_places_table.php
│   ├── *_create_place_images_table.php
│   └── ...
└── seeders/
    ├── DatabaseSeeder.php
    └── CategorySeeder.php
routes/
└── api.php                              # API Routes
config/
└── cors.php                             # CORS Configuration
```

---

## 🛠️ Useful Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View API routes
php artisan route:list --path=api

# Create a new place from Tinker
php artisan tinker
>>> $place = App\Models\Place::create([...])

# Regenerate storage link
php artisan storage:link
```

---

## 📝 Important Notes

1. **CORS**: The configuration allows requests from `localhost:3000` and `localhost:5173`. Adjust in `config/cors.php` according to your frontend.

2. **Images**: Stored in `storage/app/public/places/{place_id}/` and served via `/storage/places/{place_id}/{filename}`

3. **Place statuses**:
   - `pending`: Pending approval
   - `approved`: Approved and visible
   - `rejected`: Rejected
   - `needs_fix`: Needs corrections

4. **Roles**:
   - `user`: Regular user (can view places, favorites, reviews)
   - `partner`: Partner (can create/edit their places)
   - `admin`: Administrator (full access)

---

## 📄 License

This project is licensed under the MIT License.