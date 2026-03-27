ALTER TABLE staff
	ADD COLUMN staff_has_kiwi_saver TINYINT DEFAULT 0 after staff_holiday_pay_rate;

ALTER TABLE staff
	ADD COLUMN staff_kiwi_save_employer_rate DOUBLE after staff_has_kiwi_saver;

ALTER TABLE staff
	ADD COLUMN staff_kiwi_save_employee_rate DOUBLE after staff_kiwi_save_employer_rate;
