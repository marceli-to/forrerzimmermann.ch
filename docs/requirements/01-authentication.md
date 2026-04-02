# Authentication & Authorization

## Overview

Session-based authentication using Laravel Breeze (Blade stack). API calls use the same session - no separate token system.

---

## Authentication

### Package

Laravel Breeze with Blade views.

### Features

- Login (`/login`)
- Logout
- Password reset via email
- Remember me functionality

### Routes

```
GET   /login              → Show login form
POST  /login              → Authenticate user
POST  /logout             → Logout user
GET   /forgot-password    → Show password reset request form
POST  /forgot-password    → Send password reset link
GET   /reset-password/{token} → Show password reset form
POST  /reset-password     → Reset password
```

### Session Configuration

- Session driver: `database` (for scalability)
- Session lifetime: 120 minutes (configurable)
- Secure cookies in production

---

## Users

### Model: `User`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key (internal) |
| uuid | uuid | Unique, public identifier |
| firstname | string | First name |
| name | string | Last name |
| email | string | Unique, used for login |
| password | string | Hashed |
| role | enum | `admin`, `editor`, `viewer` |
| remember_token | string | For "remember me" |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### Notes

- Users are **not linked** to team members (separate entities)
- Only admins can manage users
- Users have soft deletes

---

## Roles & Permissions

### Roles

| Role | Description |
|------|-------------|
| `admin` | Full access to all features |
| `editor` | Can view/create/edit, but cannot delete |
| `viewer` | Read-only access to all content |

### Permission Matrix

| Action | Admin | Editor | Viewer |
|--------|-------|--------|--------|
| **Users** | | | |
| View users | Yes | No | No |
| Create users | Yes | No | No |
| Edit users | Yes | No | No |
| Delete users | Yes | No | No |
| **Content (Projects, Team, etc.)** | | | |
| View | Yes | Yes | Yes |
| Create | Yes | Yes | No |
| Edit | Yes | Yes | No |
| Delete | Yes | No | No |
| Restore (soft deleted) | Yes | No | No |
| **Settings** | | | |
| View/Edit settings | Yes | No | No |

### Implementation

Use Laravel Policies for authorization:

```php
// app/Policies/ProjectPolicy.php

public function viewAny(User $user): bool
{
    return true; // All roles can view
}

public function create(User $user): bool
{
    return in_array($user->role, ['admin', 'editor']); // Viewer cannot create
}

public function update(User $user, Project $project): bool
{
    return in_array($user->role, ['admin', 'editor']); // Viewer cannot edit
}

public function delete(User $user, Project $project): bool
{
    return $user->role === 'admin'; // Only admin
}

public function restore(User $user, Project $project): bool
{
    return $user->role === 'admin'; // Only admin
}
```

---

## Middleware

### Route Protection

```php
// routes/web.php

Route::prefix('dashboard')
    ->middleware(['auth'])
    ->group(function () {
        // Dashboard routes
    });

Route::prefix('api/dashboard')
    ->middleware(['auth'])
    ->group(function () {
        // API routes (session-based)
    });
```

### Admin-Only Middleware

```php
// app/Http/Middleware/AdminOnly.php

public function handle(Request $request, Closure $next)
{
    if ($request->user()->role !== 'admin') {
        abort(403);
    }
    return $next($request);
}
```

Usage:

```php
Route::resource('users', UserController::class)
    ->middleware('admin');
```

---

## User Management (Admin Only)

### Routes

```
GET    /dashboard/users           → List users
GET    /dashboard/users/create    → Create user form
POST   /dashboard/users           → Store user
GET    /dashboard/users/{id}/edit → Edit user form
PUT    /dashboard/users/{id}      → Update user
DELETE /dashboard/users/{id}      → Soft delete user
```

### Validation Rules

**Create User:**
```php
[
    'firstname' => 'required|string|max:255',
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:8|confirmed',
    'role' => 'required|in:admin,editor,viewer',
]
```

**Update User:**
```php
[
    'firstname' => 'required|string|max:255',
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email,' . $user->id,
    'password' => 'nullable|min:8|confirmed',
    'role' => 'required|in:admin,editor,viewer',
]
```

---

## Security Considerations

- CSRF protection on all forms
- Rate limiting on login attempts (Laravel's default)
- Password hashing via bcrypt
- Secure session cookies (`secure`, `http_only`, `same_site`)
- No API tokens exposed (session-only auth)
