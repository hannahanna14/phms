# PHMS API Documentation

## Base URL
```
https://your-domain.com/api
```

## Authentication
Most endpoints require authentication. Include the session cookie or use Laravel Sanctum tokens.

## Endpoints

### Authentication & Users
```http
GET /api/user
Authorization: Bearer {token}
```
Returns the authenticated user information.

```http
GET /api/users
Authorization: Required
```
Returns list of all users with id, full_name, and role.

### Students
```http
POST /api/students
Content-Type: application/json

{
  "name": "Student Name",
  "grade": "Grade 1",
  // other student fields
}
```

### Health Examinations
```http
GET /api/health-examination/{id}
Authorization: Required
```

```http
GET /api/health-examination/student/{studentId}?grade_level={grade}
Authorization: Required
```

```http
GET /api/debug/health-examination/{studentId}?grade_level={grade}
Authorization: Required
Description: Debug endpoint for health examinations
```

```http
GET /api/oral-health-examination/student/{studentId}?grade_level={grade}
Authorization: Required
```

### Health Reports
```http
GET /api/students/search?query={searchTerm}
Authorization: Required
```

```http
POST /api/health-report/generate
Content-Type: application/json
Authorization: Required

{
  "student_ids": [1, 2, 3],
  "report_type": "health_examination"
}
```

### PDF Export Routes
```http
GET /health-examination-pdf/{studentId}
Authorization: Required
Description: Export health examination as PDF
```

```http
GET /oral-health-examination/{studentId}/pdf
Authorization: Required
Description: Export oral health examination as PDF
```

```http
GET /test-health-examination-pdf/{studentId}
Authorization: Required
Description: Test health examination PDF generation
```

### Incidents
```http
GET /api/incidents/student/{studentId}
Authorization: Required
```

```http
PUT /api/incidents/{id}/timer-status
Content-Type: application/json
Authorization: Required

{
  "status": "active|paused|completed"
}
```

```http
GET /api/incidents/timer-status/{id}
Authorization: Required
```

### Timer Controls

#### Health Treatment Timers
```http
POST /api/health-treatment/{id}/start-timer
POST /api/health-treatment/{id}/pause-timer
POST /api/health-treatment/{id}/resume-timer
POST /api/health-treatment/{id}/complete-timer
GET /api/health-treatment/timer-status/{id}
Authorization: Required
```

#### Oral Health Treatment Timers
```http
POST /api/oral-health-treatment/{id}/start-timer
POST /api/oral-health-treatment/{id}/pause-timer
POST /api/oral-health-treatment/{id}/resume-timer
POST /api/oral-health-treatment/{id}/complete-timer
GET /api/oral-health-treatment/timer-status/{id}
Authorization: Required
```

#### Incident Timers
```http
POST /api/incidents/{id}/start-timer
POST /api/incidents/{id}/pause-timer
POST /api/incidents/{id}/resume-timer
POST /api/incidents/{id}/complete-timer
Authorization: Required
```

### Notifications
```http
GET /api/notifications/check-timers
GET /api/notifications/check-unrecorded
GET /api/notifications/check-schedules
Authorization: Required
```

### Consultation/Messaging
```http
GET /api/consultation/messages/{conversationId}
Authorization: Required
```

```http
POST /api/consultation/send
Content-Type: application/json
Authorization: Required

{
  "conversation_id": 1,
  "message": "Your message here"
}
```

```http
POST /api/consultation/{conversationId}/read
Authorization: Required
```

### Additional Web APIs
```http
GET /api/dashboard-data
Authorization: Required
```

```http
GET /api/health-treatment/student/{studentId}
Authorization: Required
```

```http
PUT /api/health-treatment/{id}
Content-Type: application/json
Authorization: Required

{
  "treatment_data": "updated values"
}
```

```http
GET /api/oral-health-treatment/student/{studentId}
Authorization: Required
```

```http
PUT /api/oral-health-treatment/{id}
Content-Type: application/json
Authorization: Required

{
  "treatment_data": "updated values"
}
```

```http
GET /api/schedule/events
Authorization: Required
```

### Student Management (Admin Only)
```http
POST /api/student-management/promote
Content-Type: application/json
Authorization: Required (Admin)

{
  "student_ids": [1, 2, 3],
  "new_grade": "Grade 2"
}
```

```http
PUT /api/student-management/student/{studentId}
Content-Type: application/json
Authorization: Required (Admin)

{
  "name": "Updated Name",
  "grade": "Updated Grade"
}
```

```http
GET /api/student-management/students-by-grade?grade={grade}
Authorization: Required (Admin)
```

```http
POST /api/student-management/bulk-assign-teacher
Content-Type: application/json
Authorization: Required (Admin)

{
  "student_ids": [1, 2, 3],
  "teacher_id": 5
}
```

## Response Format
All responses are in JSON format:

### Success Response
```json
{
  "success": true,
  "data": {
    // response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // validation errors if any
  }
}
```

## Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Rate Limiting
API requests may be rate limited. Check response headers for rate limit information.

## Support
For API support, contact: [your-email@domain.com]