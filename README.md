# SQL Query Optimization Demo

This project demonstrates how to optimize a slow SQL query from approximately **8 seconds down to 38 milliseconds** - that's a 99% performance improvement!

## The Challenge

I was given a SQL query that searches for jobs across multiple related tables. The query was taking around 8 seconds to execute, which is way too slow for a production application. The task was to identify the performance bottlenecks and fix them.

## Performance Results

Here's what I achieved:

| Query Method | Execution Time | Performance |
|--------------|----------------|-------------|
| Original (with LEFT JOINs) | ~5,175 ms | 🐢 Very slow |
| Optimized (with EXISTS) | ~38 ms | ⚡ 136x faster! |

**Bottom line:** The optimized query saves about 5 seconds per search. For a busy application with hundreds of searches per minute, this adds up quickly.

## Getting Started

### Prerequisites

You'll need Docker and Docker Compose installed on your machine. That's it!

### Quick Setup (Recommended)

I've created a setup script that does everything for you:

```bash
# Make the script executable (first time only)
chmod +x setup.sh

# Run the setup
./setup.sh
```

The script will:
- Start all Docker containers (PHP, Nginx, MySQL, phpMyAdmin)
- Install Laravel dependencies
- Set up the environment configuration
- Create database tables
- Populate the database with 240 sample IT jobs

Wait for it to complete (takes about 1-2 minutes), then open http://localhost:8000 in your browser.

### Manual Setup

If you prefer to run commands manually:

```bash
# 1. Start the containers
docker-compose up -d

# 2. Install PHP dependencies
docker-compose exec app composer install

# 3. Set up Laravel environment
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# 4. Create and populate the database
docker-compose exec app php artisan migrate:fresh --seed

# 5. Verify everything is running
docker-compose ps
```

### Accessing the Application

Once setup is complete:

- **Main Demo:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8081
  - Server: `mysql`
  - Username: `dbuser`
  - Password: `dbpassword`

### Testing the Performance Difference

**In your browser:**
1. Open http://localhost:8000
2. Try searching for keywords like "Developer", "Software", "Database", or "Cloud"
3. Click the "Compare Both" button to see the performance difference

**Using command line:**
```bash
curl "http://localhost:8000/api/search/compare?keyword=Developer" | jq .
```

You'll see output like:
```json
{
  "keyword": "Developer",
  "optimized_time_ms": 38.02,
  "slow_time_ms": 5175.74,
  "improvement_percentage": "99.27%",
  "speedup_factor": "136.13x faster"
}
```

## Understanding the Problem

Let me explain what was wrong with the original query and why it was so slow.

### The Original Query Structure

The original query looked something like this (simplified):

```sql
SELECT Jobs.* 
FROM jobs Jobs
LEFT JOIN jobs_personalities ON Jobs.id = jobs_personalities.job_id
LEFT JOIN personalities ON personalities.id = jobs_personalities.personality_id
LEFT JOIN jobs_practical_skills ON Jobs.id = jobs_practical_skills.job_id
LEFT JOIN practical_skills ON practical_skills.id = jobs_practical_skills.skill_id
LEFT JOIN jobs_basic_abilities ON Jobs.id = jobs_basic_abilities.job_id
LEFT JOIN basic_abilities ON basic_abilities.id = jobs_basic_abilities.ability_id
LEFT JOIN jobs_tools ON Jobs.id = jobs_tools.job_id
-- ... and 3 more similar JOINs
WHERE (
    Jobs.name LIKE '%keyword%' OR
    personalities.name LIKE '%keyword%' OR
    practical_skills.name LIKE '%keyword%' OR
    -- ... 17 more OR conditions
)
GROUP BY Jobs.id
LIMIT 50
```

### Problem #1: The Cartesian Product Explosion

This is the killer. When you JOIN multiple tables with one-to-many relationships, the number of rows explodes.

**Real example from our database:**

Let's say we have a job with:
- 3 personalities (from jobs_personalities)
- 3 skills (from jobs_practical_skills)
- 3 abilities (from jobs_basic_abilities)
- 1 tool (from jobs_tools)

When MySQL executes those LEFT JOINs, it creates:
- 3 × 3 × 3 × 1 = **27 rows for just ONE job!**

In our database:
- We have 240 jobs
- Average relationships per job: ~3 personalities, ~3 skills, ~3 abilities, ~1.5 tools
- This creates approximately: 240 jobs × 27 = **6,480 intermediate rows**

