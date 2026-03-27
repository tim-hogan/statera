ALTER TABLE timesheet
	ADD COLUMN timesheet_type  enum('hours','direct') default 'hours' after timesheet_date;

ALTER TABLE timesheet
	ADD COLUMN timesheet_direct_gross DECIMAL(11,2) default 0.0 after timesheet_staff;
