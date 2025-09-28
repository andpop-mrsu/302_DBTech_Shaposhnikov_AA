@echo off
echo Starting ETL process...
python make_db_init.py

if %errorlevel% neq 0 (
    echo Error: Python script failed!
    exit /b 1
)

echo Loading SQL script into database...
sqlite3 movies_rating.db < db_init.sql

if %errorlevel% neq 0 (
    echo Error: SQLite command failed!
    exit /b 1
)

echo Database created successfully!
echo File: movies_rating.db
pause