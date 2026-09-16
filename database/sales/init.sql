USE autolux_vendas;

CREATE TABLE clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(30),
    tax_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE parts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(140) NOT NULL,
    brand VARCHAR(80) NOT NULL,
    type VARCHAR(80) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) UNSIGNED NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_parts_filters (brand, type, price)
) ENGINE=InnoDB;

CREATE TABLE payment_methods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(40) NOT NULL UNIQUE,
    name VARCHAR(80) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    payment_method_id INT UNSIGNED NOT NULL,
    total DECIMAL(10,2) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_client FOREIGN KEY (client_id) REFERENCES clients(id),
    CONSTRAINT fk_sales_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    part_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_part FOREIGN KEY (part_id) REFERENCES parts(id)
) ENGINE=InnoDB;

INSERT INTO clients (name, email, phone, tax_number) VALUES
('Ana Martins', 'ana.martins@example.com', '912 345 678', '245678901'),
('Bruno Costa', 'bruno.costa@example.com', '934 567 890', '256789012'),
('Carla Rodrigues', 'carla.rodrigues@example.com', '961 234 567', '267890123');

INSERT INTO payment_methods (code, name) VALUES
('cash', 'Dinheiro'),
('card', 'Cartão bancário'),
('transfer', 'Transferência bancária'),
('mbway', 'MB WAY');

INSERT INTO parts (sku, name, brand, type, description, price, stock, image_url) VALUES
('BOS-BP-001', 'Pastilhas de travão dianteiras', 'Bosch', 'Travagem', 'Jogo de pastilhas cerâmicas para eixo dianteiro.', 49.90, 18, 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=800&q=80'),
('MAN-FL-002', 'Filtro de óleo W 712/95', 'Mann-Filter', 'Filtros', 'Filtro de óleo de elevada capacidade e vedação segura.', 12.50, 35, 'https://images.unsplash.com/photo-1635784063288-1d21f5e3af7e?auto=format&fit=crop&w=800&q=80'),
('NGK-SP-003', 'Jogo de velas Laser Platinum', 'NGK', 'Ignição', 'Conjunto de quatro velas de ignição de longa duração.', 38.75, 22, 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?auto=format&fit=crop&w=800&q=80'),
('VAL-WP-004', 'Bomba de água', 'Valeo', 'Refrigeração', 'Bomba com junta incluída para circuito de refrigeração.', 84.00, 9, 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=800&q=80'),
('MON-SA-005', 'Amortecedor traseiro', 'Monroe', 'Suspensão', 'Amortecedor a gás para maior estabilidade e conforto.', 72.90, 12, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80'),
('PHI-HL-006', 'Lâmpada LED H7 Ultinon', 'Philips', 'Iluminação', 'Lâmpada LED H7, luz branca e baixo consumo.', 59.99, 27, 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=800&q=80'),
('CON-TB-007', 'Kit correia de distribuição', 'Continental', 'Motor', 'Kit completo com correia e tensores.', 129.50, 7, 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?auto=format&fit=crop&w=800&q=80'),
('VAR-BT-008', 'Bateria Blue Dynamic 74Ah', 'Varta', 'Elétrica', 'Bateria 12V indicada para veículos com equipamento médio.', 119.00, 11, 'https://images.unsplash.com/photo-1515923162030-3b256e3b4366?auto=format&fit=crop&w=800&q=80');
