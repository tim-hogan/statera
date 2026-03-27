ALTER TABLE timesheet
	ADD COLUMN timesheet_pay_cadence  ENUM('weekly', 'fortnightly', 'monthly', 'adhoc') default 'adhoc' after timesheet_paid_date;