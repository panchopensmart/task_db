<?php

$host = 'db';
$db = 'mydatabase';
$user = 'user';
$password = 'password';

try {
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $password);
    echo "Подключение к базе данных успешно!<br>";
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Импорт заказов
function import_orders(string $file_path, PDO $pdo): void
{
    $invalid_lines = [];
    $file = fopen($file_path, 'r');

    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $parts = explode(';', $line);

            if (count($parts) < 2) {
                $invalid_lines[] = $line;
                continue;
            }

            $item_id = $parts[0];
            $customer_id = $parts[1];
            $comment = $parts[2] ?? '';

            if (empty($item_id) || empty($customer_id)) {
                $invalid_lines[] = $line;
                continue;
            }

            $stmt = $pdo->prepare('SELECT * FROM merchandise WHERE id = ?');
            $stmt->execute([$item_id]);

            if ($stmt->rowCount() === 0) {
                echo "Товар с ID $item_id не найден в базе данных.<br>";
                $invalid_lines[] = $line;
                continue;
            }

            $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
            $stmt->execute([$customer_id]);

            if ($stmt->rowCount() === 0) {
                echo "Клиент с ID $customer_id не найден в базе данных.<br>";
                $invalid_lines[] = $line;
                continue;
            }

            $stmt = $pdo->prepare('INSERT INTO orders (item_id, customer_id, comment, status, order_date) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$item_id, $customer_id, $comment, 'new']);
        }
        fclose($file);
    }

    if (!empty($invalid_lines)) {
        file_put_contents('external_data/invalid_orders.txt', implode("\n", $invalid_lines));
    }
}

import_orders('external_data/orders.txt', $pdo);
$pdo = null;