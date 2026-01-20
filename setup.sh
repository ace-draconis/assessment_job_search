#!/bin/bash

echo "🚀 Setting up Job Search SQL Optimization Demo..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Start containers
echo "📦 Starting Docker containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Install composer dependencies if vendor doesn't exist
if [ ! -d "src/vendor" ]; then
    echo "📚 Installing Composer dependencies..."
    docker-compose exec -T app composer install
fi

# Setup environment file
if [ ! -f "src/.env" ]; then
    echo "⚙️  Setting up environment configuration..."
    docker-compose exec -T app cp .env.example .env
    docker-compose exec -T app php artisan key:generate
fi

# Run migrations and seed database
echo "🗄️  Setting up database..."
docker-compose exec -T app php artisan migrate:fresh --seed

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access your application:"
echo "   - Application: http://localhost:8000"
echo "   - Demo Page: http://localhost:8000/demo.html"
echo "   - phpMyAdmin: http://localhost:8081"
echo ""
echo "📊 Test the API:"
echo "   curl \"http://localhost:8000/api/search/compare?keyword=Flight\" | jq ."
echo ""
echo "🛠️  Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop containers: docker-compose down"
echo "   - Restart: docker-compose restart"
echo ""
