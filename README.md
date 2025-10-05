# 📰 Laravel News Aggregator API

A **Laravel 12–based news aggregator API** that fetches articles from multiple sources at scheduled intervals.  
The application is **containerized with Docker**, runs background jobs and a scheduler, and exposes **RESTful endpoints** for articles, sources, and categories.

This project demonstrates a **production-grade setup** including queue workers, scheduling, and an API-first design.

---

## 📦 Features

- ⏱ Fetches and stores articles from external APIs every **5 minutes** (via Laravel scheduler)  
- ⚙️ Queue worker handles jobs for fetching and storing articles  
- 📑 RESTful API with **pagination** for articles, sources, and categories  
- 🐳 **Fully Dockerized** setup (no need to install PHP/Laravel locally)  
- 🧪 Includes **Postman collection** for easy endpoint testing  
- 💾 Database-driven queue driver for simplicity (**Redis recommended in production**)  

---

## 🐳 Why Docker?

Laravel 12 requires **PHP 8.2+ and specific extensions**.  
To avoid version mismatch issues, the project runs entirely inside Docker containers.

### Benefits:
- ✅ Correct **PHP 8.2 environment** with required extensions  
- ✅ Pre-configured **MySQL/Postgres** and **Redis (optional)**  
- ✅ Queue worker & scheduler managed automatically  
- ✅ No need to install PHP, Composer, or Laravel locally  
- ✅ Guaranteed reproducibility across any machine  

```bash
docker-compose up --build
```
Once started, the API is available at:
👉 http://localhost:8000

🚀 Getting Started
1. Clone the Repository
git clone https://github.com/Logik03/News-Agregator.git
```bash
cd news-agregator
```
3. Copy Environment File
```bash
cp .env.example .env
```
5. Add Your API Keys

This project fetches news from external providers (e.g., NewsAPI.org, The Guardian, New York Times).
You must supply your own keys:
```bash
GUARDIAN_KEY=your_guardian_api_key_here
```
```bash
NEWSAPIORG_KEY=your_newsapi_key_here
```
```bash
NEWYORKTIMES_KEY=your_newyorktimes_api_key_here
```

⚠️ Without valid keys, the application cannot fetch real news.

4. Build and Run Containers
```bash
docker-compose up --build
```

This starts the following services:

app → Laravel API (PHP-FPM)

db → MySQL/Postgres database : MySql was used for this test case

worker → runs 
```bash
php artisan queue:work
```

scheduler → runs 
```bash
php artisan schedule:work
```
5. Run Database Migrations

the docker-compose runs the migrations automatically but then migrations can still be ran manually by running the below command in a terminal
```bash
docker-compose exec app php artisan migrate
```
6. Fetch News

The scheduler runs automatically every 5 minutes.
To fetch immediately run : 
```bash
docker-compose exec app php artisan fetch:newsapi
```
OR 
```bash
docker-compose exec app php artisan fetch:guardian
```
OR 
```bash
docker-compose exec app php artisan fetch:nyt
```

⚙️ Queue Driver

Current setup → Database queue driver (simple for local setup)

Production-ready → Use Redis for performance & scalability i.e change 
```bash
QUEUE_CONNECTION=redis
```
in .env for production

⏰ Scheduler

The scheduler runs via:
```bash
php artisan schedule:work
```

This acts like a cron job, triggering the fetch news job every 5 minutes.

🧩 Database Schema

sources → News sources (e.g., Guardian, NEWYORK TIMES, News Api)

authors → Article authors

categories → Article categories

articles → Stored articles

Future improvement: add a pivot table for multi-category articles.

🧠 API Endpoints
Method	Endpoint	Description
GET	/api/articles	List all articles (paginated)
GET	/api/articles/{id}	Get single article
GET	/api/categories	List all categories
GET	/api/sources	List all sources
🧪 Postman Collection

A ready-to-import Postman collection is included:
- [Download the Postman collection](./Postman/News-Aggregator.postman_collection.json)
- Import it into Postman (`File → Import`)

Import Instructions

Open Postman

Click Import

Select the JSON file above downloaded from the Postman folder in the folder structure for the application

The collection is preconfigured for:
👉 http://localhost:8000

🧰 Manual Setup (Without Docker)
Requirements

PHP 8.2+

Composer

MySQL

Redis (optional)

Steps

```bash
composer install
```
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
```bash
php artisan migrate
```
```bash
php artisan serve
```
```bash
php artisan queue:work
```
```bash
php artisan schedule:work
```


API available at:
👉 http://127.0.0.1:8000

🧱 Future Improvements

🔗 Add pivot table for multi-category articles

👤 Implement personalized feeds

📊 Use Redis + Laravel Horizon for job management/monitoring

✅ Add unit/integration tests for API endpoints

☸️ Deploy with Kubernetes/ECS for scaling

🧾 Notes for Reviewers

🔑 You must provide your own API keys in .env.

📂 No seed data is included; articles are fetched by the scheduler.

🛠 For instant testing:
```bash
docker-compose exec app php artisan fetch:newsapi
```

📑 All endpoints & examples are documented in the Postman collection.

