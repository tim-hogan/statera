#!/bin/bash
#-------------------------------------------------------------------------
# Scirpt to install a statera
# 
#   


#-------------------------------------------------------------------------
# Start

#*****************************************************************************************
# Current Directory
#*****************************************************************************************
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DATE=$(date +'%Y-%m-%dT%H%M')

#*****************************************************************************************
# Global defines
#*****************************************************************************************
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
NC='\033[0m' 

#*****************************************************************************************
# Directories
#*****************************************************************************************
APACHEDIR="/etc/apache2"


#*****************************************************************************************
# Options
#*****************************************************************************************
INSTALLNAME=""
HOSTNAME=""
INSTALLFILES=true
INSTALLDB=true
INSTALLWEB=true
UNINSTALL=false
IGNORECERTBOT=false

function usage()
{
    echo "Usage:"
    echo "    INSTALL"
    echo "    -------"
    echo "    $0 [-fg] -i <installation name> [-s <hostname>]"
    echo " "
    echo "    UNINSTALL"
    echo "    ---------"
    echo "    $0 -u  i- <installation name>"
    echo " "
    echo "    OPTIONS"
    echo "    ---------"
    echo "    -i <installation name> Required to specify the database and installation name"
    echo "    -s <host name> Optional web address"
    echo "    -f Install files only"
    echo "    -g Install files and database (Skip website)"
    echo "    -d Database only"
    echo "    -x Ignore Certbot"

}

while getopts ":dfghi:s:ux" o; do
    case "${o}" in
        d)
            INSTALLFILES=false    
            INSTALLDB=true
            INSTALLWEB=false
            ;;
        f)
            INSTALLDB=false
            INSTALLWEB=false
            ;;
        g)
            INSTALLWEB=false
            ;;
        h)
            usage
            exit 0
            ;;
        i)
            INSTALLNAME=${OPTARG}
            ;;
        s)
            HOSTNAME=${OPTARG}
            ;;
        u)
            UNINSTALL=true
            ;;
        x)
            IGNORECERTBOT=true
            ;;
        *)
            error
            usage
            exit 1
            ;;
    esac
done



#check all parameters
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}ERROR: Please run as root with sudo${NC}" >&2
  exit 1
fi

if  [[ -z $INSTALLNAME ]] ; then
  echo -e "${RED}ERROR: You must specify an install name with -i${NC}" >&2
  exit 1
fi


WEBDIR="/var/www/html/${INSTALLNAME}"
DBNAME="${INSTALLNAME}"

if  [[ -z $HOSTNAME ]] ; then
    HOSTNAME="${INSTALLNAME}.devt.nz"
fi

echo -e "${GREEN}-------------------------------------------------------------------------------${NC}"
echo -e "${GREEN}-Installation checks-----------------------------------------------------------${NC}"
echo -e "${GREEN}-------------------------------------------------------------------------------${NC}"
echo -e "${GREEN} Web Directory ${WEBDIR}${NC}"
echo -e "${GREEN} Database Name ${DBNAME}${NC}"
echo -e "${GREEN} Host Name ${HOSTNAME}${NC}"
echo -e "${GREEN}-------------------------------------------------------------------------------${NC}"
echo -en "${YELLOW} Is this correct Y/n ${NC}"

read DUMMY
if [ "$DUMMY" != "Y" ] ; then
    exit 1
fi

echo -e "${GREEN}Start of install${NC}"


#*****************************************************************************************
# Uninstall
#*****************************************************************************************
#
if $UNINSTALL ; then
    rm -r $WEBDIR
    mysql -e "DROP DATABASE ${DBNAME}"
    vault deleteshelf -s $INSTALLNAME
    echo -e "Uninstall complete"
    exit 0
fi


echo -e "${GREEN}statera Installation for $INSTALLNAME ${NC}"
echo -e "${GREEN}============================================${NC}"


#*****************************************************************************************
# Decompress Files
#*****************************************************************************************
#
#directory
echo -e "${GREEN}Decompress files${NC}"
if [ -d "${DIR}/tmpfiles" ] ; then
    rm -r ${DIR}/tmpfiles
fi
mkdir -p ${DIR}/tmpfiles
tar -C ${DIR}/tmpfiles -zxf ${DIR}/statera.tar.gz

