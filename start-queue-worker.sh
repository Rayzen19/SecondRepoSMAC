#!/bin/bash
# Queue Worker Starter for SMAC Announcement Notifications
# This script starts the Laravel queue worker to process email notifications

echo "========================================"
echo "  SMAC Queue Worker"
echo "  Processing Email Notifications"
echo "========================================"
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "ERROR: artisan file not found!"
    echo "Please run this script from the Laravel project root directory."
    echo ""
    exit 1
fi

echo "Starting queue worker..."
echo ""
echo "Press Ctrl+C to stop the worker"
echo "========================================"
echo ""

# Start the queue worker with 3 retry attempts
php artisan queue:work --tries=3 --timeout=90
