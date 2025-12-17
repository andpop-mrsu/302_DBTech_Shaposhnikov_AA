<?php
require_once 'db.php';

$pdo = getPDO();

// Создание таблиц, если они не существуют
$pdo->exec("
    CREATE TABLE IF NOT EXISTS doctors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        last_name TEXT NOT NULL,
        first_name TEXT NOT NULL,
        middle_name TEXT,
        specialization TEXT NOT NULL,
        phone TEXT,
        email TEXT
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS schedules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        day_of_week INTEGER NOT NULL,
        start_time TEXT NOT NULL,
        end_time TEXT NOT NULL,
        office TEXT,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        service_name TEXT NOT NULL,
        service_date DATE NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        patient_name TEXT NOT NULL,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )
");

echo "Таблицы созданы успешно!<br>";

// Очистка таблиц (в правильном порядке из-за внешних ключей)
$pdo->exec("DELETE FROM services");
$pdo->exec("DELETE FROM schedules");
$pdo->exec("DELETE FROM doctors");

echo "Старые данные удалены!<br>";

// Добавление врачей
$doctors = [
    ['Иванов', 'Александр', 'Петрович', 'Терапевт', '+7 (495) 123-45-67', 'ivanov@clinic.ru'],
    ['Смирнова', 'Елена', 'Владимировна', 'Хирург', '+7 (495) 234-56-78', 'smirnova@clinic.ru'],
    ['Петров', 'Дмитрий', 'Сергеевич', 'Кардиолог', '+7 (495) 345-67-89', 'petrov@clinic.ru'],
    ['Козлова', 'Ольга', 'Игоревна', 'Невролог', '+7 (495) 456-78-90', 'kozlov@clinic.ru'],
    ['Сидоров', 'Михаил', 'Александрович', 'Офтальмолог', '+7 (495) 567-89-01', 'sidorov@clinic.ru'],
    ['Морозова', 'Анна', 'Дмитриевна', 'Стоматолог', '+7 (495) 678-90-12', 'morozova@clinic.ru'],
    ['Николаев', 'Сергей', 'Викторович', 'Отоларинголог', '+7 (495) 789-01-23', 'nikolaev@clinic.ru'],
    ['Федорова', 'Мария', 'Павловна', 'Гинеколог', '+7 (495) 890-12-34', 'fedorova@clinic.ru'],
    ['Волков', 'Андрей', 'Николаевич', 'Уролог', '+7 (495) 901-23-45', 'volkov@clinic.ru'],
    ['Алексеева', 'Татьяна', 'Олеговна', 'Эндокринолог', '+7 (495) 012-34-56', 'alekseeva@clinic.ru']
];

$stmt = $pdo->prepare("INSERT INTO doctors (last_name, first_name, middle_name, specialization, phone, email) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($doctors as $doctor) {
    $stmt->execute($doctor);
}

echo "Врачи добавлены успешно!<br>";

