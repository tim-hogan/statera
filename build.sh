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

OLDVERSION=0
VERSION=1
INGNOREGIT=false
BRANCH="main"
TEST=false




usage () {
    echo -e "$0 Useage"
    echo -e "  $0 [-hi]"
    echo -e "     -h Help"
    echo -e "     -i Ignore GIT pulls and push"
    echo -e "     -t <branch> Test system and test branch"
}

emptyandcreate () {
	if [ -d "$1" ]; then
		rm -r $1
	fi
	mkdir -p $1
}

#checks
if [ "$EUID" -eq 0 ]; then
  echo -e "${RED}ERROR: Please DO NOT run as root with sudo${NC}" >&2
  exit 1
fi

while getopts ":hit:" o; do
    case "${o}" in
        h)
            usage
            exit 0
            ;;
        i)
            INGNOREGIT=true
            ;;
        t)
            BRANCH=${OPTARG}
            TEST=true
            ;;
        *)
            usage
            exit 1
            ;;
    esac
done

echo -e "${GREEN}===============${NC}"
echo -e "${GREEN}statera Builder${NC}"
echo -e "${GREEN}===============${NC}"


#*****************************************************************************************
# Git pulls
#*****************************************************************************************
if ! $INGNOREGIT ; then

    echo -e "${GREEN}Get source from github${NC}"
    echo -e "Getting devt framework from GitHub"
    cd ../devt
    rm -fr .git
    rm -fr *
    git init
    git remote add devt https://github.com/tim-hogan/devt.git
    git pull devt $BRANCH
    cd -

    echo -e "Getting satera from GitHub"
    rm -fr .git
    rm -fr *
    git init
    git remote add satera https://github.com/tim-hogan/statera.git
    git pull satera $BRANCH

    . version
    OLDVERSION=${VERSION}
    VERSION=$((VERSION + 1))

    echo -e "Write back to the version file with VERSION=${VERSION}"
    echo "VERSION=${VERSION}" > version
    echo -e "Cat of version follows"
    cat version

    chmod +x build.sh
fi

chmod +x src/install.sh

echo -e "${GREEN}Start fo build${NC}"
echo -e "${GREEN}==============${NC}"


#create the install directory
emptyandcreate install

#create the package directory
emptyandcreate packagefiles

#copy the install script
cp ./src/install.sh ./install

#change directory to the files and copy them up.
cd packagefiles

#sql
emptyandcreate sql
cp ../src/sql/statera.sql ./sql
if [  -f "../src/sql/upgrade.sql" ] ; then
    echo "Database upgrade files found"
    cp ../src/sql/upgrade.sql ./sql    
fi

#webfiles
emptyandcreate webfiles
cp ../src/AccountsPayable.php                   ./webfiles
cp ../src/AccountsReceivable.php                ./webfiles
cp ../src/BankAccounts.php                      ./webfiles
cp ../src/ChangePassword.php                    ./webfiles
cp ../src/EndofYear.php                         ./webfiles
cp ../src/Expenses.php                          ./webfiles
cp ../src/FinancialStatements.php               ./webfiles
cp ../src/GSTReport.php                         ./webfiles
cp ../src/index.php                             ./webfiles
cp ../src/Invoice.php                           ./webfiles
cp ../src/Invoices.php                          ./webfiles
cp ../src/JournalDump.php                       ./webfiles
cp ../src/Maint.php                             ./webfiles
cp ../src/MaintSel.php                          ./webfiles
cp ../src/PayTax.php                            ./webfiles
cp ../src/Sale.php                              ./webfiles
cp ../src/ShareIssue.php                        ./webfiles
cp ../src/Signin.php                            ./webfiles
cp ../src/Signout.php                           ./webfiles
cp ../src/UndoLast.php                          ./webfiles
cp ../src/Wizard.php                            ./webfiles

emptyandcreate webfiles/includes
cp ../src/includes/classstateraDB.php           ./webfiles/includes
cp ../src/includes/commonSession.php            ./webfiles/includes
cp ../src/includes/securityParams.php           ./webfiles/includes

cp ../src/includes/menu.html                    ./webfiles/includes
cp ../src/includes/footer.html                  ./webfiles/includes
cp ../src/includes/heading.html                 ./webfiles/includes


#shared classes
cp ../../devt/classes/classAccounts.php	    	./webfiles/includes
cp ../../devt/classes/classEnvironment.php		./webfiles/includes
cp ../../devt/classes/classFormList2.php		./webfiles/includes
cp ../../devt/classes/classInputParam.php		./webfiles/includes
cp ../../devt/classes/classParseText.php        ./webfiles/includes
cp ../../devt/classes/classRolling.php			./webfiles/includes
cp ../../devt/classes/classSQLPlus2.php			./webfiles/includes
cp ../../devt/classes/classSecure.php			./webfiles/includes
cp ../../devt/classes/classTime.php			    ./webfiles/includes
cp ../../devt/classes/classVault.php			./webfiles/includes


#copy css
emptyandcreate webfiles/css
cp ../src/css/base.css                          ./webfiles/css
cp ../src/css/footer.css                        ./webfiles/css
cp ../src/css/form.css                          ./webfiles/css
cp ../src/css/heading.css                       ./webfiles/css
cp ../src/css/list.css                          ./webfiles/css
cp ../src/css/menu.css                          ./webfiles/css
cp ../src/css/Signin.css                        ./webfiles/css

#copy js
emptyandcreate webfiles/js
cp ../src/js/st.js                              ./webfiles/js

#copy config
emptyandcreate webfiles/config
cp ../src/config/StateraForm.php                ./webfiles/config

#copy docs
emptyandcreate webfiles/docs
cp ../src/docs/IR265.pdf                        ./webfiles/docs

tar -zcf ../install/statera.tar.gz .

cd ..

echo -e "The file will be packaged with a password"
echo -en "${CYAN}"
zip -er install.zip install > /dev/null
echo -en "${NC}"

if [ ! -f install.zip ] ; then
	rm -r install
	echo -e "${RED}Build failed${NC}"
	exit 1
fi

mkdir -p package
mkdir -p package/current
mkdir -p package/archive

if [ -f "./package/current/install.zip" ] ; then
    cp ./package/current/install.zip ./package/archive/install.v${OLDVERSION}.zip
fi
cp install.zip ./package/current

#Remote copy to devt host
echo -e "${YELLOW}You will be asked for the deVT password as we are about to copy to the devt host${NC}"
if ! $TEST ; then
    echo -e "rename /var/www/html/static/statera/install.zip /var/www/html/static/statera/install-${DATE}.zip\n put install.zip /var/www/html/static/statera/install.zip" | sftp deVT@static.devt.nz
else
    echo -e "put install.zip /var/www/html/static/statera/test/install.zip" | sftp deVT@static.devt.nz

fi

rm -r install
rm install.zip
rm -r packagefiles

#*****************************************************************************************
# Git push
#*****************************************************************************************
if ! $INGNOREGIT && ! $TEST ; then
    echo -e "Merging back to GitHub"
    git checkout $BRANCH
    echo -e "Adding new ./package/intall.zip"
    git add package/current/install.zip
    if [ -f "./package/archive/install.v${OLDVERSION}.zip" ] ; then
        echo -e "Adding to archive ./package/archive/install.v${OLDVERSION}.zip"
        git add package/archive/install.v${OLDVERSION}.zip
    fi
    echo -e "Adding new version"
    git add version
    echo -e "git Commit"
    git commit -m "Version ${VERSION}"
    echo -e "git Push"
    git push covidpass $BRANCH
fi

chmod +x build.sh


echo -e "${GREEN}Build complete${NC}"
