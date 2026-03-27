ALTER TABLE staff
    ADD COLUMN staff_tax_code VARCHAR(8) NULL after staff_tax_number;

ALTER TABLE staff
    ADD COLUMN staff_hourly_rate1 DECIMAL(5,2) NULL DEFAULT 0.0 after staff_list_on_timesheet;

ALTER TABLE staff
    ADD COLUMN staff_hourly_rate2 DECIMAL(5,2) NULL DEFAULT 0.0 after staff_hourly_rate1;

ALTER TABLE staff
    ADD COLUMN staff_hourly_rate3 DECIMAL(5,2) NULL DEFAULT 0.0 after staff_hourly_rate2;
