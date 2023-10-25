<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 'sub' column if it doesn't exist
        if (!Schema::hasColumn('users', 'sub')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('sub')->after('id')
                    ->unique()
                    ->nullable()
                    ->default(null);
            });
        }

        // Remove unique constraint from 'email' column if it exists
        if (Schema::hasColumn('users', 'email') && Schema::getConnection()->getDoctrineColumn('users', 'email')->getUnique()) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('email');
            });
        }

        // Add 'api_token' column if it doesn't exist
        if (!Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('api_token', 80)->after('email')
                    ->unique()
                    ->nullable()
                    ->default(null);
            });
        }

        // Drop 'email_verified_at' column if it exists
        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }

        // Drop 'password' column if it exists
        if (Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sub');
            $table->dropColumn('api_token');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->after('email_verified_at');
        });
    }
}