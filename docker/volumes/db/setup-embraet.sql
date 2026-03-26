-- ==========================================================
-- SUPABASE ENTERPRISE CONSOLIDATED SETUP (EMBRAET)
-- ==========================================================

-- 1. ESQUEMAS BASE
CREATE SCHEMA IF NOT EXISTS auth;
CREATE SCHEMA IF NOT EXISTS storage;
CREATE SCHEMA IF NOT EXISTS _realtime;
CREATE SCHEMA IF NOT EXISTS _supabase;
CREATE SCHEMA IF NOT EXISTS _analytics;

-- 2. USUÁRIOS E ROLES (Lógica Idempotente)
DO $$
BEGIN
    -- Superusuário Embraet
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'supabase_admin') THEN
        CREATE USER supabase_admin WITH SUPERUSER CREATEDB CREATEROLE REPLICATION PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER supabase_admin WITH SUPERUSER CREATEDB CREATEROLE REPLICATION PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- Auth Admin
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'supabase_auth_admin') THEN
        CREATE USER supabase_auth_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER supabase_auth_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- Authenticator (O Coração do REST)
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'authenticator') THEN
        CREATE USER authenticator WITH NOINHERIT LOGIN PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER authenticator WITH NOINHERIT LOGIN PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- Roles de Sistema
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'anon') THEN
        CREATE ROLE anon NOLOGIN;
    END IF;
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'authenticated') THEN
        CREATE ROLE authenticated NOLOGIN;
    END IF;
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'service_role') THEN
        CREATE ROLE service_role NOLOGIN;
    END IF;
END
$$;

-- 3. PERMISSÕES DE ACESSO
GRANT usage ON SCHEMA public TO anon, authenticated, service_role;
GRANT ALL ON ALL TABLES IN SCHEMA public TO anon, authenticated, service_role;
GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO anon, authenticated, service_role;

-- 4. DONOS DOS ESQUEMAS
ALTER SCHEMA auth OWNER TO supabase_admin;
ALTER SCHEMA storage OWNER TO supabase_admin;
ALTER SCHEMA _realtime OWNER TO supabase_admin;
ALTER SCHEMA _supabase OWNER TO supabase_admin;
ALTER SCHEMA _analytics OWNER TO supabase_admin;

-- 5. FINALIZAÇÃO
GRANT supabase_admin TO postgres;
