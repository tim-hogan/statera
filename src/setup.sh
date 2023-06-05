#!/bin/bash
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}ERROR: Please run as root with sudo${NC}" >&2
  exit 1
fi

DBNAME="statera"

#load the database
echo "Creating database from SQL file"
mysql ${DBNAME} < ${DBNAME}.sql

#create the global record
echo "Createing global record"
mysql ${DBNAME} -e "INSERT into global (global_default_homepage,global_default_domainname) values ('/','devt.nz')";

PEPPER=$(/etc/vault/getKey -s statera -k PEPPER)
#create the first user in the database
echo "Createing first database admin user"
#create the salt and hash
SALT="$(openssl rand -hex 32)"
H1="$(echo -n "${SALT}${PEPPER}" | openssl dgst -sha256)"
HASH1=${H1:$((${#H1} - 64)):64}
H1="$(echo -n "admin${HASH1}" | openssl dgst -sha256)"
HASH=${H1:$((${#H1} - 64)):64}

RAND1="$(openssl rand -hex 8)"
SESSION_KEY="$(openssl rand -hex 32)"
echo "Username and passwords have been created"

mysql ${DBNAME} -e "INSERT into user (user_randid,user_session_key,user_lastname,user_username,user_hash,user_salt,user_security,user_timezone) values ('${RAND1}','${SESSION_KEY}','Administrator','admin','${HASH}','${SALT}',2047,'Pacific/Auckland')"
