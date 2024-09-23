CREATE TABLE IF NOT EXISTS clients
(
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
    );

INSERT INTO clients (id, name)
VALUES (1, 'Иван Иванов'),
       (2, 'Петр Петров'),
       (3, 'Сидор Сидоров') ON CONFLICT (id) DO NOTHING;

CREATE INDEX idx_clients_name ON clients (name);

CREATE TABLE IF NOT EXISTS merchandise
(
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
    );

INSERT INTO merchandise (id, name)
VALUES (1, 'Товар 1'),
       (2, 'Товар 2'),
       (3, 'Товар 3') ON CONFLICT (id) DO NOTHING;

CREATE INDEX idx_merchandise_name ON merchandise (name);

CREATE TABLE IF NOT EXISTS orders
(
    id SERIAL PRIMARY KEY,
    item_id INT NOT NULL,
    customer_id INT NOT NULL,
    comment TEXT,
    status VARCHAR(10) CHECK (status IN ('new', 'complete')),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES merchandise (id),
    FOREIGN KEY (customer_id) REFERENCES clients (id)
    );

CREATE INDEX idx_orders_item_customer ON orders (item_id, customer_id);