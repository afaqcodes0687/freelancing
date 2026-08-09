<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('banner_title');
            $table->string('content_title');
            $table->text('main_content');
            $table->string('faq_title');
            $table->json('faqs');
            $table->string('side_image')->nullable();
            $table->string('main_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supports');
    }
};
