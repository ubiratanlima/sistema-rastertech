-- SUPABASE ENTERPRISE ROLES SETUP (IDEMPOTENT)
-- Este arquivo verifica se os usuários já existem antes de criar.

DO $$
BEGIN
    -- 1. Usuário supabase_admin (Mestre)
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'supabase_admin') THEN
        CREATE USER supabase_admin WITH SUPERUSER CREATEDB CREATEROLE REPLICATION PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER supabase_admin WITH SUPERUSER CREATEDB CREATEROLE REPLICATION PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- 2. Usuário supabase_auth_admin (Auth)
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'supabase_auth_admin') THEN
        CREATE USER supabase_auth_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER supabase_auth_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- 3. Usuário supabase_storage_admin (Storage)
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'supabase_storage_admin') THEN
        CREATE USER supabase_storage_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER supabase_storage_admin WITH CREATEROLE PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;

    -- 4. Usuário authenticator (PostgREST)
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'authenticator') THEN
        CREATE USER authenticator WITH NOINHERIT LOGIN PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    ELSE
        ALTER USER authenticator WITH NOINHERIT LOGIN PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
    END IF;
END
$$;

-- 2. Esquemas base (Agora com permissões garantidas)
CREATE SCHEMA IF NOT EXISTS auth AUTHORIZATION supabase_admin;
CREATE SCHEMA IF NOT EXISTS storage AUTHORIZATION supabase_admin;
CREATE SCHEMA IF NOT EXISTS _realtime AUTHORIZATION supabase_admin;
CREATE SCHEMA IF NOT EXISTS _supabase AUTHORIZATION supabase_admin;
CREATE SCHEMA IF NOT EXISTS _analytics AUTHORIZATION supabase_admin;

-- 3. Roles anônimas para o REST (PostgREST)
DO $$
BEGIN
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

GRANT usage ON SCHEMA public TO anon, authenticated, service_role;
GRANT ALL ON ALL TABLES IN SCHEMA public TO anon, authenticated, service_role;
GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO anon, authenticated, service_role;
GRANT ALL ON ALL FUNCTIONS IN SCHEMA public TO anon, authenticated, service_role;

ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO anon, authenticated, service_role;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO anon, authenticated, service_role;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO anon, authenticated, service_role;
