<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('media_id')->nullable();
            $table->unsignedBigInteger('job_category_id');
            $table->unsignedBigInteger('job_type_id');
            $table->text('description')->nullable();
            $table->text('detail')->nullable();
            $table->text('business_skill')->nullable();
            $table->text('knowledge')->nullable();
            $table->text('location')->nullable();
            $table->text('activity')->nullable();
            $table->boolean('academic_degree_doctor')->default(false);
            $table->boolean('academic_degree_master')->default(false);
            $table->boolean('academic_degree_professional')->default(false);
            $table->boolean('academic_degree_bachelor')->default(false);
            $table->string('salary_statistic_group')->nullable();
            $table->string('salary_range_first_year')->nullable();
            $table->string('salary_range_average')->nullable();
            $table->text('salary_range_remarks')->nullable();
            $table->text('restriction')->nullable();
            $table->integer('estimated_total_workers')->nullable();
            $table->text('remarks')->nullable();
            $table->string('url')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('publish_status')->default(0);
            $table->integer('version')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->timestamp('deleted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
