ALTER TABLE journal
	ADD COLUMN journal_kiwisaver_employee DECIMAL(11,2)default 0.0 after journal_vendor_tax_number;

ALTER TABLE journal
	ADD COLUMN journal_kiwisaver_employer DECIMAL(11,2)default 0.0 after journal_kiwisaver_employee;

ALTER TABLE journal
	ADD COLUMN journal_kiwisaver_esct_tax DECIMAL(11,2)default 0.0 after journal_kiwisaver_employer;

