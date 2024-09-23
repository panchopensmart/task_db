1) для запуска нужно в корне каталога запустить контейнеры `docker compose up --build`
2) Экзекаем контейнер с бд `docker exec -it postgres bash` 
3) И в контейнере postgres выполняем команду миграции `psql -U user -d mydatabase -f /tmp/dump.sql`
Для воспроизведения нужно послать get запрос на адрес http://127.0.0.1:8080
*комментарий не обязательно может быть в данны
*невалидные значения записываются в external_data/invalid_orders.txt

Запросы возвращающие набор данных:
1.`SELECT name
FROM clients
WHERE id NOT IN (
SELECT DISTINCT customer_id
FROM orders
WHERE order_date >= NOW() - INTERVAL '7 days'
);`
2.`SELECT name
FROM clients
WHERE id IN (
SELECT customer_id
FROM orders
GROUP BY customer_id
ORDER BY COUNT(*) DESC
LIMIT 5
);`
3. *не знаю сумму по заказам
4.`SELECT m.name
   FROM merchandise m
   LEFT JOIN orders o ON m.id = o.item_id AND o.status = 'complete'
   GROUP BY m.id, m.name
   HAVING COUNT(o.id) = 0;`

Почему были выбраны такие индексы: 
1. Созданы индексы для name таблиц товаров и клиентов при условии что товаров и клиентов в сервисе будет много
2. Создан составной индекс на айди товара и клиента в таблице orders чтобы оптимизировать выборку заказов для конкретного клиента по выбранному товару