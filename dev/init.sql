CREATE DATABASE APP;
USE APP;

-- Users table.
CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, 
    email VARCHAR(255) UNIQUE NOT NULL,
    role ENUM('Admin', 'Procurement Officer', 'Department Head') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Audit purposes
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Audit purposes
);

-- Vendors table.
CREATE TABLE VENDORS (
    vendor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_information VARCHAR(255) NOT NULL,
    service_provided TEXT DEFAULT NULL,
    payment_terms TEXT DEFAULT NULL,
    created_by INT, -- Reference to user_id in USERS table
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Audit purposes
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Audit purposes
    contact_info VARCHAR(8) DEFAULT NULL, -- Assuming Singapore number
    services VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (created_by) REFERENCES USERS(user_id) -- Foreign key constraint
);

-- Inventory table.
CREATE TABLE INVENTORY (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 0,
    restock_level INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Audit purposes
);

-- Purchase orders table.
CREATE TABLE ORDERS (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT, -- Reference to vendor_id in VENDORS table
    inventory_item INT, -- Reference to inventory_id in INVENTORY table
    quantity INT NOT NULL,
    request_id INT, -- Reference to request_id in REQUESTS table
    created_by INT, -- Reference to user_id in USERS table
    status ENUM('Pending', 'Approved', 'Completed') DEFAULT 'Pending' NOT NULL,
    FOREIGN KEY (vendor_id) REFERENCES VENDORS(vendor_id), -- Foreign key constraint
    FOREIGN KEY (inventory_item) REFERENCES INVENTORY(inventory_id), -- Foreign key constraint
    FOREIGN KEY (request_id) REFERENCES REQUESTS(request_id), -- Foreign key constraint
    FOREIGN KEY (created_by) REFERENCES USERS(user_id) -- Foreign key constraint
);

-- Procurement requests table.
CREATE TABLE REQUESTS (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    created_by INT, -- Reference to user_id in USERS table
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    FOREIGN KEY (created_by) REFERENCES USERS(user_id) -- Foreign key constraint
);

