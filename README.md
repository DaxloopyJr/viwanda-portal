# Viwanda Portal — Ministry of Industry and Trade Data Collection & Reporting System

A Laravel implementation of the **Viwanda Portal SRS**: a centralised portal through which
institutions under the Ministry of Industry and Trade report datasets to the Ministry,
via **two channels**:

- **Portal (manual)** — web forms and CSV file upload, for institutions without digital systems.
- **API (system-to-system)** — a REST endpoint with token authentication, for institutions
  with their own digital systems.

Built with **Laravel 12 + Blade + Bootstrap 5** only, with **MySQL or PostgreSQL** as the
database, **spatie/laravel-permission** for role-based access control, **Laravel Sanctum**
for API tokens, and **Chart.js** for dashboard graphs (line, bar, pie/doughnut) and tables.

---

## 1. Requirements

- PHP 8.2+ with `pdo_mysql` **or** `pdo_pgsql`, `mbstring`, `openssl`, `zip`, `curl`
- Composer 2
- MySQL 8+ **or** PostgreSQL 14+

## 2. Installation

```bash
unzip viwanda-portal.zip && cd viwanda-portal
composer install
cp .env.example .env
php artisan key:generate
```

### 2.1 Configure the database

**MySQL (default in `.env.example`):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=viwanda_portal
DB_USERNAME=root
DB_PASSWORD=secret
```

**PostgreSQL:**

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=viwanda_portal
DB_USERNAME=postgres
DB_PASSWORD=secret
```

All migrations are database-agnostic — no changes are needed to switch engines.

### 2.2 Migrate, seed and run

```bash
php artisan migrate --seed
php artisan serve
```

Open http://127.0.0.1:8000

## 3. Demo accounts (all passwords: `password`)

| Email | Role | What they can do |
|---|---|---|
| admin@viwanda.go.tz | System Administrator | Everything: users, roles, institutions, datasets, audit trail |
| manager@viwanda.go.tz | Ministry Data Manager | Review oversight, accept, **publish**, reports |
| reviewer@viwanda.go.tz | Department Data Reviewer | Start review, accept, **return**, reject submissions |
| fct.officer@fct.go.tz | Institution Data Officer (FCT) | Create/edit/submit FCT data via portal or CSV upload |
| api.fcc@fcc.go.tz | Institution API Account (FCC) | API token for system-to-system submission |
| executive@viwanda.go.tz | Executive / Report Consumer | Read-only dashboards and reports |

RBAC is enforced with **spatie/laravel-permission** (6 roles, 19 permissions) on both web
and API routes; institution users only ever see their own institution's data.

## 4. Submission workflow

```
draft → submitted → under_review → accepted → published
                          ↓   ↘
                     returned   rejected
```

- Institution officer creates a submission (manual entry or CSV upload) → **draft**
- Officer submits → **submitted** (dataset schema validation runs per row)
- Reviewer starts review → **under_review**, then **accept**, **return** (with comments)
  or **reject** (with comments)
- Returned submissions can be edited and resubmitted by the institution
- Ministry Data Manager **publishes** accepted submissions
- Every transition is written to the **audit trail**

## 5. API channel (for institutions with digital systems)

Institution API accounts create a token on their **Profile** page (ability: `submit`).

```bash
# Fetch the dataset schema (data dictionary)
curl -H "Authorization: Bearer <TOKEN>" \
     http://localhost:8000/api/v1/datasets/FCT-AC/schema

# Submit records (idempotent via transaction_reference)
curl -X POST -H "Authorization: Bearer <TOKEN>" \
     -H "Content-Type: application/json" \
     http://localhost:8000/api/v1/submissions \
     -d '{
           "dataset_code": "FCT-AC",
           "reporting_period": "2026-Q3",
           "transaction_reference": "FCT-2026-Q3-001",
           "records": [
             {"date": "2026-07-03", "case_no": "Appeal No. 10/2026",
              "case_originated": "EWURA", "case_sector": "Energy: Petroleum sector",
              "case_status": "Order delivered", "remarks": "Decided"}
           ]
         }'

# Check submission status
curl -H "Authorization: Bearer <TOKEN>" \
     http://localhost:8000/api/v1/submissions/VP-2026-00010
```

- Duplicate `transaction_reference` → the API returns the existing submission instead of
  creating a duplicate (idempotent submission).
- Records failing validation → `422` with per-row, per-field errors; nothing is queued
  for review until all rows pass.
- An API account can only submit datasets belonging to its own institution (`403` otherwise).

## 6. Datasets (data catalogue)

Datasets carry a JSON **data dictionary** (`fields`) which drives both the dynamic entry
forms and server-side validation rules (types, required flags, `in:` option lists).

Seeded datasets:

| Code | Name | Institution |
|---|---|---|
| FCT-AC | Appeal/Application Case | Fair Competition Tribunal |
| FCC-CN | Competition Complaints and Notifications | Fair Competition Commission |
| SIDO-IE | Industrial Establishments and Employment | SIDO |
| TBS-PC | Product Certifications | Tanzania Bureau of Standards |

The **FCT-AC** dataset is modelled on the Fair Competition Tribunal "Appeal/Application
Case" data form, and the seed data includes the four real sample rows from that form
(Application No.28/2020 TCRA — withdrawn; Appeal No. 24/2020 FCC — withdrawn;
Appeal No. 16/2020 EWURA — order delivered; Appeal No. 15/2020 EWURA — order delivered).

## 7. Dashboards & reports

- **Dashboard**: KPI cards (institutions, submissions, pending review, acceptance rate),
  12-month submissions trend (line), status distribution (pie), submissions per
  institution (bar), recent submissions table, per-institution compliance table.
- **Reports**: consolidated per-institution report with status-mix doughnut chart,
  submission compliance report per period, and CSV export.

## 8. Project structure

```
app/Http/Controllers/         Web controllers (Dashboard, Submission, Review, ...)
app/Http/Controllers/Api/V1/  API controllers (Sanctum-protected)
app/Models/                   Institution, Dataset, Submission, SubmissionRecord, AuditLog
database/migrations/          DB-agnostic schema (MySQL / PostgreSQL)
database/seeders/             Roles & permissions, institutions, datasets, users, demo data
resources/views/              Blade + Bootstrap 5 views (Chart.js dashboards)
routes/web.php, routes/api.php
```