MySQL has to process all 6,480 rows before it can even start filtering!

### Problem #2: Expensive GROUP BY

Because of the row explosion, we get duplicate job records. The `GROUP BY Jobs.id` is required to collapse all those duplicates back into unique jobs.

**The problem:** GROUP BY is an expensive operation. MySQL must:
1. Process all 6,480 rows
2. Sort them by job ID
3. Aggregate them back down to 240 unique jobs

This is like sorting 6,480 papers just to count how many unique documents you have.

### Problem #3: No Early Termination

With LEFT JOIN, MySQL must fetch ALL matching rows from every table, even if it finds a match in the first table. There's no way to say "I found a match, stop looking!"

### Problem #4: LIKE with Leading Wildcards

The `LIKE '%keyword%'` pattern can't use indexes effectively because the wildcard is at the beginning. MySQL has to scan every single row in the table.

## The Solution: EXISTS Subqueries

Here's the optimized query approach:

```sql
SELECT Jobs.*
FROM jobs Jobs
INNER JOIN job_categories ON job_categories.id = Jobs.job_category_id
INNER JOIN job_types ON job_types.id = Jobs.job_type_id
WHERE Jobs.publish_status = 1 
  AND Jobs.deleted IS NULL
  AND (
    -- Direct column searches (fast)
    Jobs.name LIKE '%keyword%' OR
    Jobs.description LIKE '%keyword%' OR
    job_categories.name LIKE '%keyword%' OR
    job_types.name LIKE '%keyword%' OR
    
    -- Check relationships with EXISTS (fast!)
    EXISTS (
        SELECT 1 
        FROM jobs_personalities jp
        INNER JOIN personalities p ON p.id = jp.personality_id
        WHERE jp.job_id = Jobs.id 
          AND p.name LIKE '%keyword%'
          AND p.deleted IS NULL
    ) OR
    
    EXISTS (
        SELECT 1 
        FROM jobs_practical_skills jps
        INNER JOIN practical_skills ps ON ps.id = jps.practical_skill_id
        WHERE jps.job_id = Jobs.id 
          AND ps.name LIKE '%keyword%'
          AND ps.deleted IS NULL
    )
    -- ... more EXISTS clauses for other relationships
  )
ORDER BY Jobs.sort_order DESC, Jobs.id DESC
LIMIT 50
```

### Why This is Much Faster

**1. No Cartesian Product**
- Each EXISTS subquery runs independently
- No row multiplication
- We only get 240 rows (one per job) instead of 6,480

**2. No GROUP BY Needed**
- Since there's no row duplication, we don't need to deduplicate
- Saves a huge amount of processing time

**3. Early Termination**
- EXISTS stops as soon as it finds ONE matching row
- If a job has 5 matching skills, EXISTS checks the first one and says "yes, this job matches" - done!
- Much faster than fetching all 5 skills

**4. Better Query Execution**
- MySQL can optimize each EXISTS independently
- More predictable query plan
- Better use of indexes on foreign keys

### Real Performance Impact

Based on our actual database with 240 jobs:

| Metric | Original Query | Optimized Query | Improvement |
|--------|---------------|-----------------|-------------|
| Rows Processed | ~6,480 | ~240 | 96% fewer |
| Execution Time | ~5,175 ms | ~38 ms | 99.3% faster |
| Deduplication | Required (GROUP BY) | Not needed | N/A |

## What's in the Database

The demo uses a realistic IT job search database:

**Main Data:**
- **240 jobs** (12 different job types, 20 jobs each)
  - Full Stack Developer, Backend Engineer, Frontend Developer
  - Data Scientist, Data Engineer, Cloud Architect
  - DevOps Engineer, Security Engineer, ML Engineer
  - Mobile Developer, IT Project Manager, QA Engineer

- **10 job categories**
  - Software Development, Data & Analytics, Cloud & Infrastructure
  - Cybersecurity, AI & Machine Learning, etc.

**Relationships (the tricky part!):**
- 714 personality traits assigned to jobs (avg ~3 per job)
- 713 practical skills (avg ~3 per job)
- 721 basic abilities (avg ~3 per job)
- 351 tools/technologies (avg ~1.5 per job)
- Plus career paths and qualifications

These many-to-many relationships are exactly what caused the original query to be slow.

## Tech Stack

