#!/usr/bin/env python3
import os
import csv
import sqlite3
from pathlib import Path


def detect_delimiter(file_path):
    """Автоматическое определение разделителя в файле"""
    with open(file_path, 'r', encoding='utf-8') as f:
        first_line = f.readline()
        if '\t' in first_line:
            return '\t'
        elif '|' in first_line:
            return '|'
        elif ',' in first_line:
            return ','
        else:
            return '\t'


def get_table_structure(table_name):
    """Возвращает структуру таблицы согласно требованиям"""
    structures = {
        'movies': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('title', 'TEXT'),
            ('year', 'INTEGER'),
            ('genres', 'TEXT')
        ],
        'ratings': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('user_id', 'INTEGER'),
            ('movie_id', 'INTEGER'),
            ('rating', 'REAL'),
            ('timestamp', 'INTEGER')
        ],
        'tags': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('user_id', 'INTEGER'),
            ('movie_id', 'INTEGER'),
            ('tag', 'TEXT'),
            ('timestamp', 'INTEGER')
        ],
        'users': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('name', 'TEXT'),
            ('email', 'TEXT'),
            ('gender', 'TEXT'),
            ('register_date', 'TEXT'),
            ('occupation', 'TEXT')
        ]
    }
    return structures.get(table_name)


def map_file_to_table(file_path, table_name):
    """Сопоставляет файл с таблицей и возвращает маппинг колонок"""
    # Предопределенные маппинги для известных файлов
    mappings = {
        'movies.csv': {
            'table': 'movies',
            'mapping': {'title': 0, 'year': 1, 'genres': 2}
        },
        'ratings.csv': {
            'table': 'ratings',
            'mapping': {'user_id': 0, 'movie_id': 1, 'rating': 2, 'timestamp': 3}
        },
        'tags.csv': {
            'table': 'tags',
            'mapping': {'user_id': 0, 'movie_id': 1, 'tag': 2, 'timestamp': 3}
        },
        'users.txt': {
            'table': 'users',
            'mapping': {'name': 0, 'email': 1, 'gender': 2, 'register_date': 3, 'occupation': 4}
        }
    }

    filename = file_path.name
    if filename in mappings:
        return mappings[filename]

    # Автоматическое определение для других файлов
    return {'table': table_name, 'mapping': {}}


def read_headers_and_sample(file_path, delimiter):
    """Читает заголовки и несколько строк для анализа"""
    with open(file_path, 'r', encoding='utf-8') as f:
        # Пытаемся прочитать первую строку как заголовок
        first_line = f.readline().strip()
        headers = [header.strip() for header in first_line.split(delimiter)]

        # Читаем несколько строк данных
        sample_data = []
        for _ in range(5):
            line = f.readline().strip()
            if line:
                sample_data.append(line.split(delimiter))

        return headers, sample_data


def generate_create_table(table_name):
    """Генерация SQL команды CREATE TABLE"""
    structure = get_table_structure(table_name)
    if not structure:
        return None

    sql = f"CREATE TABLE {table_name} (\n"
    columns = []
    for column_name, column_type in structure:
        columns.append(f"    {column_name} {column_type}")

    sql += ",\n".join(columns)
    sql += "\n);"
    return sql


