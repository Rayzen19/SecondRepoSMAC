# Real-Time Messaging System Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    REAL-TIME MESSAGING SYSTEM                   │
│                   St. Matthew Senior High School                │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│    ADMIN     │  │   TEACHER    │  │   STUDENT    │  │  GUARDIAN    │
│   (User 1)   │  │   (User 2)   │  │   (User 3)   │  │   (User 4)   │
└──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘
       │                 │                 │                 │
       └─────────────────┴─────────────────┴─────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │   Laravel Backend   │
                    │  Message Controller │
                    └──────────┬──────────┘
                               │
              ┌────────────────┼────────────────┐
              │                │                │
              ▼                ▼                ▼
       ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
       │   Database  │  │ MessageSent │  │    Queue    │
       │   (MySQL)   │  │    Event    │  │   Worker    │
       └─────────────┘  └─────────────┘  └──────┬──────┘
                                                  │
                                                  ▼
                                         ┌────────────────┐
                                         │     Pusher     │
                                         │  (WebSocket)   │
                                         └────────┬───────┘
                                                  │
                    ┌─────────────────────────────┼─────────────────────┐
                    │                             │                     │
                    ▼                             ▼                     ▼
         ┌──────────────────┐        ┌──────────────────┐   ┌──────────────────┐
         │  Private Channel │        │  Private Channel │   │  Private Channel │
         │   user.1 (Admin) │        │ user.2 (Teacher) │   │ user.3 (Student) │
         └──────────────────┘        └──────────────────┘   └──────────────────┘
```

## Message Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          SENDING A MESSAGE                                  │
└─────────────────────────────────────────────────────────────────────────────┘

   Teacher (Browser)
        │
        │ 1. Clicks "Send"
        ▼
   [POST /teacher/messenger/send]
        │
        │ 2. AJAX Request
        ▼
   MessageController::sendConversation()
        │
        ├─── 3. Save to Database
        │         │
        │         ▼
        │    messages table
        │    message_recipients table
        │
        └─── 4. Broadcast Event
                │
                ▼
          broadcast(new MessageSent(...))
                │
                │ 5. Queue Job
                ▼
          jobs table (queued)
                │
                │ 6. Queue Worker Processes
                ▼
          Queue Worker (php artisan queue:work)
                │
                │ 7. Send to Pusher
                ▼
          Pusher API
                │
                │ 8. WebSocket Broadcast
                ▼
          Private Channel: user.{recipient_id}
                │
                │ 9. Echo Listener Receives
                ▼
          Student (Browser) - Real-time Update!
                │
                └─── Message appears instantly ✨
```

## Database Schema

```
┌─────────────────────────────────────────────────────────────────┐
│                        DATABASE TABLES                          │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────┐
│      messages        │
├──────────────────────┤
│ id                   │ PK
│ sender_id            │ FK → users.id
│ subject              │
│ body                 │ TEXT
│ created_at           │
│ updated_at           │
└──────────┬───────────┘
           │
           │ 1:N
           │
           ▼
┌──────────────────────┐
│ message_recipients   │
├──────────────────────┤
│ id                   │ PK
│ message_id           │ FK → messages.id
│ recipient_id         │ FK → users.id
│ read_at              │ NULLABLE
│ created_at           │
│ updated_at           │
└──────────┬───────────┘
           │
           │ N:1
           │
           ▼
┌──────────────────────┐
│       users          │
├──────────────────────┤
│ id                   │ PK
│ name                 │
│ email                │
│ userable_type        │ (Admin, Teacher, Student, Guardian)
│ userable_id          │
│ created_at           │
│ updated_at           │
└──────────────────────┘
```

## User Roles Communication Matrix

```
┌─────────────────────────────────────────────────────────────────┐
│              WHO CAN MESSAGE WHOM?                              │
└─────────────────────────────────────────────────────────────────┘

           │ Admin │ Teacher │ Student │ Guardian │
───────────┼───────┼─────────┼─────────┼──────────┤
   Admin   │   ✓   │    ✓    │    ✓    │    ✓     │
───────────┼───────┼─────────┼─────────┼──────────┤
  Teacher  │   ✓   │    ✓    │    ✓    │    ✓     │
───────────┼───────┼─────────┼─────────┼──────────┤
  Student  │   ✓   │    ✓    │    ✓    │    ✗     │
───────────┼───────┼─────────┼─────────┼──────────┤
 Guardian  │   ✓   │    ✓    │    ✗    │    ✗     │
───────────┴───────┴─────────┴─────────┴──────────┘

✓ = Can message
✗ = Typically wouldn't message (but not restricted)
```

## File Structure

