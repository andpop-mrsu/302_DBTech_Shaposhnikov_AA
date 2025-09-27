#!/bin/bash
echo "Starting ETL process..."
python3 make_db_init.py

if [ $? -ne 0 ]; then
    echo "Error: Python script failed!"
    exit 1
fi

echo "Loading SQL script into database..."
sqlite3 movies_rating.db < db_init.sql

if [ $? -ne 0 ]; then
    echo "Error: SQLite command failed!"
    exit 1
fi

echo "Database created successfully!"
echo "File: movies_rating.db"