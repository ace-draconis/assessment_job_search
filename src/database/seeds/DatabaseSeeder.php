<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Malaysian locations for IT jobs
        $malaysianLocations = [
            'Kuala Lumpur, Federal Territory',
            'Petaling Jaya, Selangor',
            'Cyberjaya, Selangor',
            'Shah Alam, Selangor',
            'Subang Jaya, Selangor',
            'Puchong, Selangor',
            'Klang, Selangor',
            'Ampang, Selangor',
            'Cheras, Kuala Lumpur',
            'Bangsar, Kuala Lumpur',
            'Mont Kiara, Kuala Lumpur',
            'KLCC, Kuala Lumpur',
            'Putrajaya, Federal Territory',
            'Penang, Pulau Pinang',
            'Georgetown, Pulau Pinang',
            'Bayan Lepas, Pulau Pinang',
            'Johor Bahru, Johor',
            'Iskandar Puteri, Johor',
            'Skudai, Johor',
            'Kuching, Sarawak',
            'Miri, Sarawak',
            'Kota Kinabalu, Sabah',
            'Sandakan, Sabah',
            'Ipoh, Perak',
            'Melaka, Melaka',
            'Seremban, Negeri Sembilan',
            'Kuantan, Pahang',
            'Kota Bharu, Kelantan',
            'Alor Setar, Kedah',
            'Sungai Petani, Kedah',
        ];
        
        // Realistic IT job descriptions by type
        $itJobDescriptions = [
            'Full Stack Developer' => [
                'description' => 'Build end-to-end web applications using modern JavaScript frameworks and backend technologies. Design responsive user interfaces and robust APIs to deliver seamless user experiences.',
                'detail' => 'We are seeking a Full Stack Developer to create innovative web applications from concept to deployment. You will work with React/Vue.js on the frontend, Node.js/Python on the backend, and databases like PostgreSQL/MongoDB. Responsibilities include API design, database optimization, implementing authentication, and ensuring code quality through testing. Experience with cloud platforms (AWS/Azure) and CI/CD pipelines is highly valued.',
            ],
            'Backend Engineer' => [
                'description' => 'Design and implement scalable server-side applications and APIs. Build robust microservices architecture and optimize database performance for high-traffic systems.',
                'detail' => 'Join our backend team to architect and develop high-performance server applications. You will design RESTful APIs, implement business logic, optimize database queries, and ensure system reliability. Strong experience with Java/Python/Node.js, SQL/NoSQL databases, message queues, and caching systems is required. You will also participate in system design discussions, conduct code reviews, and mentor junior developers.',
            ],
            'Frontend Developer' => [
                'description' => 'Create beautiful and responsive user interfaces using modern JavaScript frameworks. Transform design mockups into pixel-perfect, interactive web applications.',
                'detail' => 'We are looking for a Frontend Developer passionate about creating exceptional user experiences. You will work with React/Vue/Angular to build dynamic single-page applications, implement responsive designs, optimize performance, and ensure cross-browser compatibility. Strong knowledge of HTML5, CSS3, JavaScript ES6+, and state management (Redux/Vuex) is essential. Experience with TypeScript, testing frameworks, and design systems is a plus.',
            ],
            'Data Scientist' => [
                'description' => 'Analyze complex datasets to extract actionable insights and build predictive models. Apply machine learning algorithms to solve business problems and drive data-driven decision making.',
                'detail' => 'Join our data science team to leverage advanced analytics and machine learning techniques. You will develop predictive models, perform statistical analysis, create data visualizations, and present findings to stakeholders. Strong programming skills in Python/R, experience with ML libraries (scikit-learn, TensorFlow, PyTorch), and knowledge of SQL and big data tools (Spark, Hadoop) are required. You will collaborate with engineers to deploy models and monitor their performance in production.',
            ],
            'Data Engineer' => [
                'description' => 'Build and maintain robust data pipelines and infrastructure. Design scalable ETL processes to transform raw data into actionable insights for analytics and ML teams.',
                'detail' => 'We are seeking a Data Engineer to develop and optimize our data infrastructure. You will design and implement ETL pipelines, build data warehouses, ensure data quality, and create automated workflows. Experience with cloud platforms (AWS/GCP/Azure), data processing tools (Airflow, Spark), SQL/NoSQL databases, and programming in Python/Scala is essential. You will work closely with data scientists and analysts to support their data needs.',
            ],
            'Cloud Architect' => [
                'description' => 'Design and implement cloud infrastructure solutions that are scalable, secure, and cost-effective. Lead cloud migration initiatives and establish best practices for cloud adoption.',
                'detail' => 'We are looking for a Cloud Architect to design our cloud infrastructure strategy. You will architect multi-cloud solutions, implement infrastructure as code (Terraform/CloudFormation), design disaster recovery plans, and optimize cloud costs. Deep expertise in AWS/Azure/GCP, containerization (Docker/Kubernetes), networking, and security is required. You will also mentor engineers and establish cloud governance frameworks.',
            ],
            'DevOps Engineer' => [
                'description' => 'Automate deployment processes and maintain CI/CD pipelines. Bridge development and operations teams to enable rapid and reliable software delivery.',
                'detail' => 'Join our DevOps team to streamline software delivery and infrastructure management. You will build and maintain CI/CD pipelines, implement infrastructure as code, monitor system performance, and ensure high availability. Experience with Jenkins/GitLab CI, Docker/Kubernetes, cloud platforms, scripting (Python/Bash), and monitoring tools (Prometheus, Grafana) is essential. You will drive automation initiatives and improve deployment efficiency.',
            ],
            'Security Engineer' => [
                'description' => 'Protect systems and data from cyber threats by implementing security controls and best practices. Conduct vulnerability assessments and respond to security incidents.',
                'detail' => 'We are seeking a Security Engineer to strengthen our cybersecurity posture. You will perform security audits, implement security controls, conduct penetration testing, and respond to incidents. Strong knowledge of network security, application security, encryption, identity management, and compliance frameworks (ISO 27001, SOC 2) is required. Experience with security tools (SIEM, IDS/IPS, WAF) and cloud security is highly valued.',
            ],
            'ML Engineer' => [
                'description' => 'Develop and deploy machine learning models at scale. Build ML infrastructure and pipelines to productionize AI solutions for real-world applications.',
                'detail' => 'Join our AI team to build production-ready machine learning systems. You will develop ML models, create training pipelines, optimize model performance, and deploy models to production. Strong programming skills in Python, experience with ML frameworks (TensorFlow, PyTorch), MLOps tools, and cloud ML services (SageMaker, Vertex AI) are essential. You will collaborate with data scientists to transition models from research to production.',
            ],
            'Mobile Developer' => [
                'description' => 'Create native and cross-platform mobile applications for iOS and Android. Deliver smooth user experiences and integrate with backend services.',
                'detail' => 'We are looking for a Mobile Developer to build innovative mobile applications. You will develop apps using Swift/Kotlin or cross-platform frameworks (React Native, Flutter), implement responsive UI designs, integrate APIs, and optimize app performance. Experience with mobile development best practices, app store deployment, push notifications, and offline functionality is required. You will also participate in code reviews and maintain app quality.',
            ],
            'IT Project Manager' => [
                'description' => 'Lead technology projects from conception to delivery, ensuring timely completion within budget. Coordinate between technical teams and business stakeholders to achieve project goals.',
                'detail' => 'We are seeking an experienced IT Project Manager to oversee multiple technology initiatives. You will be responsible for project planning, resource allocation, risk management, stakeholder communication, and sprint planning. Strong knowledge of Agile/Scrum methodologies, project management tools (JIRA, MS Project), and technical understanding is required. Excellent leadership and communication skills are essential for success in this role.',
            ],
            'QA Engineer' => [
                'description' => 'Ensure software quality through comprehensive testing strategies. Design automated test suites and conduct manual testing to identify bugs before release.',
                'detail' => 'Join our QA team to maintain high software quality standards. You will create test plans, develop automated tests using Selenium/Cypress, perform functional and regression testing, and track bugs. Experience with testing frameworks, API testing (Postman), performance testing, and CI/CD integration is essential. You will work closely with developers to ensure comprehensive test coverage and reliable releases.',
            ],
        ];
        
        // 1. Seed Job Categories (All IT-Related)
        $categories = [
            'Software Development',
            'Data & Analytics',
            'Cloud & Infrastructure',
            'Cybersecurity',
            'AI & Machine Learning',
            'Mobile Development',
            'DevOps & Engineering',
            'Product & Project Management',
            'UI/UX Design',
            'Quality Assurance'
        ];
        
        foreach ($categories as $index => $category) {
            DB::table('job_categories')->insert([
                'name' => $category,
                'sort_order' => $index + 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 2. Seed Job Types (All IT-Related)
        $jobTypes = [
            ['Full Stack Developer', 'Software Development'],
            ['Backend Engineer', 'Software Development'],
            ['Frontend Developer', 'Software Development'],
            ['Data Scientist', 'Data & Analytics'],
            ['Data Engineer', 'Data & Analytics'],
            ['Cloud Architect', 'Cloud & Infrastructure'],
            ['DevOps Engineer', 'DevOps & Engineering'],
            ['Security Engineer', 'Cybersecurity'],
            ['ML Engineer', 'AI & Machine Learning'],
            ['Mobile Developer', 'Mobile Development'],
            ['IT Project Manager', 'Product & Project Management'],
            ['QA Engineer', 'Quality Assurance'],
        ];
        
        foreach ($jobTypes as $index => $type) {
            $category = DB::table('job_categories')->where('name', $type[1])->first();
            if ($category) {
                DB::table('job_types')->insert([
                    'name' => $type[0],
                    'job_category_id' => $category->id,
                    'sort_order' => $index + 1,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // 3. Seed Personalities (IT-Focused)
        $personalities = [
            'Analytical Thinker', 'Team Collaborator', 'Innovation-Driven', 'Detail-Oriented',
            'Problem Solver', 'Tech Enthusiast', 'Agile Mindset', 'Continuous Learner',
            'Results-Oriented', 'Strategic Thinker', 'Communication Expert', 'Self-Starter',
            'Quality Focused', 'Mentor', 'Customer-Centric'
        ];
        
        foreach ($personalities as $personality) {
            DB::table('personalities')->insert([
                'name' => $personality,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 4. Seed Practical Skills (IT-Specific)
        $skills = [
            'Python Programming', 'Java Development', 'JavaScript/TypeScript', 'SQL & Database Design',
            'API Development', 'Cloud Computing (AWS/Azure/GCP)', 'Docker & Kubernetes', 'Git Version Control',
            'Agile/Scrum Methodology', 'CI/CD Pipeline', 'System Architecture', 'Microservices',
            'RESTful APIs', 'React/Vue/Angular', 'Node.js', 'Machine Learning', 
            'Data Visualization', 'Security Best Practices', 'Performance Optimization', 'Technical Documentation'
        ];
        
        foreach ($skills as $skill) {
            DB::table('practical_skills')->insert([
                'name' => $skill,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 5. Seed Basic Abilities (IT-Focused)
        $abilities = [
            'Algorithmic Thinking', 'System Design', 'Code Review',
            'Debugging & Troubleshooting', 'Technical Communication', 'Architecture Planning',
            'Performance Tuning', 'Security Analysis', 'Data Modeling',
            'Problem Decomposition', 'Technical Research', 'Automation',
            'Testing Strategy', 'Documentation', 'Collaboration'
        ];
        
        foreach ($abilities as $ability) {
            DB::table('basic_abilities')->insert([
                'name' => $ability,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 6. Seed Affiliates (IT Tools, Qualifications, Career Paths)
        $affiliates = [
            // Tools (type 1)
            ['VS Code / IntelliJ', 1],
            ['GitHub / GitLab', 1],
            ['JIRA / Confluence', 1],
            ['Docker / Kubernetes', 1],
            ['AWS / Azure / GCP', 1],
            ['Terraform', 1],
            ['Jenkins / CircleCI', 1],
            ['Postman / Swagger', 1],
            ['Tableau / Power BI', 1],
            ['Slack / MS Teams', 1],
            // Qualifications (type 2)
            ['Computer Science Degree', 2],
            ['AWS Certified Solutions Architect', 2],
            ['Certified Kubernetes Administrator', 2],
            ['PMP Certification', 2],
            ['CISSP Security Certification', 2],
            ['Google Cloud Professional', 2],
            ['Scrum Master Certification', 2],
            ['Microsoft Azure Certification', 2],
            ['CompTIA Security+', 2],
            ['Oracle Certified Professional', 2],
            // Career Paths (type 3)
            ['Senior Engineer / Tech Lead', 3],
            ['Engineering Manager', 3],
            ['Solution Architect', 3],
            ['Principal Engineer', 3],
            ['CTO / VP Engineering', 3],
            ['DevOps Specialist', 3],
            ['Technical Consultant', 3],
            ['Startup Founder', 3],
            ['Staff Engineer', 3],
            ['Engineering Director', 3],
        ];
        
        foreach ($affiliates as $affiliate) {
            DB::table('affiliates')->insert([
                'name' => $affiliate[0],
                'type' => $affiliate[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 7. Seed Jobs
        $allJobTypes = DB::table('job_types')->get();
        $allPersonalities = DB::table('personalities')->pluck('id')->toArray();
        $allSkills = DB::table('practical_skills')->pluck('id')->toArray();
        $allAbilities = DB::table('basic_abilities')->pluck('id')->toArray();
        $tools = DB::table('affiliates')->where('type', 1)->pluck('id')->toArray();
        $qualifications = DB::table('affiliates')->where('type', 2)->pluck('id')->toArray();
        $careerPaths = DB::table('affiliates')->where('type', 3)->pluck('id')->toArray();
        
        foreach ($allJobTypes as $jobType) {
            // Create 20 jobs per job type for better search coverage
            for ($i = 1; $i <= 20; $i++) {
                // All jobs are now IT-related with Malaysian locations
                $location = $faker->randomElement($malaysianLocations);
                
                $description = isset($itJobDescriptions[$jobType->name])
                    ? $itJobDescriptions[$jobType->name]['description']
                    : 'Exciting IT opportunity in ' . $jobType->name . '. Join our dynamic tech team to work on innovative projects using cutting-edge technologies.';
                
                $detail = isset($itJobDescriptions[$jobType->name])
                    ? $itJobDescriptions[$jobType->name]['detail']
                    : 'We are looking for a talented ' . $jobType->name . ' to join our team in Malaysia. This role offers the opportunity to work with modern technologies, collaborate with experienced professionals, and grow your career in a supportive environment. The ideal candidate will have a passion for technology, strong problem-solving skills, and the ability to work effectively in a team.';
                
                $jobId = DB::table('jobs')->insertGetId([
                    'name' => $jobType->name . ' - ' . $faker->company,
                    'media_id' => $faker->numberBetween(1, 5),
                    'job_category_id' => $jobType->job_category_id,
                    'job_type_id' => $jobType->id,
                    'description' => $description,
                    'detail' => $detail,
                    'business_skill' => implode(', ', $faker->words(5)),
                    'knowledge' => implode(', ', $faker->words(5)),
                    'location' => $location,
                    'activity' => $faker->sentence(10),
                    'academic_degree_doctor' => $faker->boolean(10),
                    'academic_degree_master' => $faker->boolean(20),
                    'academic_degree_professional' => $faker->boolean(30),
                    'academic_degree_bachelor' => $faker->boolean(60),
                    'salary_statistic_group' => 'Group ' . $faker->numberBetween(1, 5),
                    'salary_range_first_year' => 'RM ' . $faker->numberBetween(36, 72) . 'k - RM ' . $faker->numberBetween(72, 108) . 'k',
                    'salary_range_average' => 'RM ' . $faker->numberBetween(60, 180) . 'k per year',
                    'salary_range_remarks' => 'Salary depends on experience and qualifications',
                    'restriction' => $faker->sentence,
                    'estimated_total_workers' => $faker->numberBetween(1000, 100000),
                    'remarks' => $faker->sentence,
                    'url' => $faker->url,
                    'seo_description' => $faker->sentence,
                    'seo_keywords' => implode(',', $faker->words(5)),
                    'sort_order' => (($jobType->id - 1) * 20) + $i,
                    'publish_status' => 1,
                    'version' => 1,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Attach relationships
                // Personalities (2-4 per job)
                $selectedPersonalities = $faker->randomElements($allPersonalities, $faker->numberBetween(2, 4));
                foreach ($selectedPersonalities as $personalityId) {
                    DB::table('jobs_personalities')->insert([
                        'job_id' => $jobId,
                        'personality_id' => $personalityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Practical Skills (2-4 per job)
                $selectedSkills = $faker->randomElements($allSkills, $faker->numberBetween(2, 4));
                foreach ($selectedSkills as $skillId) {
                    DB::table('jobs_practical_skills')->insert([
                        'job_id' => $jobId,
                        'practical_skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Basic Abilities (2-4 per job)
                $selectedAbilities = $faker->randomElements($allAbilities, $faker->numberBetween(2, 4));
                foreach ($selectedAbilities as $abilityId) {
                    DB::table('jobs_basic_abilities')->insert([
                        'job_id' => $jobId,
                        'basic_ability_id' => $abilityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Tools (1-2 per job)
                $selectedTools = $faker->randomElements($tools, $faker->numberBetween(1, 2));
                foreach ($selectedTools as $toolId) {
                    DB::table('jobs_tools')->insert([
                        'job_id' => $jobId,
                        'affiliate_id' => $toolId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Career Paths (1-2 per job)
                $selectedPaths = $faker->randomElements($careerPaths, $faker->numberBetween(1, 2));
                foreach ($selectedPaths as $pathId) {
                    DB::table('jobs_career_paths')->insert([
                        'job_id' => $jobId,
                        'affiliate_id' => $pathId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Recommended Qualifications (1-2 per job)
                $selectedRecQuals = $faker->randomElements($qualifications, $faker->numberBetween(1, 2));
                foreach ($selectedRecQuals as $qualId) {
                    DB::table('jobs_rec_qualifications')->insert([
                        'job_id' => $jobId,
                        'affiliate_id' => $qualId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Required Qualifications (0-2 per job)
                $selectedReqQuals = $faker->randomElements($qualifications, $faker->numberBetween(0, 2));
                foreach ($selectedReqQuals as $qualId) {
                    DB::table('jobs_req_qualifications')->insert([
                        'job_id' => $jobId,
                        'affiliate_id' => $qualId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
