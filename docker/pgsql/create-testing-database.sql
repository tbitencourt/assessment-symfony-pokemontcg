SELECT 'CREATE DATABASE pokemontcg_test'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'pokemontcg_test')\gexec
