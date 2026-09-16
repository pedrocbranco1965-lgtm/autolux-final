USE autolux_fornecedores;

CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(30),
    address VARCHAR(255),
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE supplier_parts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT UNSIGNED NOT NULL,
    sku VARCHAR(30) NOT NULL,
    name VARCHAR(140) NOT NULL,
    unit_cost DECIMAL(10,2) UNSIGNED NOT NULL,
    available_stock INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_supplier_sku (supplier_id, sku),
    CONSTRAINT fk_supplier_parts_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

CREATE TABLE purchase_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT UNSIGNED NOT NULL,
    status ENUM('submitted', 'confirmed', 'received', 'cancelled') NOT NULL DEFAULT 'submitted',
    total DECIMAL(10,2) UNSIGNED NOT NULL,
    notes VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

CREATE TABLE purchase_order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT UNSIGNED NOT NULL,
    supplier_part_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_cost DECIMAL(10,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_part
        FOREIGN KEY (supplier_part_id) REFERENCES supplier_parts(id)
) ENGINE=InnoDB;

INSERT INTO suppliers (name, email, phone, address) VALUES
('EuroParts Portugal', 'encomendas@europarts.example', '211 234 567', 'Sintra, Lisboa'),
('Mecânica Global', 'comercial@mecanicaglobal.example', '229 876 543', 'Maia, Porto'),
('AutoComponentes Sul', 'vendas@autocomponentes.example', '289 456 123', 'Loulé, Faro');

INSERT INTO supplier_parts (supplier_id, sku, name, unit_cost, available_stock) VALUES
(1, 'BOS-BP-001', 'Pastilhas de travão dianteiras', 31.20, 120),
(1, 'MAN-FL-002', 'Filtro de óleo W 712/95', 7.30, 300),
(1, 'NGK-SP-003', 'Jogo de velas Laser Platinum', 24.80, 80),
(2, 'VAL-WP-004', 'Bomba de água', 56.00, 45),
(2, 'MON-SA-005', 'Amortecedor traseiro', 48.50, 75),
(2, 'CON-TB-007', 'Kit correia de distribuição', 88.40, 35),
(3, 'PHI-HL-006', 'Lâmpada LED H7 Ultinon', 39.99, 140),
(3, 'VAR-BT-008', 'Bateria Blue Dynamic 74Ah', 82.50, 55);
