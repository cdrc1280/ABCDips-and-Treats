<?php

namespace Tests\Feature\Shared;

use Illuminate\Support\ConfigurationUrlParser;
use Tests\TestCase;

/** Vercel's PHP runtime has no SNI-capable libpq, so Neon URLs carry the endpoint ID in the password. */
class NeonDatabaseUrlTest extends TestCase
{
    /** @return array<string, mixed> */
    private function pgsqlFor(string $url): array
    {
        $saved = [$_ENV, $_SERVER];
        unset($_ENV['DB_URL'], $_SERVER['DB_URL']);
        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = $url;
        putenv('DB_URL');
        putenv("DATABASE_URL={$url}");

        try {
            $config = (require config_path('database.php'))['connections']['pgsql'];
        } finally {
            [$_ENV, $_SERVER] = $saved;
            putenv('DATABASE_URL');
        }

        return (new ConfigurationUrlParser)->parseConfiguration($config);
    }

    public function test_neon_pooler_url_gets_the_endpoint_id_in_the_password(): void
    {
        $config = $this->pgsqlFor('postgresql://neondb_owner:s3cr%40t@ep-restless-silence-bd71my60-pooler.c-8.us-east-2.aws.neon.tech/neondb?sslmode=require');

        $this->assertSame('endpoint=ep-restless-silence-bd71my60;s3cr@t', $config['password']);
        $this->assertSame('neondb_owner', $config['username']);
        $this->assertSame('ep-restless-silence-bd71my60-pooler.c-8.us-east-2.aws.neon.tech', $config['host']);
        $this->assertSame('neondb', $config['database']);
    }

    public function test_other_postgres_urls_are_left_alone(): void
    {
        $this->assertSame('plain', $this->pgsqlFor('postgresql://app:plain@db.example.com:5432/shop')['password']);
        $this->assertSame('endpoint=ep-x;pw', $this->pgsqlFor('postgresql://u:endpoint%3Dep-x%3Bpw@ep-x.aws.neon.tech/db')['password']);
    }
}
