⛽ GoFull
Libya's On-Road Emergency Assistance Platform
Fuel Delivery · Towing Service · Anywhere · Anytime


📖 Overview
GoFull is a Libyan digital platform that connects stranded drivers (out of fuel or vehicle breakdown) with nearby service providers — either fuel delivery drivers or tow truck operators.

The platform consists of:

📱 Flutter Mobile App — unified app for both drivers and service providers

🖥️ Web Dashboard — built with Laravel Blade for admins and employees

⚙️ Laravel REST API — central backend powering the mobile app

🗄️ MySQL Database — structured relational data storage

🎯 Problem & Solution
Problem	Solution
Drivers rely on personal contacts when stranded	One-tap service request through the app
Difficulty locating the customer accurately	Automatic GPS location detection
Lack of pricing transparency	Full service details shown before acceptance
No trust-building system between parties	Rating & review system after every service
No formal verification for service providers	Admin-managed document verification with in-person appointment
👥 System Roles
Role	Description	Interface
driver	Requests fuel delivery or towing service	Flutter App
provider	Provides fuel delivery or towing service	Flutter App
admin	Full platform management access	Web Dashboard
employee	Internal staff with limited permissions	Web Dashboard
✨ Key Features
Driver
✅ Register & login with phone number and password

✅ Request fuel delivery with type and quantity

✅ Request towing service with vehicle plate number

✅ Track service provider on the map in real time

✅ Rate the service (1–5 stars) after completion

✅ View full history of past requests

✅ Cancel a request before it is accepted

Service Provider
✅ Register with official document uploads

✅ Receive nearby requests matching service type

✅ Accept or reject incoming requests

✅ Update request status (en_route / arrived / in_progress / completed)

✅ Toggle availability (Online / Offline)

✅ View ratings and full request history

Admin
✅ Dashboard with live statistics

✅ Manage all users (suspend / delete)

✅ Verify service providers with in-person appointment scheduling

✅ Monitor all active requests in real time

✅ View reports and analytics

✅ Manage employee accounts

Employee
✅ Review service provider documents

✅ Schedule in-person verification appointments

✅ Approve or reject provider applications

✅ Monitor active service requests

🗄️ Database Schema
text
users ──── 1:1 ──→ provider_profiles ──── 1:M ──→ provider_documents
      ──── 1:M ──→ service_requests   ──── 1:1 ──→ ratings
      ──── 1:M ──→ notifications
Tables
Table	Description
users	All system users (driver / provider / admin / employee)
provider_profiles	Service provider details and vehicle information
provider_documents	Uploaded verification documents
service_requests	Service requests (fuel delivery or towing)
ratings	Driver ratings for service providers
notifications	System notifications for all users
Request Lifecycle
text
pending → accepted → en_route → arrived → in_progress → completed
                                                              ↓
                                                      (driver rates)

Any stage → cancelled (before completed)
🛠️ Requirements
PHP >= 8.5

Composer >= 2.x

MySQL >= 8.0

Node.js >= 18 (for assets)

Laravel 13

🚀 Installation
1. Clone the repository
bash
git clone https://github.com/your-username/gofull-backend.git
cd gofull-backend
2. Install dependencies
bash
composer install
npm install && npm run build
3. Set up environment file
bash
cp .env.example .env
php artisan key:generate
4. Configure database in .env
text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gofull
DB_USERNAME=root
DB_PASSWORD=your_password
5. Run migrations
bash
php artisan migrate
6. Create the first admin account
bash
php artisan db:seed --class=AdminSeeder
7. Link storage
bash
php artisan storage:link
8. Start the server
bash
php artisan serve
📡 API Documentation
Base URL
text
http://localhost:8000/api
Authentication
The system uses Laravel Sanctum. Send the token in every authenticated request header:

text
Authorization: Bearer {token}
🔐 Auth Endpoints
Method	Endpoint	Description	Auth
POST	/api/auth/register	Register new account (driver or provider)	❌
POST	/api/auth/login	Login	❌
POST	/api/auth/logout	Logout	✅
Register — Driver
json
POST /api/auth/register
{
  "name": "Ahmed Mohamed",
  "phone": "0911234567",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "driver"
}
Register — Provider
json
POST /api/auth/register
{
  "name": "Salem Ali",
  "phone": "0917654321",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "provider",
  "service_type": "fuel_delivery",
  "vehicle_make": "Toyota",
  "vehicle_model": "Hilux",
  "vehicle_year": 2020,
  "vehicle_plate": "123-BCN",
  "vehicle_color": "White",
  "documents": [
    { "type": "national_id", "file": "<file>" },
    { "type": "driving_license", "file": "<file>" },
    { "type": "vehicle_photo", "file": "<file>" }
  ]
}
Login
json
POST /api/auth/login
{
  "phone": "0911234567",
  "password": "password123"
}
Success Response
json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "phone": "0911234567",
      "role": "driver",
      "status": "active"
    }
  }
}
🚗 Driver Endpoints
Method	Endpoint	Description
GET	/api/driver/requests	Get my request history
POST	/api/driver/requests/fuel	Create fuel delivery request
POST	/api/driver/requests/towing	Create towing request
GET	/api/driver/requests/{id}	Get single request details
PATCH	/api/driver/requests/{id}/cancel	Cancel a request
POST	/api/driver/requests/{id}/rate	Rate a completed service
Fuel Delivery Request
json
POST /api/driver/requests/fuel
{
  "driver_latitude": 32.1234567,
  "driver_longitude": 20.1234567,
  "driver_address": "Al-Rashid Street, Benghazi",
  "fuel_type": "petrol",
  "fuel_quantity": 20,
  "notes": "Car is next to Al-Nour station"
}
Towing Request
json
POST /api/driver/requests/towing
{
  "driver_latitude": 32.1234567,
  "driver_longitude": 20.1234567,
  "driver_address": "Airport Road, Benghazi",
  "plate_number": "456-BCN",
  "notes": "Car won't start at all"
}
Rate a Service
json
POST /api/driver/requests/{id}/rate
{
  "rating": 5,
  "comment": "Excellent and very fast service"
}
🔧 Provider Endpoints
Method	Endpoint	Description
PATCH	/api/provider/profile/availability	Toggle availability
GET	/api/provider/requests	View available requests
PATCH	/api/provider/requests/{id}/accept	Accept a request
PATCH	/api/provider/requests/{id}/reject	Reject a request
PATCH	/api/provider/requests/{id}/status	Update request status
Update Request Status
json
PATCH /api/provider/requests/{id}/status
{
  "status": "en_route"
}
Allowed values in order: en_route → arrived → in_progress → completed

