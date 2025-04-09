# Task Management Application

A Laravel-based task management system that allows users to create, organize, and track tasks with subtasks capabilities, status tracking, and soft delete functionality.

## Features

- **Task Management**: Create, edit, view, and delete tasks
- **Subtasks**: Organize work by creating hierarchical task structures
- **Status Tracking**: Track tasks with "To Do", "In Progress", and "Done" statuses
- **Visibility Control**: Set tasks as "Draft" or "Published"
- **Soft Delete**: Tasks are moved to trash before permanent deletion
- **Image Attachments**: Attach images to tasks for better context
- **Task Filtering**: Filter tasks by status, search by title, and sort results
- **Progress Tracking**: Monitor subtask completion progress

## Technology Stack

- **Framework**: Laravel
- **Database**: MySQl
- **Frontend**: Blade templates with CSS/JS
- **Authentication**: Laravel's built-in auth system
- **Storage**: Laravel's filesystem for image handling
- **Task Scheduling**: Laravel's command scheduler for maintenance tasks

## Installation

1. Clone the repository:
   ```
   git clone [repository-url]
   cd task-management
   ```

2. Install dependencies:
   ```
   composer install
   npm install
   ```

3. Set up environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database in `.env` file
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database/database.sqlite
   ```

5. Run migrations and seeders:
   ```
   php artisan migrate
   php artisan db:seed
   ```

6. Compile assets:
   ```
   npm run build
   ```

7. Create storage symlinks:
   ```
   php artisan storage:link
   ```

8. Start the development server:
   ```
   php artisan serve
   ```

## Project Structure

### Models

- **Task**: Represents a task with soft delete capability
  - Attributes:
    - title
    - content
    - status (to_do, in_progress, done)
    - visibility (draft, published)
    - image
    - user_id
    - parent_id
  - Relationships:
    - Belongs to a User
    - Can have a parent Task
    - Can have multiple subtasks

- **User**: Standard Laravel user model

### Controllers

- **TaskController**: Handles all task-related operations
  - index: List tasks with filtering and pagination
  - create/store: Create new tasks/subtasks
  - show: View task details and subtasks
  - edit/update: Update task information
  - destroy: Soft-delete tasks
  - toggleStatus: Change task status (to_do → in_progress → done)
  - toggleVisibility: Switch between draft/published states

### Commands

- **PurgeDeletedTasks**: Permanently removes trashed tasks after a specified period
  - Usage: `php artisan tasks:purge [days]`
  - Default: 30 days

### Observers

- **TaskObserver**: Monitors and reacts to task model events

## Database Schema

### Tasks Table
- id (primary key)
- title (string)
- content (text)
- status (string): to_do, in_progress, done
- visibility (string): draft, published
- image (string, nullable): path to task image
- user_id (foreign key): references users table
- parent_id (foreign key, nullable): references tasks table for subtasks
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable): for soft deletes

### Users Table
- Standard Laravel users table with authentication fields

## Task Management Workflow

1. **Creating Tasks**:
   - Main tasks can be created from the tasks index page
   - Subtasks can be created from a parent task's detail page

2. **Task Status Flow**:
   - Tasks start as "To Do"
   - Can be moved to "In Progress"
   - Finally marked as "Done"
   - Status can be toggled directly from task listings

3. **Visibility Control**:
   - Tasks can be kept as "Draft" during preparation
   - Published when ready to be shared/implemented

4. **Task Deletion**:
   - When deleted, tasks are moved to trash (soft deleted)
   - Permanently removed after specified days by scheduled command

## Maintenance

### Scheduled Commands

To enable automatic purging of deleted tasks, add this to your server's crontab:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This will run the scheduler which includes the `tasks:purge` command on its configured schedule.

## Development Guidelines

### Adding New Features

1. Create appropriate database migrations if needed
2. Update or create models with proper relationships and attributes
3. Create or modify controllers to handle new functionality
4. Create blade views for any new UI components
5. Add routes in web.php
6. Write tests for new functionality

### Coding Standards

- Follow PSR-12 coding standards
- Use Laravel naming conventions
- Document all classes and methods with PHPDoc
- Use type hints where appropriate

## Contributing

1. Create a feature branch
2. Make changes
3. Ensure tests pass
4. Submit a pull request