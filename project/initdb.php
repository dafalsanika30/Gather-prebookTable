-- Step 1: Create database
CREATE DATABASE restaurantdb;

-- Step 2: Connect to it
\c restaurantdb;

-- Step 3: Create dedicated application user
CREATE USER projectuser WITH ENCRYPTED PASSWORD 'projectpass';

-- Step 4: Grant privileges
GRANT ALL PRIVILEGES ON DATABASE restaurantdb TO projectuser;

-- Optional: make sure new tables and sequences are accessible
ALTER DATABASE restaurantdb OWNER TO projectuser;

-- Step 5: Allow schema privileges (useful when running table creation scripts)
GRANT ALL PRIVILEGES ON SCHEMA public TO projectuser;
