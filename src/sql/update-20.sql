ALTER TABLE quote
	ADD COLUMN quote_accepted_timestamp DATETIME after quote_option_no_item_cost;
ALTER TABLE quote
	ADD COLUMN quote_denied_timestamp DATETIME after quote_accepted_timestamp;
ALTER TABLE quote
	ADD COLUMN quote_completed_timestamp DATETIME after quote_denied_timestamp;