if $INSTALLFILES ; then
    echo -e "${GREEN}Copy files${NC}"
    echo "Removing old directories"
    if [ -d "$WEBDIR" ] ; then
        rm -r $WEBDIR
    fi


    echo "Copying Web files"
    mkdir -p $WEBDIR
    cp -rT ${DIR}/tmpfiles/webfiles/ $WEBDIR
    
    mkdir -p $WEBDIR/attachments

    chown -R www-data:www-data $WEBDIR
    
    chmod 664 $WEBDIR/attachments

    echo " Web files copied"



fi

if $INSTALLDB ; then
	echo "Install database"
    if [ -d /var/lib/mysql/${DBNAME} ] ; then 
		echo -n -e "${RED}The database ${YELLOW}${DBNAME} ${RED}already exists on this server, do you want to override ${NC}[y/n] "
        read DUMMY
        if [ "$DUMMY" != "y" ] ; then
             INSTALLDB=false
        else
            echo -n -e "${RED}***WARNING*** You are about to override and existing database, this will remove all data from it. Are you sure ${NC} [y/n]"
            read DUMMY
            if [ "$DUMMY" != "y" ] ; then
                INSTALLDB=false
            else
                mysql -e "DROP DATABASE ${DBNAME}"
            fi
        fi
    fi

    if $INSTALLDB ; then
        echo -e "Installing Database"
        mysql -e "CREATE DATABASE IF NOT EXISTS ${DBNAME}"
        mysql ${DBNAME} < ${DIR}/tmpfiles/sql/statera.sql
        #*****************************************************************************************
        # DO we need to create a new and password
        #*****************************************************************************************
        #
        echo -e "${YELLOW}Do you want to create a new database MySQL access and keys? Y/n${NC}"
        read DUMMY
        if [ "$DUMMY" == "Y" ] ; then
            
            DB_USER="$(openssl rand -hex 8)"
            DB_PW="$(openssl rand -hex 8)"
            PEPPER="$(openssl rand -hex 32)"
            COOKIE_KEY="$(openssl rand -hex 32)"
            BACKUPKEY="$(openssl rand -hex 16 | base64)"

            vault newshelf -s $INSTALLNAME
            vault add -s $INSTALLNAME -k PEPPER -v $PEPPER
            vault add -s $INSTALLNAME -k DATABASE_NAME -v  ${DBNAME}
            vault add -s $INSTALLNAME -k DATABASE_HOST -v  127.0.0.1
            vault add -s $INSTALLNAME -k DATABASE_USER -v $DB_USER
            vault add -s $INSTALLNAME -k DATABASE_PW -v $DB_PW
            vault add -s $INSTALLNAME -k COOKIE_KEY -v $COOKIE_KEY
            vault add -s $INSTALLNAME -k BACKUPKEY -v $BACKUPKEY
            
            mysql -e "CREATE USER '${DB_USER}'@'%' IDENTIFIED BY '${DB_PW}'"
            mysql -e "GRANT ALL ON ${DBNAME}.* TO '${DB_USER}'@'%'"

        fi

        #create the global record
        echo "Createing global record"
        mysql ${DBNAME} -e "INSERT into global (global_default_homepage,global_default_domainname) values ('/','devt.nz')";
        

        PEPPER=$(sudo php /etc/vault/getKey -s $INSTALLNAME -k PEPPER)
        echo "PEPPER ${PEPPER}"
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
    fi
fi

if $INSTALLWEB ; then
    echo -e "Installing Website"

echo "<VirtualHost *:80>
ServerName $HOSTNAME
ServerAdmin webmaster@localhost
DocumentRoot $WEBDIR
<Directory $WEBDIR>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
SetEnv VAULTID 220759
SetEnv VAULT_SHELF $INSTALLNAME
ErrorLog \${APACHE_LOG_DIR}/error.log
CustomLog \${APACHE_LOG_DIR}/access.log combined
Header always set Strict-Transport-Security \"max-age=63072000; includeSubdomains; preload\"
</VirtualHost>" > $APACHEDIR/sites-available/$HOSTNAME.conf

    echo -e "Enable web site"
    a2ensite $HOSTNAME
    systemctl reload apache2
    if ! $IGNORECERTBOT ; then
        certbot -n --apache -d $HOSTNAME --redirect
    fi
    systemctl reload apache2
fi

echo -e "${GREEN}INSTALLATION COMPLETE${NC}"
exit 0