#!/bin/bash

supportDatabases=(mysql sqlite pgsql)
installLockFile=${FeehiCMSPath}/install/install.lock
yiiCmd=$FeehiCMSPath/yii

function inArray(){
    i=0
    while [ $i -lt ${#supportDatabases[@]} ]
    do
        if [ "${supportDatabases[$i]}" == "$1" ];then
              return 0
        fi
        ((i++))
    done
    return 1
}

function initConfig(){
    "${FeehiCMSPath}/init" --env="${Env}" --overwrite=All
}

function importDB(){
    dbType=(${DBDSN//:/ })
    if ! inArray "$dbType"; then
    echo "DBHost error, only support ${supportDatabases[*]}"
    exit 1
fi

    if [ "${dbType}" == sqlite ];then
        temp=${DBDSN%/*}
        sqliteDataPath=${temp/sqlite:/}
    if [ ! -d "${sqliteDataPath}" ]; then
    mkdir -p "$sqliteDataPath"
        fi
    else
        if [ "${DBUser}" == "" ];then
            echo "${dbType} must set env DBUser"
        fi
    fi

    sed -i "s#.*'dsn'.*#            'dsn' => '${DBDSN}',#g" "${FeehiCMSPath}/common/config/main-local.php"
    sed -i "s#.*'charset'.*#            'charset' => '${DBCharset}',#g" "${FeehiCMSPath}/common/config/main-local.php"
    sed -i "s#.*'tablePrefix'.*#            'tablePrefix' => '${TablePrefix}',#g" "${FeehiCMSPath}/common/config/main-local.php"
    if [ "${dbType}" != sqlite ];then
        sed -i "s#.*'username'.*#            'username' => '${DBUser}',#g" "${FeehiCMSPath}/common/config/main-local.php"
        sed -i "s#.*'password'.*#            'password' => '${DBPassword}',#g" "${FeehiCMSPath}/common/config/main-local.php"
    fi
    $yiiCmd migrate/up --interactive=0 frontendUri="${FrontendUri}" adminUsername="${AdminUsername}" adminPassword="${AdminPassword}"

    echo "Import database success; Configured Database info:"
    echo -e "DBDSN ${DBDSN}"
    if [ "$dbType" != sqlite ];then
        echo -e "DBUser ${DBUser} \nDBPassword ${DBPassword}"
    fi
}

start(){
		if [ "$fpmMode" -eq 1 ];then
        php-fpm
    else
        $yiiCmd serve "${Listening}"
    fi
}

onlineInstall=1
downloadUploadFiles=0
forceInstall=0
fpmMode=0
while getopts "odfm" OPT; do
  case ${OPT} in
    o)
       onlineInstall=0 #${OPTARG}
       ;;
    d)
      downloadUploadFiles=1 #download FeehiCMS init articles uploaded files
      ;;
    f)
      forceInstall=1 #will force to install no matter whether installed
      ;;
    m)
      fpmMode=1 #run fpm
      ;;
    ?)
      echo "Invalid option: -$OPTARG"
      exit 1
  esac
done

case ${!#} in
    start)
        if [ $forceInstall -eq 1 ];then
            rm -rf "$installLockFile"
        fi

        if [ ! -f "$installLockFile" ];then #need to install
            initConfig
            if [ $onlineInstall -eq 1 ];then #auto install
                importDB
            else
                rm -rf "$installLockFile" #need visis http://your-server-ip:port/install.php and then fill info for install FeehiCMS
            fi

            if [ $downloadUploadFiles -eq 1 ];then
                $yiiCmd feehi/download-upload-files
            fi
        fi
        start
        ;;
    sh|bash|/bin/bash|/bin/sh)
        /bin/bash -C
        ;;
    *)
    echo "usage:
        start: start server(if not installed will install first)
        sh: entry bash

        options
            -f will force to install FeehiCMS, no matter whether installed
            -o will not auto import database. you need visit host:port/install.php for online install
            -d download init FeehiCMS existed articles uplaoded files(pictures)
            -m run fpm(port 9000 cannot be modified)

        examples:
            docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e feehi/cms -o start #start server then visit http://your-server-ip/install.php
            docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e FrontendUri=//your-server-ip -e DBDSN=sqlite:/data/feehi.db -e TablePrefix=feehi_ -e AdminUsername=admin -e AdminPassword=123456 feehi/cms #auto import database sqlite
            docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e FrontendUri=//your-server-ip -e DBDSN=mysql:host=x.x.x.x;dbname=feehi -e DBUser=xxx -e DBPassword=xxxxxx -e TablePrefix=feehi_ -e AdminUsername=admin -e AdminPassword=123456 feehi/cms #auto import database mysql
        "
    exit 1
esac