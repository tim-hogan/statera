ALTER TABLE staff
	ADD COLUMN staff_add_holiday_pay TINYINT DEFAULT 0 after staff_hourly_rate3;

ALTER TABLE staff
	ADD COLUMN staff_bank_acct_number VARCHAR(24) after staff_tax_code;

ALTER TABLE staff
	ADD COLUMN staff_bank_acct_name VARCHAR(60) after staff_bank_acct_number;

ALTER TABLE staff
	ADD COLUMN staff_holiday_pay_rate DOUBLE DEFAULT 0.0 after staff_add_holiday_pay;

ALTER TABLE journal
	ADD COLUMN journal_wage_tax  DECIMAL(11,2)  after journal_gross;
