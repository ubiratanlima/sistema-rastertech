-- 🧬 SETUP DE INFRAESTRUTURA RASTERTECH (PRODUÇÃO)

-- 1. Criação de Usuários e Permissões do Sistema
CREATE USER supabase_auth_admin WITH PASSWORD 'RastertechMasterInternal_123';
ALTER USER supabase_auth_admin WITH SUPERUSER;

CREATE USER authenticator WITH PASSWORD 'RastertechMasterInternal_123';
ALTER USER authenticator WITH SUPERUSER;

CREATE USER supabase_admin WITH PASSWORD '${DB_PASSWORD}';
ALTER USER supabase_admin WITH SUPERUSER;

CREATE USER anon;
CREATE USER authenticated;
CREATE ROLE service_role;

-- 2. Regras de Compatibilidade (ECO-PATCH PARA SUPABASE AUTH NO POSTGRES 15)
-- Permite que o GoTrue rode migrações legadas que comparam UUID com TEXT de forma implícita.
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_cast c JOIN pg_type s ON s.oid = c.castsource JOIN pg_type t ON t.oid = c.casttarget WHERE s.typname = 'uuid' AND t.typname = 'text') THEN
        CREATE CAST (uuid AS text) WITH INOUT AS IMPLICIT;
    END IF;
END $$;

-- 2. Garantir permissões no esquema PUBLIC (CRÍTICO PARA POSTGRES 15+)
GRANT ALL ON SCHEMA public TO supabase_auth_admin;
GRANT ALL ON SCHEMA public TO authenticator;
GRANT ALL ON SCHEMA public TO postgres;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO supabase_auth_admin;

-- 3. Criação de Esquemas Necessários
CREATE SCHEMA IF NOT EXISTS auth;
CREATE SCHEMA IF NOT EXISTS extensions;
CREATE SCHEMA IF NOT EXISTS storage;

ALTER SCHEMA auth OWNER TO supabase_auth_admin;
ALTER SCHEMA extensions OWNER TO supabase_auth_admin;
ALTER SCHEMA storage OWNER TO supabase_auth_admin;

-- 4. Extensões de Segurança e Monitoramento
CREATE EXTENSION IF NOT EXISTS "uuid-ossp" SCHEMA extensions;
CREATE EXTENSION IF NOT EXISTS "pgcrypto" SCHEMA extensions;

-- 5. Tabelas de Infraestrutura (DUMMY PARA PREVENIR ERROS DE MIGRATION)
CREATE TABLE IF NOT EXISTS public._infra_status (
    id SERIAL PRIMARY KEY,
    status TEXT DEFAULT 'ready',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Finalizado.
