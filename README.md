<p align="center">
  <img src="https://img.shields.io/badge/github%20copilot-000000?style=for-the-badge&logo=githubcopilot&logoColor=white" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" />
    <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
    </p>
# 🎪 Event Management API

API untuk sistem manajemen event dengan fitur authentication, CRUD events, dan role-based access control.

## 📋 Deskripsi

Sistem ini memungkinkan organizer untuk membuat dan mengelola event mereka, sementara admin dapat mengakses semua event. Public dapat melihat event yang sudah dipublish.

### Fitur Utama
- 🔐 JWT Authentication (Login/Logout)
- 👥 Role-based Access Control (Admin & Organizer)
- 🎪 CRUD Events dengan validation
- 🔍 Search, Filter, dan Pagination events
- 🛡️ Rate limiting pada login (5 attempts/minute)
- 📊 Health check endpoint

## 🛠️ Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL / PostgreSQL
- **Authentication**: JWT (tymon/jwt-auth)
- **API**: RESTful API
- **Rate Limiting**: Laravel built-in

## 🚀 Instalasi & Setup

### 1. Clone Repository
```bash
git clone https://github.com/Hanz26456/Brief-test-MBKM-Tefa.git
cd event-management-api
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

### 4. Database Configuration
Edit file `.env` dan sesuaikan dengan database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Database Migration & Seeding
```bash
# Buat database tables dan isi dengan sample data
php artisan migrate:fresh --seed
```

### 6. Jalankan Server
```bash
php artisan serve
```

Server akan berjalan di: (http://127.0.0.1:8000/)

## 📚 API Documentation

### Base URL
```
http://127.0.0.1:8000/api
```

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response Success:**
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 1,
            "name": "Admin Sistem",
            "email": "admin@example.com",
            "role": "admin"
        },
        "token_type": "Bearer"
    }
}
```

#### Get Profile
```http
GET /api/auth/me
Authorization: Bearer YOUR_JWT_TOKEN
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer YOUR_JWT_TOKEN
```

### Event Endpoints

#### Get All Events (Public)
```http
GET /api/events
```

**Query Parameters:**
- `search` - Cari berdasarkan judul event
- `status` - Filter berdasarkan status (draft/published)
- `sort` - Urutkan berdasarkan tanggal (asc/desc)
- `page` - Halaman (pagination)
- `per_page` - Jumlah per halaman (default: 10)

**Contoh:**
```http
GET /api/events?search=tech&status=published&sort=desc&page=1&per_page=5
```

#### Get Event Detail
```http
GET /api/events/{id}
```

#### Create Event (Auth Required)
```http
POST /api/events
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json

{
    "title": "Workshop React JS",
    "description": "Belajar React JS dari dasar hingga mahir",
    "venue": "Lab Komputer Polije",
    "start_datetime": "2025-09-20 09:00:00",
    "end_datetime": "2025-09-20 17:00:00",
    "status": "published"
}
```

#### Update Event (Owner/Admin Only)
```http
PUT /api/events/{id}
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json

{
    "title": "Workshop React JS - Updated",
    "description": "Workshop terbaru tentang React JS",
    "venue": "Lab Baru Polije",
    "start_datetime": "2025-09-25 10:00:00",
    "end_datetime": "2025-09-25 18:00:00",
    "status": "published"
}
```

#### Delete Event (Owner/Admin Only)
```http
DELETE /api/events/{id}
Authorization: Bearer YOUR_JWT_TOKEN
```

### Health Check
```http
GET /api/health
```

## 👥 Test Accounts

Setelah menjalankan `php artisan db:seed`, Anda dapat menggunakan akun berikut untuk testing:

### Admin Account
- **Email**: admin@example.com
- **Password**: password
- **Role**: admin
- **Akses**: Semua event (read, create, update, delete)

### Organizer Account 1
- **Email**: budi@example.com
- **Password**: password  
- **Role**: organizer
- **Akses**: Hanya event milik sendiri

### Organizer Account 2
- **Email**: sari@example.com
- **Password**: password
- **Role**: organizer
- **Akses**: Hanya event milik sendiri

## 🔒 Authorization Rules

### Public (Tanpa Login)
- ✅ Lihat daftar event (hanya yang published)
- ✅ Lihat detail event
- ❌ Tidak bisa create/update/delete
🧪 Testing Event Controller:
1. Test List Events (Public - Tanpa Login):
bashGET http://127.0.0.1:8000/api/events

### Organizer (Setelah Login)
- ✅ Lihat semua event published
- ✅ Create event baru
- ✅ Update/delete event milik sendiri
- ❌ Tidak bisa update/delete event orang lain

### Admin (Setelah Login)
- ✅ Akses penuh ke semua event
- ✅ Create, read, update, delete semua event
- ✅ Lihat event draft dari organizer manapun

## 🛡️ Security Features

- **JWT Authentication** dengan token expiration
- **Rate Limiting** pada login endpoint (5 attempts per minute)
- **RBAC (Role-Based Access Control)** untuk proteksi endpoint
- **Input Validation** pada semua request
- **SQL Injection Prevention** menggunakan Eloquent ORM
- **CORS** configuration untuk API access

## 🧪 Testing

### Manual Testing dengan Postman

1. **Login sebagai admin**
2. **Copy JWT token** dari response
3. **Test CRUD operations** dengan token tersebut
4. **Test RBAC** - coba akses event orang lain dengan organizer account

### Testing Scenario

**Scenario 1: Admin Access**
- Login sebagai admin → Bisa akses semua event
- Create event → Success
- Update event orang lain → Success

**Scenario 2: Organizer Access**  
- Login sebagai organizer → Hanya lihat event sendiri di management
- Create event → Success
- Update event sendiri → Success  
- Update event orang lain → Error 403 Forbidden

**Scenario 3: Public Access**
- Akses `/api/events` tanpa token → Success (hanya published events)
- Akses `/api/events` dengan search → Success
- Create event tanpa token → Error 401 Unauthorized

## 📝 Sample Data

Setelah seeding, database akan berisi:

### Events
- **Tech Conference 2025** (by Budi, published)
- **Workshop Laravel** (by Sari, draft)  
- **Seminar Digital Marketing** (by Budi, published)

### Testing Flow
1. Login sebagai `budi@example.com`
2. GET `/api/events` → Akan lihat 2 event published
3. POST `/api/events` → Bisa create event baru
4. PUT `/api/events/2` → Error 403 (event milik Sari)
5. PUT `/api/events/1` → Success (event milik Budi)

## 🐛 Troubleshooting

### Error: "JWT secret not set"
```bash
php artisan jwt:secret
```

### Error: Database connection failed
- Cek konfigurasi `.env`
- Pastikan database service berjalan
- Pastikan database sudah dibuat

### Error: Class not found
```bash
composer dump-autoload
```

### Error: CORS issues
- Install `laravel-cors` package jika diperlukan
- Configure CORS di `config/cors.php`

## 🤝 Contributing

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📞 Contact

**Nama**    : Muhammad Farhan Maulana  
**Nim**     : E41232012  
**Email**   : e41232012@student.polije.ac.id 
**GitHub**  : [@Hanz26456](https://github.com/Hanz26456)
                        **POLITEKNIK NEGERI JEMBER KAMPUS 2 BONDOWOSO**