🔔 Notifications Endpoint
Method	Endpoint	Description
GET	/api/notifications	Get all my notifications
📊 Response Format
Success
json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { }
}
Validation Error
json
{
  "success": false,
  "message": "The provided data is invalid",
  "errors": {
    "phone": ["The phone number is already taken"]
  }
}
General Error
json
{
  "success": false,
  "message": "You are not authorized to perform this action"
}
🖥️ Web Dashboard
Access
text
http://localhost:8000/admin/login
Page Permissions
Page	Admin	Employee
Dashboard (statistics)	✅	✅
Provider verification	✅	✅
Active request monitoring	✅	✅
User management	✅	❌
Analytics & reports	✅	❌
Employee management	✅	❌
📁 Project Structure
text
gofull-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── Driver/
│   │   │   │   │   ├── ServiceRequestController.php
│   │   │   │   │   └── RatingController.php
│   │   │   │   ├── Provider/
│   │   │   │   │   ├── ProfileController.php
│   │   │   │   │   └── RequestController.php
│   │   │   │   └── NotificationController.php
│   │   │   └── Web/
│   │   │       ├── Auth/
│   │   │       │   └── LoginController.php
│   │   │       └── Admin/
│   │   │           ├── DashboardController.php
│   │   │           ├── UserController.php
│   │   │           ├── ProviderVerificationController.php
│   │   │           ├── ServiceMonitorController.php
│   │   │           ├── AnalyticsController.php
│   │   │           └── EmployeeController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   └── LoginRequest.php
│   │   │   ├── Driver/
│   │   │   │   ├── FuelDeliveryRequest.php
│   │   │   │   ├── TowingRequest.php
│   │   │   │   └── RatingRequest.php
│   │   │   ├── Provider/
│   │   │   │   └── UpdateStatusRequest.php
│   │   │   └── Admin/
│   │   │       ├── SetAppointmentRequest.php
│   │   │       └── CreateEmployeeRequest.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── ProviderApproved.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── ProviderProfile.php
│   │   ├── ProviderDocument.php
│   │   ├── ServiceRequest.php
│   │   ├── Rating.php
│   │   └── Notification.php
│   └── Services/
│       └── NotificationService.php
├── database/
│   └── migrations/
│       ├── xxxx_create_users_table.php
│       ├── xxxx_create_provider_profiles_table.php
│       ├── xxxx_create_provider_documents_table.php
│       ├── xxxx_create_service_requests_table.php
│       ├── xxxx_create_ratings_table.php
│       └── xxxx_create_notifications_table.php
├── routes/
│   ├── api.php
│   └── web.php
└── resources/
    └── views/
        └── admin/
            ├── auth/
            │   └── login.blade.php
            ├── dashboard.blade.php
            ├── users/
            ├── providers/
            ├── monitor/
            ├── analytics/
            └── employees/
🔒 Core Business Rules
A driver cannot have more than one active request at the same time

A provider cannot accept a new request if they already have an active one

A provider cannot receive requests until admin approval (verification_status = approved)

A provider cannot receive requests if is_available = false

A rating can only be submitted after status = completed

Each request can only receive one rating (unique constraint)

Status transitions must follow the fixed chain — no skipping allowed

Only admin can delete/suspend users, view analytics, and manage employees

Both admin and employee can verify providers and monitor requests

Cancelled requests must store: cancelled_by + cancelled_at + cancellation_reason

🧪 Test Data (Seeders)
bash
# Create first admin account
php artisan db:seed --class=AdminSeeder

# Seed full demo data
php artisan db:seed
📦 Main Dependencies
Package	Version	Purpose
laravel/framework	^11.0	Core framework
laravel/sanctum	^4.0	API token authentication
🗺️ Development Roadmap
 Database design & migrations

 Models with relationships

 Form Requests & validation

 API Controllers

 Web Dashboard Controllers

 Blade Views

 NotificationService (FCM integration)

 Database Seeders

 Feature Tests

🤝 Contributing
Fork the repository

Create a feature branch: git checkout -b feature/your-feature

Commit your changes: git commit -m 'Add some feature'

Push to the branch: git push origin feature/your-feature

Open a Pull Request

👨‍💻 Development Team
Name	Role
Ahmed Ahmeid	Full Stack Developer
📄 License
This project is licensed under the MIT License.

GoFull — We get you back on the road ⛽🚛

Made with ❤️ in Libya

