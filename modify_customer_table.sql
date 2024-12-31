-- Backup the existing table first
CREATE TABLE tbl_customer_backup AS SELECT * FROM tbl_customer;

-- Modify the customer table to remove B2B fields and unnecessary fields
ALTER TABLE tbl_customer
    DROP COLUMN cust_state,
    DROP COLUMN cust_zip,
    DROP COLUMN cust_b_name,
    DROP COLUMN cust_b_cname,
    DROP COLUMN cust_b_phone,
    DROP COLUMN cust_b_country,
    DROP COLUMN cust_b_address,
    DROP COLUMN cust_b_city,
    DROP COLUMN cust_b_state,
    DROP COLUMN cust_b_zip,
    DROP COLUMN cust_s_name,
    DROP COLUMN cust_s_cname,
    DROP COLUMN cust_s_phone,
    DROP COLUMN cust_s_country,
    DROP COLUMN cust_s_address,
    DROP COLUMN cust_s_city,
    DROP COLUMN cust_s_state,
    DROP COLUMN cust_s_zip,
    DROP COLUMN cust_cname; 