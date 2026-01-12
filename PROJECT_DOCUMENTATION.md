# Pupil Health Management System (PHMS)

## Overview

PHMS is a comprehensive web-based health management system designed for Naawan Central School. The system manages student health records, examinations, treatments, and reporting from Kinder 2 through Grade 6.

## System Architecture

**Backend:**
- Framework: Laravel 11 (PHP)
- Database: MySQL
- Authentication: Laravel Sanctum
- PDF Generation: DomPDF
- Excel Support: Maatwebsite Excel

**Frontend:**
- Framework: Vue.js 3
- Architecture: Inertia.js (Monolith)
- UI Library: PrimeVue
- Styling: TailwindCSS
- Charting: Chart.js
- Calendar: FullCalendar

## User Roles & Permissions

1. **Admin**
   - View-only access
   - Exporting
   - Logs Viewing
   - Report Generation
   - Schedule creation and editing

2. **Nurse**
   - Health record management
   - Schedule creation and editing
   - Report generation and PDF exports

3. **Teacher**
   - View-only access to assigned students
   - Restricted to their specific grade/section assignments

## Core Modules

### 1. Health Examinations
Records physical measurements (height, weight, BMI), vital signs, and sensory screenings (vision, hearing). Tracks data progression from Kinder 2 to Grade 6.

### 2. Health Treatments
Manages medical treatment records, including chief complaints, diagnoses, and procedures. Features a 2-hour timer for tracking active treatment sessions in the clinic.

### 3. Oral Health Examinations
Provides an interactive dental chart for recording tooth conditions (Decayed, Filled, Extracted) and overall oral health status. Supports both temporary and permanent teeth sets.

### 4. Oral Health Treatments
Documents dental procedures, preventive care, and orthodontic consultations. Includes specific timer functionality for dental sessions.

### 5. Incidents
Tracks accidents and injuries occurring within the school premises, including emergency response details and follow-up requirements.

### 6. Reports
Generates professional PDF reports for health and oral health records. Supports filtering by grade, section, and specific fields. Includes bulk generation capabilities.

### 7. Schedule Management
Manages appointments for health checkups, vaccinations, and other health-related events. Integrates with a calendar view for easy planning.

## Installation and Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL Database

### Setup Steps

1. **Clone the Repository**
   Download the source code to your local machine.

2. **Install Backend Dependencies**
   Run the following command in the project root:
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   Install the necessary Node packages:
   ```bash
   npm install
   ```

4. **Environment Configuration**
   Copy the example environment file and configure your database settings:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update the database credentials in the `.env` file (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

5. **Database Migration and Seeding**
   Set up the database structure and initial data:
   ```bash
   php artisan migrate --seed
   ```

6. **Run the Application**
   Start the development servers. You will need two terminal windows/tabs:

   Terminal 1 (Laravel Server):
   ```bash
   php artisan serve
   ```

   Terminal 2 (Vite Dev Server):
   ```bash
   npm run dev
   ```

## Support

For technical support or feature requests, please contact the system administrator or the development team leear.ramirez@msunaawan.edu.ph(project manager).