```
project-root/
│
├── app/
│   ├── Events/
│   │   └── MessageSent.php                    [NEW] ✨
│   │
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   └── MessageController.php          [UPDATED] 🔄
│   │   ├── Teacher/
│   │   │   └── MessageController.php          [UPDATED] 🔄
│   │   ├── Student/
│   │   │   └── MessageController.php          [UPDATED] 🔄
│   │   └── Guardian/
│   │       └── MessageController.php          [UPDATED] 🔄
│   │
│   └── Providers/
│       └── BroadcastServiceProvider.php       [NEW] ✨
│
├── bootstrap/
│   └── providers.php                          [UPDATED] 🔄
│
├── config/
│   └── broadcasting.php                       [NEW] ✨
│
├── resources/views/
│   ├── admin/components/
│   │   └── template.blade.php                 [UPDATED] 🔄
│   ├── teacher/
│   │   ├── components/
│   │   │   └── template.blade.php             [UPDATED] 🔄
│   │   └── messages/
│   │       └── messenger.blade.php            [NEW] ✨
│   ├── student/
│   │   ├── components/
│   │   │   └── template.blade.php             [UPDATED] 🔄
│   │   └── messages/
│   │       └── messenger.blade.php            [NEW] ✨
│   ├── guardian/
│   │   ├── components/
│   │   │   └── template.blade.php             [UPDATED] 🔄
│   │   └── messages/
│   │       └── messenger.blade.php            [NEW] ✨
│   └── messages/
│       └── messenger.blade.php                [UPDATED] 🔄
│
├── routes/
│   ├── channels.php                           [NEW] ✨
│   └── web.php                                [UPDATED] 🔄
│
├── .env                                       [UPDATED] 🔄
│
└── Documentation/
    ├── REALTIME_MESSAGING_SETUP.md           [NEW] ✨
    ├── QUICK_START_MESSAGING.md              [NEW] ✨
    ├── MESSAGING_IMPLEMENTATION_SUMMARY.md   [NEW] ✨
    └── MESSAGING_ARCHITECTURE.md             [NEW] ✨ (this file)
```

## Technology Stack

```
┌─────────────────────────────────────────────────────────────────┐
│                    TECHNOLOGY STACK                             │
└─────────────────────────────────────────────────────────────────┘

Backend:
├── Laravel 12.0        (PHP Framework)
├── Pusher PHP Server   (Broadcasting)
└── MySQL              (Database)

Frontend:
├── JavaScript ES6      (Client-side logic)
├── Pusher.js          (WebSocket client)
├── Laravel Echo       (Broadcasting client)
└── Bootstrap 5        (UI Framework)

Infrastructure:
├── XAMPP              (Local development)
├── Queue Worker       (Background processing)
└── Pusher Channels    (WebSocket service)
```

## Security Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    SECURITY LAYERS                              │
└─────────────────────────────────────────────────────────────────┘

Layer 1: Authentication
    ├── Laravel Multi-Auth Guards
    │   ├── admin
    │   ├── teacher
    │   ├── student
    │   └── guardian
    └── Session-based authentication

Layer 2: Authorization
    ├── Private Channels
    │   └── user.{id} → Only accessible by user with that ID
    └── Channel Authorization
        └── Broadcast::channel('user.{id}', callback)

Layer 3: Data Protection
    ├── CSRF Tokens (all POST requests)
    ├── TLS/HTTPS (encrypted in transit)
    └── Input Validation (Laravel validation rules)

Layer 4: Access Control
    └── Middleware Stack
        ├── auth:admin,teacher,student,guardian
        └── Pusher authentication endpoint
```

## Performance Optimization

```
┌─────────────────────────────────────────────────────────────────┐
│                  PERFORMANCE FEATURES                           │
└─────────────────────────────────────────────────────────────────┘

1. Async Broadcasting
   └── Messages sent to queue, not blocking request

2. Database Indexing
   ├── Index on message_recipients.recipient_id
   └── Index on messages.sender_id

3. Lazy Loading
   └── Messages loaded on-demand per conversation

4. WebSocket Efficiency
   ├── Private channels (not broadcasting to all)
   └── Selective message delivery

5. Caching
   └── Config cached in production
```

## Deployment Checklist

```
□ Get Pusher account (free tier)
□ Add Pusher credentials to .env
□ Update BROADCAST_CONNECTION=pusher
□ Clear config cache
□ Test queue worker
□ Set up supervisor for queue worker (production)
□ Configure database queue table
□ Test real-time messaging
□ Monitor Pusher usage dashboard
□ Set up error logging
□ Configure backup queue driver
```

## Monitoring & Debugging

```
┌─────────────────────────────────────────────────────────────────┐
│                 MONITORING POINTS                               │
└─────────────────────────────────────────────────────────────────┘

1. Pusher Dashboard
   ├── Active connections
   ├── Message throughput
   └── Error rates

2. Laravel Logs
   └── storage/logs/laravel.log

3. Browser Console
   ├── WebSocket connection status
   ├── Echo initialization
   └── Message events

4. Queue Worker
   ├── php artisan queue:work output
   └── Failed jobs table

5. Database
   ├── messages count
   ├── message_recipients count
   └── jobs table
```

---

**Legend:**
- [NEW] ✨ - Newly created file
- [UPDATED] 🔄 - Modified existing file
- ✓ - Feature enabled
- ✗ - Feature not typically used