**Backend:**
- PHP 7.4 with PHP-FPM (faster than mod_php)
- Laravel 7 framework
- MySQL 5.7

**Web Server:**
- Nginx (lightweight and fast)

**Infrastructure:**
- Docker & Docker Compose (everything runs in containers)
- phpMyAdmin for database management

All containerized and portable - works on any machine with Docker installed!

## Project Structure

```
job_search/
├── docker-compose.yml          # Defines all containers and how they connect
├── Dockerfile                  # PHP-FPM container configuration
├── setup.sh                    # Automated setup script
├── nginx/
│   └── default.conf           # Nginx web server config
├── src/                        # Laravel application (mounted as volume for live editing)
│   ├── app/Http/Controllers/
│   │   └── JobController.php  # API endpoints (optimized & slow versions)
│   ├── database/
│   │   ├── migrations/        # Database schema (14 tables)
│   │   └── seeds/             # Sample data generator
│   ├── public/
│   │   ├── index.html         # Interactive demo page
│   │   └── css/style.css      # Styling
│   └── routes/
│       └── api.php            # API route definitions
└── docs/
    └── instruction            # Original assignment
```

## Key Takeaways

**What I Learned:**

1. **JOIN Operations Can Be Expensive**
   - Multiple LEFT JOINs with one-to-many relationships create row multiplication
   - Always calculate the potential number of rows: if you have 3 tables with avg 3 relationships each, one row becomes 27 rows (3×3×3)

2. **EXISTS is Your Friend for Optional Matches**
   - Use INNER JOIN for required relationships (like job_category)
   - Use EXISTS for optional many-to-many relationships (like skills, personalities)
   - EXISTS stops at the first match - much faster than fetching all matches

3. **GROUP BY is Expensive**
   - If you need GROUP BY to deduplicate, your query structure probably needs rethinking
   - The best GROUP BY is no GROUP BY

4. **Measure Everything**
   - I went from "this feels slow" to "this takes exactly 5,175ms and processes 6,480 rows"
   - Hard numbers help you track improvement and justify the optimization effort

5. **Real-World Impact**
   - 5 seconds saved per query might not sound like much
   - But with 100 searches per minute: 500 seconds = 8+ minutes of CPU time saved every minute!
   - That's the difference between needing 1 server vs 8 servers

## Production Recommendations

If you're deploying something like this to production, here are some additional improvements:

1. **Add Database Indexes**
   ```sql
   CREATE INDEX idx_jobs_publish ON jobs(publish_status, deleted);
   CREATE INDEX idx_jobs_personality ON jobs_personalities(job_id, personality_id);
   -- etc.
   ```

2. **Use Full-Text Search for Better Text Matching**
   - MySQL FULLTEXT indexes for simple cases
   - Elasticsearch for advanced search features
   - Handles the `LIKE '%keyword%'` problem much better

3. **Add Caching**
   - Redis to cache popular search results
   - Cache for 5-10 minutes
   - Huge impact for frequently searched keywords

4. **Monitor Query Performance**
   - Enable MySQL slow query log
   - Set threshold to 1 second
   - Catch regressions early

5. **Consider Read Replicas**
   - Use master for writes
   - Use replicas for reads (searches)
   - Distribute load across multiple databases

## Troubleshooting

**Containers won't start:**
```bash
# Check if ports are already in use
sudo lsof -i :8000
sudo lsof -i :3306

# Stop all containers and restart
docker-compose down
docker-compose up -d
```

**Database not seeding:**
```bash
# Reset everything
docker-compose exec app php artisan migrate:fresh --seed
```

**Permission errors:**
```bash
# Fix Laravel storage permissions
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

## Time Investment

For transparency, here's how long this took:

- Initial Docker setup: 30 minutes
- Laravel installation: 30 minutes
- Database schema design: 1 hour
- Seed data creation: 1 hour
- API implementation (both versions): 45 minutes
- Frontend demo page: 30 minutes
- Testing and tweaking: 30 minutes
- Documentation: 30 minutes

**Total: ~5 hours**

Most of that was setting up the infrastructure and creating realistic test data. The actual query optimization took maybe 30 minutes once I understood the problem.

---

### I use AI in:
1. Github Copilot:
- Extracting table names and relationship from the query.
- Generating Database Seeds for realistic IT related Jobs data (something that I understands better).
- Setting up a single docker container with Laravel app, Nginx, Mysql, Phpmyadmin with configurations and dependencies




