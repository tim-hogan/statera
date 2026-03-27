ALTER TABLE staff
	ADD COLUMN staff_use_esct_tax tinyint default 0 after staff_kiwi_save_employee_rate;

ALTER TABLE staff
	ADD COLUMN staff_esct_tax_rate double after staff_use_esct_tax;
