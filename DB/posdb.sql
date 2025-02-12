
CREATE TABLE products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(100) UNIQUE,
    name VARCHAR(255),
    price DECIMAL(10, 2),
    stock INT(11)
);

CREATE TABLE sales (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2)
);

CREATE TABLE sales_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sale_id INT(11),
    product_id INT(11),
    quantity INT(11),
    price DECIMAL(10, 2),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (product_code, name, price, stock) VALUES
('123456', 'Product A', 10.00, 50),
('789101', 'Product B', 15.00, 30),
('112131', 'Product C', 20.00, 10);