def generate_insert_statements(table_name, file_path, mapping, delimiter):
    """Генерация SQL команд INSERT"""
    inserts = []
    table_structure = get_table_structure(table_name)
    if not table_structure:
        return inserts

    with open(file_path, 'r', encoding='utf-8') as f:
        reader = csv.reader(f, delimiter=delimiter)

        # Пропускаем заголовок
        next(reader, None)

        for row in reader:
            if row and any(cell.strip() for cell in row):  # проверяем что строка не пустая
                db_columns = []
                db_values = []

                # Обрабатываем каждую колонку согласно структуре таблицы
                for db_column, db_type in table_structure:
                    if db_column == 'id':
                        continue  # пропускаем id - он автоинкрементный

                    # Получаем индекс колонки в файле
                    col_index = mapping.get(db_column, None)

                    if col_index is not None and col_index < len(row):
                        value = row[col_index].strip()
                    else:
                        value = ''

                    # Обработка значения для SQL
                    if not value:
                        db_values.append('NULL')
                    elif 'INTEGER' in db_type:
                        try:
                            db_values.append(str(int(float(value))))  # преобразуем в integer
                        except (ValueError, TypeError):
                            db_values.append('NULL')
                    elif 'REAL' in db_type:
                        try:
                            db_values.append(str(float(value)))
                        except (ValueError, TypeError):
                            db_values.append('NULL')
                    else:
                        # Экранируем кавычки для текстовых значений
                        escaped_value = value.replace("'", "''")
                        db_values.append(f"'{escaped_value}'")

                    db_columns.append(db_column)

                if db_columns:
                    sql = f"INSERT INTO {table_name} ({', '.join(db_columns)}) VALUES ({', '.join(db_values)});"
                    inserts.append(sql)

    return inserts


def main():
    # Проверяем существование каталога dataset
    dataset_path = Path('dataset')
    if not dataset_path.exists():
        print("Ошибка: каталог 'dataset' не найден!")
        return

    # Собираем список всех текстовых файлов в dataset
    text_files = list(dataset_path.glob('*.txt')) + list(dataset_path.glob('*.csv'))

    if not text_files:
        print("Ошибка: в каталоге 'dataset' не найдены файлы .txt или .csv!")
        return

    print(f"Найдено файлов: {len(text_files)}")
    for file in text_files:
        print(f"  - {file.name}")

    # Создаем SQL скрипт
    sql_script = []
    sql_script.append("-- SQL Script generated by ETL process")
    sql_script.append("BEGIN TRANSACTION;")
    sql_script.append("")

    # Удаляем существующие таблицы (только требуемые)
    sql_script.append("-- Drop existing tables")
    required_tables = ['movies', 'ratings', 'tags', 'users']
    for table in required_tables:
        sql_script.append(f"DROP TABLE IF EXISTS {table};")
    sql_script.append("")

    # Обрабатываем только нужные файлы
    target_files = {
        'movies.csv': 'movies',
        'ratings.csv': 'ratings',
        'tags.csv': 'tags',
        'users.txt': 'users'
    }

    processed_count = 0

    for file_path in text_files:
        filename = file_path.name

        if filename in target_files:
            table_name = target_files[filename]
            print(f"Обрабатываю файл: {filename} -> таблица: {table_name}")

            try:
                # Определяем разделитель
                delimiter = detect_delimiter(file_path)

                # Получаем маппинг для файла
                mapping_info = map_file_to_table(file_path, table_name)
                mapping = mapping_info['mapping']

                # Генерируем CREATE TABLE
                create_sql = generate_create_table(table_name)
                if create_sql:
                    sql_script.append(f"-- Create table {table_name}")
                    sql_script.append(create_sql)
                    sql_script.append("")

                    # Генерируем INSERT statements
                    sql_script.append(f"-- Insert data into {table_name}")
                    inserts = generate_insert_statements(table_name, file_path, mapping, delimiter)
                    sql_script.extend(inserts)
                    sql_script.append("")

                    print(f"  ✅ Обработано записей: {len(inserts)}")
                    processed_count += 1
                else:
                    print(f"  ❌ Не удалось создать таблицу {table_name}")

            except Exception as e:
                print(f"  ❌ Ошибка при обработке файла {filename}: {str(e)}")
                continue
        else:
            print(f"⚠️  Пропускаем файл: {filename} (не требуется для целевых таблиц)")

    sql_script.append("COMMIT;")

    # Записываем SQL скрипт в файл
    with open('db_init.sql', 'w', encoding='utf-8', newline='\n') as f:
        f.write('\n'.join(sql_script))

    print(f"\n✅ SQL скрипт создан: db_init.sql")
    print(f"📊 Обработано таблиц: {processed_count}")
    print(f"📝 Всего команд: {len(sql_script)}")


if __name__ == "__main__":
    main()