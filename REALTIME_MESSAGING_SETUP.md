# Real-Time Messaging System with Pusher - Setup Guide

## Overview
This guide explains how to set up the real-time messaging system using Pusher for Laravel, allowing all users (Admin, Teacher, Student, Guardian) to communicate in real-time.

## Prerequisites
- Pusher account (free tier available at https://pusher.com)
- Composer installed
- Laravel application running

## Installation Steps

### 1. Install Pusher PHP Server (Already Done)
```bash
composer require pusher/pusher-php-server
```

### 2. Create Pusher Account and Get Credentials
1. Go to https://pusher.com and create a free account
2. Create a new Channels app
3. Get your credentials:
   - App ID
   - Key
   - Secret
   - Cluster (e.g., `ap1`, `eu`, `us2`)

### 3. Update .env File
Add the following to your `.env` file with your Pusher credentials:

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# Optional settings
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
```

### 4. Update Vite Configuration (For Real-time Updates)
Add Pusher environment variables to your `vite.config.js`:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    define: {
        'process.env.PUSHER_APP_KEY': JSON.stringify(process.env.PUSHER_APP_KEY || ''),
        'process.env.PUSHER_APP_CLUSTER': JSON.stringify(process.env.PUSHER_APP_CLUSTER || ''),
    },
});
```

### 5. Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 6. Run Queue Worker (Important!)
Pusher broadcasting requires a queue worker to be running:

```bash
php artisan queue:work
```

Or for development (auto-reloads on code changes):
```bash
php artisan queue:listen
```

**Note**: Keep this terminal window open while testing real-time features.

## Features Implemented

### 1. Real-Time Message Delivery
- Messages are instantly delivered to recipients without page refresh
- Uses Pusher WebSocket connection for real-time updates
- Private channels ensure messages are only sent to intended recipients

### 2. Multi-User Support
- Admin can message Teachers, Students, and Guardians
- Teachers can message Admins, other Teachers, Students
- Students can message Admins, Teachers, other Students
- Guardians can message Admins, Teachers

### 3. Message Notifications
- Visual notifications when new messages arrive
- Badge counter for unread messages (UI ready)
- Automatic scroll to latest message

### 4. Security
- Private channels authenticated per user
- Only authenticated users can access their messages
- Messages encrypted in transit via TLS

## File Structure

### Controllers (with Broadcasting)
- `app/Http/Controllers/Admin/MessageController.php`
- `app/Http/Controllers/Teacher/MessageController.php`
- `app/Http/Controllers/Student/MessageController.php`
- `app/Http/Controllers/Guardian/MessageController.php`

### Events
- `app/Events/MessageSent.php` - Broadcast event for new messages

### Views (with Pusher Integration)
- `resources/views/messages/messenger.blade.php` (Admin)
- `resources/views/teacher/messages/messenger.blade.php`
- `resources/views/student/messages/messenger.blade.php`
- `resources/views/guardian/messages/messenger.blade.php`

### Configuration
- `config/broadcasting.php` - Broadcasting configuration
- `routes/channels.php` - Channel authorization
- `app/Providers/BroadcastServiceProvider.php` - Service provider

## Usage

### Sending Messages
1. Navigate to Messages section in sidebar
2. Click on a conversation or click "New" to start a new conversation
3. Type your message and press Send
4. Message is instantly delivered to recipient if they're online

### Receiving Messages
- Messages appear in real-time without refresh
- Sound/visual notification can be added
- Unread badge shows number of new messages

## Testing Real-Time Functionality

### Test Steps:
1. Open two different browsers (or incognito + regular)
2. Log in as different users (e.g., Admin in one, Teacher in another)
3. Go to Messages section in both browsers
4. Start a conversation
5. Send a message from one browser
6. **Message should appear instantly in the other browser without refresh**

### Troubleshooting:

#### Messages not appearing in real-time?
1. Check if queue worker is running: `php artisan queue:work`
2. Verify Pusher credentials in `.env`
3. Check browser console for JavaScript errors
4. Verify Pusher dashboard shows connections

#### "Unauthorized" error?
- Clear config cache: `php artisan config:clear`
- Check if BroadcastServiceProvider is registered
- Verify user is authenticated

#### Connection failed?
- Check PUSHER_APP_CLUSTER matches your Pusher app
- Verify PUSHER_APP_KEY is correct
- Check if firewall is blocking WebSocket connections

## Advanced Configuration

### Queue Configuration
Update `.env` for better performance:
```env
QUEUE_CONNECTION=database
```

Then run migrations if not already done:
```bash
php artisan queue:table
php artisan migrate
```

### Production Deployment
1. Use supervisor to keep queue worker running
2. Enable Pusher encryption for sensitive data
3. Consider Redis for better queue performance
4. Monitor Pusher usage to stay within limits

### Example Supervisor Config:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## API Endpoints

### Broadcasting Auth
- `POST /broadcasting/auth` - Authenticate private channel subscriptions

### Messages
- `GET /{role}/messages` - Inbox view
- `GET /{role}/messenger` - Messenger UI
- `GET /{role}/messenger/conversation/{user}` - Get conversation
- `POST /{role}/messenger/send` - Send message (triggers broadcast)

## Security Considerations

1. **Channel Authorization**: Each user can only subscribe to their own private channel
2. **Message Validation**: All messages validated before sending
3. **CSRF Protection**: All POST requests require CSRF token
4. **Authentication**: Must be logged in to send/receive messages
5. **TLS Encryption**: All WebSocket connections encrypted

## Performance Tips

1. **Use Redis** for queue and cache in production
2. **Database Indexing**: Add indexes on `message_recipients.recipient_id`
3. **Pagination**: Implement lazy loading for old messages
4. **Connection Pooling**: Configure Pusher max connections
5. **Monitor**: Watch Pusher dashboard for connection limits

## Future Enhancements

- [ ] Typing indicators
- [ ] Read receipts
- [ ] Message reactions
- [ ] File attachments
- [ ] Group messaging
- [ ] Search functionality
- [ ] Message deletion
- [ ] Sound notifications
- [ ] Desktop notifications via browser API
- [ ] Mobile push notifications

## Support

For issues:
1. Check Pusher dashboard for connection status
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check browser console for JavaScript errors
4. Verify queue worker is processing jobs

## Resources

- Pusher Documentation: https://pusher.com/docs/channels
- Laravel Broadcasting: https://laravel.com/docs/broadcasting
- Laravel Echo: https://github.com/laravel/echo
