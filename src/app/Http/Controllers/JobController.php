<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * OPTIMIZED Search - Uses WHERE EXISTS subqueries (FAST)
     * Eliminates cartesian product and GROUP BY overhead
     */
    public function searchOptimized(Request $request)
    {
        $keyword = $request->input('keyword', 'Flight');
        $startTime = microtime(true);
        
        $jobs = DB::select("
            SELECT 
                Jobs.id, Jobs.name, Jobs.description, Jobs.location,
                Jobs.salary_range_average, Jobs.publish_status,
                JobCategories.name AS category_name,
                JobTypes.name AS job_type_name
            FROM jobs Jobs
            INNER JOIN job_categories JobCategories 
                ON JobCategories.id = Jobs.job_category_id 
                AND JobCategories.deleted IS NULL
            INNER JOIN job_types JobTypes 
                ON JobTypes.id = Jobs.job_type_id 
                AND JobTypes.deleted IS NULL
            WHERE Jobs.publish_status = 1 
                AND Jobs.deleted IS NULL
                AND (
                    Jobs.name LIKE ?
                    OR Jobs.description LIKE ?
                    OR Jobs.detail LIKE ?
                    OR Jobs.business_skill LIKE ?
                    OR Jobs.knowledge LIKE ?
                    OR Jobs.location LIKE ?
                    OR Jobs.activity LIKE ?
                    OR Jobs.salary_statistic_group LIKE ?
                    OR Jobs.salary_range_remarks LIKE ?
                    OR Jobs.restriction LIKE ?
                    OR Jobs.remarks LIKE ?
                    OR JobCategories.name LIKE ?
                    OR JobTypes.name LIKE ?
                    
                    OR EXISTS (
                        SELECT 1 FROM jobs_personalities jp
                        INNER JOIN personalities p ON p.id = jp.personality_id
                        WHERE jp.job_id = Jobs.id 
                            AND p.deleted IS NULL
                            AND p.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_practical_skills jps
                        INNER JOIN practical_skills ps ON ps.id = jps.practical_skill_id
                        WHERE jps.job_id = Jobs.id 
                            AND ps.deleted IS NULL
                            AND ps.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_basic_abilities jba
                        INNER JOIN basic_abilities ba ON ba.id = jba.basic_ability_id
                        WHERE jba.job_id = Jobs.id 
                            AND ba.deleted IS NULL
                            AND ba.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_tools jt
                        INNER JOIN affiliates t ON t.id = jt.affiliate_id
                        WHERE jt.job_id = Jobs.id 
                            AND t.type = 1
                            AND t.deleted IS NULL
                            AND t.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_career_paths jcp
                        INNER JOIN affiliates cp ON cp.id = jcp.affiliate_id
                        WHERE jcp.job_id = Jobs.id 
                            AND cp.type = 3
                            AND cp.deleted IS NULL
                            AND cp.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_rec_qualifications jrq
                        INNER JOIN affiliates rq ON rq.id = jrq.affiliate_id
                        WHERE jrq.job_id = Jobs.id 
                            AND rq.type = 2
                            AND rq.deleted IS NULL
                            AND rq.name LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1 FROM jobs_req_qualifications jrq
                        INNER JOIN affiliates rq ON rq.id = jrq.affiliate_id
                        WHERE jrq.job_id = Jobs.id 
                            AND rq.type = 2
                            AND rq.deleted IS NULL
                            AND rq.name LIKE ?
                    )
                )
            ORDER BY Jobs.sort_order DESC, Jobs.id DESC
            LIMIT 50
        ", array_fill(0, 20, "%{$keyword}%"));
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        return response()->json([
            'method' => 'OPTIMIZED (EXISTS Subqueries)',
            'execution_time_ms' => $executionTime,
            'result_count' => count($jobs),
            'keyword' => $keyword,
            'data' => $jobs
        ]);
    }
    
    /**
     * SLOW Search - Original query with LEFT JOINs and GROUP BY (SLOW)
     */
    public function searchSlow(Request $request)
    {
        $keyword = $request->input('keyword', 'Flight');
        $startTime = microtime(true);
        
        $jobs = DB::select("
            SELECT 
                Jobs.id, Jobs.name, Jobs.description, Jobs.location,
                Jobs.salary_range_average, Jobs.publish_status,
                JobCategories.name AS category_name,
                JobTypes.name AS job_type_name
            FROM jobs Jobs
            LEFT JOIN jobs_personalities JobsPersonalities
                ON Jobs.id = JobsPersonalities.job_id
            LEFT JOIN personalities Personalities
                ON Personalities.id = JobsPersonalities.personality_id
                AND Personalities.deleted IS NULL
            LEFT JOIN jobs_practical_skills JobsPracticalSkills
                ON Jobs.id = JobsPracticalSkills.job_id
            LEFT JOIN practical_skills PracticalSkills
                ON PracticalSkills.id = JobsPracticalSkills.practical_skill_id
                AND PracticalSkills.deleted IS NULL
            LEFT JOIN jobs_basic_abilities JobsBasicAbilities
                ON Jobs.id = JobsBasicAbilities.job_id
            LEFT JOIN basic_abilities BasicAbilities
                ON BasicAbilities.id = JobsBasicAbilities.basic_ability_id
                AND BasicAbilities.deleted IS NULL
            LEFT JOIN jobs_tools JobsTools
                ON Jobs.id = JobsTools.job_id
            LEFT JOIN affiliates Tools
                ON Tools.type = 1
                AND Tools.id = JobsTools.affiliate_id
                AND Tools.deleted IS NULL
            LEFT JOIN jobs_career_paths JobsCareerPaths
                ON Jobs.id = JobsCareerPaths.job_id
            LEFT JOIN affiliates CareerPaths
                ON CareerPaths.type = 3
                AND CareerPaths.id = JobsCareerPaths.affiliate_id
                AND CareerPaths.deleted IS NULL
            LEFT JOIN jobs_rec_qualifications JobsRecQualifications
                ON Jobs.id = JobsRecQualifications.job_id
            LEFT JOIN affiliates RecQualifications
                ON RecQualifications.type = 2
                AND RecQualifications.id = JobsRecQualifications.affiliate_id
                AND RecQualifications.deleted IS NULL
            LEFT JOIN jobs_req_qualifications JobsReqQualifications
                ON Jobs.id = JobsReqQualifications.job_id
            LEFT JOIN affiliates ReqQualifications
                ON ReqQualifications.type = 2
                AND ReqQualifications.id = JobsReqQualifications.affiliate_id
                AND ReqQualifications.deleted IS NULL
            INNER JOIN job_categories JobCategories
                ON JobCategories.id = Jobs.job_category_id
                AND JobCategories.deleted IS NULL
            INNER JOIN job_types JobTypes
                ON JobTypes.id = Jobs.job_type_id
                AND JobTypes.deleted IS NULL
            WHERE (
                JobCategories.name LIKE ?
                OR JobTypes.name LIKE ?
                OR Jobs.name LIKE ?
                OR Jobs.description LIKE ?
                OR Jobs.detail LIKE ?
                OR Jobs.business_skill LIKE ?
                OR Jobs.knowledge LIKE ?
                OR Jobs.location LIKE ?
                OR Jobs.activity LIKE ?
                OR Jobs.salary_statistic_group LIKE ?
                OR Jobs.salary_range_remarks LIKE ?
                OR Jobs.restriction LIKE ?
                OR Jobs.remarks LIKE ?
                OR Personalities.name LIKE ?
                OR PracticalSkills.name LIKE ?
                OR BasicAbilities.name LIKE ?
                OR Tools.name LIKE ?
                OR CareerPaths.name LIKE ?
                OR RecQualifications.name LIKE ?
                OR ReqQualifications.name LIKE ?
            )
            AND Jobs.publish_status = 1
            AND Jobs.deleted IS NULL
            GROUP BY Jobs.id
            ORDER BY Jobs.sort_order DESC, Jobs.id DESC
            LIMIT 50
        ", array_fill(0, 20, "%{$keyword}%"));
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        return response()->json([
            'method' => 'SLOW (LEFT JOIN + GROUP BY)',
            'execution_time_ms' => $executionTime,
            'result_count' => count($jobs),
            'keyword' => $keyword,
            'note' => 'Creates cartesian product requiring GROUP BY',
            'data' => $jobs
        ]);
    }
    
    /**
     * Compare both methods
     */
    public function compare(Request $request)
    {
        $keyword = $request->input('keyword', 'Flight');
        
        $optimizedStart = microtime(true);
        $optimizedResponse = $this->searchOptimized($request);
        $optimizedTime = round((microtime(true) - $optimizedStart) * 1000, 2);
        
        $slowStart = microtime(true);
        $slowResponse = $this->searchSlow($request);
        $slowTime = round((microtime(true) - $slowStart) * 1000, 2);
        
        $improvement = round((($slowTime - $optimizedTime) / $slowTime) * 100, 2);
        
        return response()->json([
            'keyword' => $keyword,
            'optimized_time_ms' => $optimizedTime,
            'slow_time_ms' => $slowTime,
            'improvement_percentage' => $improvement . '%',
            'speedup_factor' => round($slowTime / $optimizedTime, 2) . 'x faster'
        ]);
    }
}

