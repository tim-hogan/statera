ALTER TABLE staff
	ADD COLUMN staff_type ENUM('contractor', 'casual', 'wages') after staff_name;