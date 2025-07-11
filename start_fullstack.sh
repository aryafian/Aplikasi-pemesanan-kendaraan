#!/bin/bash

echo "========================================"
echo "    VehicleFlow Full Stack Startup"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4+ first."
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+ first."
    exit 1
fi

# Check if npm dependencies are installed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
fi

echo "🚀 Starting PHP Backend (CodeIgniter 3)..."
php -S localhost:8080 -t api/public &
PHP_PID=$!
echo "✅ Backend started at: http://localhost:8080"
echo ""

echo "⏳ Waiting 3 seconds for backend to initialize..."
sleep 3

echo "🎨 Starting React Frontend..."
npm run dev &
FRONTEND_PID=$!
echo "✅ Frontend starting at: http://localhost:5173"
echo ""

echo "========================================"
echo "    Both servers are running!"
echo "========================================"
echo ""
echo "📍 Access Points:"
echo "   - Frontend: http://localhost:5173"
echo "   - Backend API: http://localhost:8080"
echo ""
echo "👤 Login Credentials:"
echo "   - Admin: admin / password"
echo "   - User: user / password"
echo "   - Approver1: approver1 / password"
echo "   - Approver2: approver2 / password"
echo ""
echo "🔧 Development:"
echo "   - Frontend auto-reloads on changes"
echo "   - Backend: restart PHP server for changes"
echo ""
echo "Press Ctrl+C to stop both servers"

# Wait for both processes
wait $PHP_PID $FRONTEND_PID