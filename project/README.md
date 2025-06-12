# Social Media Backend API

A comprehensive social media backend built with Laravel, featuring user authentication, posts, comments, likes, friendships, and admin dashboard.

## Features

### User Features
- User registration and authentication
- User profiles with personal information
- Create, read, update, delete posts
- Comment on posts with nested replies
- Like posts and comments
- Friend system with friend requests
- Privacy settings for posts (public, friends, private)

### Admin Features
- Admin dashboard with statistics
- User management (view, edit, activate/deactivate, delete)
- Post management (view, moderate, delete)
- System analytics and recent activity monitoring

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure your database in `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=social_media
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Seed the database (optional):
   ```bash
   php artisan db:seed
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/me` - Get current user info

### Posts
- `GET /api/posts` - Get all posts (with privacy filtering)
- `POST /api/posts` - Create new post
- `GET /api/posts/{id}` - Get specific post
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post
- `GET /api/users/{userId}/posts` - Get user's posts

### Comments
- `POST /api/posts/{postId}/comments` - Add comment to post
- `PUT /api/comments/{id}` - Update comment
- `DELETE /api/comments/{id}` - Delete comment
- `GET /api/comments/{id}/replies` - Get comment replies

### Likes
- `POST /api/posts/{id}/like` - Toggle like on post
- `POST /api/comments/{id}/like` - Toggle like on comment

### Friendships
- `POST /api/users/{userId}/friend-request` - Send friend request
- `POST /api/friendships/{id}/accept` - Accept friend request
- `POST /api/friendships/{id}/decline` - Decline friend request
- `DELETE /api/users/{userId}/friend` - Remove friend
- `GET /api/friend-requests` - Get pending friend requests
- `GET /api/friends` - Get user's friends

### Admin Routes
- `GET /api/admin/dashboard/stats` - Get system statistics
- `GET /api/admin/dashboard/activity` - Get recent activity
- `GET /api/admin/users` - Get all users (with filtering)
- `GET /api/admin/users/{id}` - Get specific user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `POST /api/admin/users/{id}/toggle-status` - Toggle user status
- `GET /api/admin/posts` - Get all posts (with filtering)
- `GET /api/admin/posts/{id}` - Get specific post
- `POST /api/admin/posts/{id}/toggle-status` - Toggle post status
- `DELETE /api/admin/posts/{id}` - Delete post

## Authentication

This API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Default Admin Account

After running the seeder, you can login with:
- Email: admin@example.com
- Password: password

## Database Schema

### Users Table
- Personal information (name, email, phone, bio, etc.)
- Profile and cover pictures
- Role-based access (user/admin)
- Activity tracking (last_seen, is_active)

### Posts Table
- User-generated content with optional images
- Privacy settings (public, friends, private)
- Like and comment counters
- Soft deletion support

### Comments Table
- Nested comments with parent-child relationships
- User attribution and post association
- Moderation support

### Likes Table
- Polymorphic relationships (can like posts or comments)
- Unique constraint to prevent duplicate likes

### Friendships Table
- Friend request system with status tracking
- Bidirectional friendship support
- Block functionality

## Privacy & Security

- All routes are protected with authentication middleware
- Admin routes require admin role
- Users can only edit/delete their own content
- Privacy settings control post visibility
- Input validation on all endpoints
- Password hashing with bcrypt

## File Upload

Posts support image uploads stored in the `storage/app/public/posts` directory. Make sure to create a storage link:

```bash
php artisan storage:link
```