// Получаем ID добавленных врачей
$doctorIds = $pdo->query("SELECT id FROM doctors ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

// Проверяем, что врачи добавлены
if (empty($doctorIds)) {
    die("Ошибка: врачи не были добавлены!");
}

echo "ID врачей: " . implode(', ', $doctorIds) . "<br>";

// Добавление графика работы
$schedules = [
    // Врач 1 - Терапевт
    [$doctorIds[0], 1, '08:00', '14:00', '101'],
    [$doctorIds[0], 2, '08:00', '14:00', '101'],
    [$doctorIds[0], 3, '08:00', '14:00', '101'],
    [$doctorIds[0], 4, '08:00', '14:00', '101'],
    [$doctorIds[0], 5, '08:00', '14:00', '101'],
    
    // Врач 2 - Хирург
    [$doctorIds[1], 1, '09:00', '15:00', '205'],
    [$doctorIds[1], 2, '09:00', '15:00', '205'],
    [$doctorIds[1], 3, '09:00', '15:00', '205'],
    [$doctorIds[1], 4, '09:00', '15:00', '205'],
    [$doctorIds[1], 5, '09:00', '15:00', '205'],
    
    // Врач 3 - Кардиолог
    [$doctorIds[2], 1, '10:00', '16:00', '315'],
    [$doctorIds[2], 3, '10:00', '16:00', '315'],
    [$doctorIds[2], 5, '10:00', '16:00', '315'],
    
    // Врач 4 - Невролог
    [$doctorIds[3], 2, '11:00', '17:00', '412'],
    [$doctorIds[3], 4, '11:00', '17:00', '412'],
    [$doctorIds[3], 6, '10:00', '14:00', '412'],
    
    // Врач 5 - Офтальмолог
    [$doctorIds[4], 1, '08:00', '13:00', '208'],
    [$doctorIds[4], 2, '08:00', '13:00', '208'],
    [$doctorIds[4], 3, '08:00', '13:00', '208'],
    [$doctorIds[4], 4, '08:00', '13:00', '208'],
    [$doctorIds[4], 5, '08:00', '13:00', '208'],
    
    // Врач 6 - Стоматолог
    [$doctorIds[5], 1, '14:00', '20:00', '106'],
    [$doctorIds[5], 2, '14:00', '20:00', '106'],
    [$doctorIds[5], 3, '14:00', '20:00', '106'],
    [$doctorIds[5], 4, '14:00', '20:00', '106'],
    [$doctorIds[5], 5, '14:00', '20:00', '106'],
    
    // Врач 7 - ЛОР
    [$doctorIds[6], 1, '08:00', '12:00', '307'],
    [$doctorIds[6], 3, '08:00', '12:00', '307'],
    [$doctorIds[6], 5, '08:00', '12:00', '307'],
    
    // Врач 8 - Гинеколог
    [$doctorIds[7], 2, '09:00', '18:00', '214'],
    [$doctorIds[7], 4, '09:00', '18:00', '214'],
    
    // Врач 9 - Уролог
    [$doctorIds[8], 1, '10:00', '19:00', '319'],
    [$doctorIds[8], 3, '10:00', '19:00', '319'],
    [$doctorIds[8], 5, '10:00', '19:00', '319'],
    
    // Врач 10 - Эндокринолог
    [$doctorIds[9], 2, '08:00', '15:00', '105'],
    [$doctorIds[9], 4, '08:00', '15:00', '105'],
    [$doctorIds[9], 6, '09:00', '13:00', '105']
];

$stmt = $pdo->prepare("INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time, office) VALUES (?, ?, ?, ?, ?)");

$scheduleCount = 0;
foreach ($schedules as $schedule) {
    $stmt->execute($schedule);
    $scheduleCount++;
}

echo "График работы добавлен ($scheduleCount записей)!<br>";

// Добавление оказанных услуг
$services = [
    // Услуги врача 1 - Терапевт
    [$doctorIds[0], 'Первичный осмотр терапевта', '2024-01-15', 1500.00, 'Семенов А.В.'],
    [$doctorIds[0], 'Повторный осмотр терапевта', '2024-01-20', 1200.00, 'Семенов А.В.'],
    [$doctorIds[0], 'ЭКГ', '2024-01-25', 800.00, 'Ковалева И.С.'],
    [$doctorIds[0], 'Выписка больничного', '2024-01-28', 500.00, 'Семенов А.В.'],
    
    // Услуги врача 2 - Хирург
    [$doctorIds[1], 'Консультация хирурга', '2024-01-10', 2000.00, 'Попов Д.М.'],
    [$doctorIds[1], 'Удаление родинки', '2024-01-12', 5000.00, 'Попов Д.М.'],
    [$doctorIds[1], 'Перевязка', '2024-01-14', 800.00, 'Попов Д.М.'],
    [$doctorIds[1], 'Снятие швов', '2024-01-28', 1000.00, 'Попов Д.М.'],
    
    // Услуги врача 3 - Кардиолог
    [$doctorIds[2], 'Консультация кардиолога', '2024-01-05', 2500.00, 'Орлова Е.П.'],
    [$doctorIds[2], 'Эхокардиография', '2024-01-08', 3500.00, 'Орлова Е.П.'],
    [$doctorIds[2], 'Суточный мониторинг ЭКГ', '2024-01-20', 4500.00, 'Кузнецов В.А.'],
    [$doctorIds[2], 'Расшифровка ЭКГ', '2024-01-28', 1500.00, 'Кузнецов В.А.'],
    
    // Услуги врача 4 - Невролог
    [$doctorIds[3], 'Консультация невролога', '2024-01-18', 2200.00, 'Тихомиров С.Н.'],
    [$doctorIds[3], 'Электронейромиография', '2024-01-22', 5200.00, 'Тихомиров С.Н.'],
    [$doctorIds[3], 'Назначение лечения', '2024-01-29', 1800.00, 'Тихомиров С.Н.'],
    
    // Услуги врача 5 - Офтальмолог
    [$doctorIds[4], 'Консультация офтальмолога', '2024-01-03', 1800.00, 'Белова М.К.'],
    [$doctorIds[4], 'Проверка зрения', '2024-01-03', 900.00, 'Белова М.К.'],
    [$doctorIds[4], 'Подбор очков', '2024-01-10', 1200.00, 'Белова М.К.'],
    [$doctorIds[4], 'Контрольный осмотр', '2024-01-29', 1000.00, 'Белова М.К.'],
    
    // Услуги врача 6 - Стоматолог
    [$doctorIds[5], 'Консультация стоматолога', '2024-01-11', 1000.00, 'Григорьев П.Л.'],
    [$doctorIds[5], 'Лечение кариеса', '2024-01-11', 4000.00, 'Григорьев П.Л.'],
    [$doctorIds[5], 'Профессиональная чистка', '2024-01-25', 3500.00, 'Соколова А.Д.'],
    
    // Услуги врача 7 - ЛОР
    [$doctorIds[6], 'Консультация ЛОРа', '2024-01-07', 1600.00, 'Михайлов И.Б.'],
    [$doctorIds[6], 'Промывание миндалин', '2024-01-07', 1200.00, 'Михайлов И.Б.'],
    
    // Услуги врача 8 - Гинеколог
    [$doctorIds[7], 'Консультация гинеколога', '2024-01-14', 1900.00, 'Захарова О.В.'],
    [$doctorIds[7], 'УЗИ органов малого таза', '2024-01-14', 2800.00, 'Захарова О.В.'],
    
    // Услуги врача 9 - Уролог
    [$doctorIds[8], 'Консультация уролога', '2024-01-21', 2100.00, 'Крылов Н.С.'],
    [$doctorIds[8], 'УЗИ мочевого пузыря', '2024-01-21', 2300.00, 'Крылов Н.С.'],
    
    // Услуги врача 10 - Эндокринолог
    [$doctorIds[9], 'Консультация эндокринолога', '2024-01-09', 2400.00, 'Фролова Т.М.'],
    [$doctorIds[9], 'Анализ на гормоны', '2024-01-09', 3200.00, 'Фролова Т.М.'],
    [$doctorIds[9], 'УЗИ щитовидной железы', '2024-01-16', 2700.00, 'Фролова Т.М.']
];

$stmt = $pdo->prepare("INSERT INTO services (doctor_id, service_name, service_date, price, patient_name) VALUES (?, ?, ?, ?, ?)");

$serviceCount = 0;
foreach ($services as $service) {
    $stmt->execute($service);
    $serviceCount++;
}

echo "Оказанные услуги добавлены ($serviceCount записей)!<br>";

echo "<h3>Инициализация базы данных завершена успешно!</h3>";
echo "<a href='index.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #000; color: #fff; text-decoration: none;'>Перейти к списку врачей</a>";
?>