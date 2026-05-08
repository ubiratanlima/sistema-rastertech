-- 🧬 SETUP DE INFRAESTRUTURA RASTERTECH (LOCAL)

CREATE USER supabase_auth_admin WITH PASSWORD 'RastertechMasterInternal_123';
ALTER USER supabase_auth_admin WITH SUPERUSER;

CREATE USER authenticator WITH PASSWORD 'RastertechMasterInternal_123';
ALTER USER authenticator WITH SUPERUSER;

CREATE USER supabase_admin WITH PASSWORD '567099a56bbd02ecaaff74dbe4edd2ca';
ALTER USER supabase_admin WITH SUPERUSER;

CREATE USER anon;
CREATE USER authenticated;
CREATE ROLE service_role;

CREATE SCHEMA IF NOT EXISTS auth;
CREATE SCHEMA IF NOT EXISTS extensions;
CREATE SCHEMA IF NOT EXISTS storage;

ALTER SCHEMA auth OWNER TO supabase_auth_admin;
ALTER SCHEMA extensions OWNER TO supabase_auth_admin;
ALTER SCHEMA storage OWNER TO supabase_auth_admin;

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" SCHEMA extensions;
CREATE EXTENSION IF NOT EXISTS "pgcrypto" SCHEMA extensions;
