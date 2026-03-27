ALTER TABLE user
	ADD COLUMN user_notify_quotes  TINYINT default 0 after user_undolist;