#!/bin/bash

supportDatabases=(mysql sqlite pgsql)
installLockFile=${FeehiCMSPath}/install/install.lock
inArray(){
  i=0
  while [ $i -lt ${#supportDatabases[@]} ]
  do
      if [ ${supportDatabases[$i]} == $1 ];then
        return 1
      fi
      let i++
  done
  return 0
}

initConfig(){
  $FeehiCMSPath/init --env=${Env} --overwrite=All
}

importDb(){
  dbType=(${DbDSN//:/ })
  inArray $dbType
  if [  $? -eq 0 ];then
    echo "DBHost error, only support "${supportDatabases[*]}
    exit 1
  fi

  if [ $dbType != sqlite ];then
    if [ ${dbUser} == "" ];then
        echo $dbType "must set env DBUser"
    fi
  fi

  sed -i "s#'dsn' => 'sqlite:/feehi.db'#'dsn' => '${DbDSN}'#g" $FeehiCMSPath/common/config/main-local.php
  sed -i "s#'charset' => 'utf8'#'charset' => '${DbCharset}'#g" $FeehiCMSPath/common/config/main-local.php
  if [ $dbType != sqlite ];then
    sed -i "s#'username' => 'root'#'username' => '${DbUser}'#g" $FeehiCMSPath/common/config/main-local.php
    sed -i "s#'password' => ''#'${DbPassword}'#g" $FeehiCMSPath/common/config/main-local.php
  fi
  $FeehiCMSPath/yii migrate/up --interactive=0 frontendUri=${FrontendUri}
  echo "Import database success; Configured Database info:"
  echo -e "DbDSN ${DbDSN}"
  if [ $dbType != sqlite ];then
      echo -e "DBUser ${DBUser} \nDbPassword ${DbPassword}"
  fi
}

start(){
  $FeehiCMSPath/yii serve ${Listening}
}

onlineInstall=1
downloadUploadFiles=0
forceInstall=0
while getopts "odf" OPT; do
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
    ?)
      echo "Invalid option: -$OPTARG"
      exit 1
  esac
done

case ${!#} in
  start)
    if [ $forceInstall -eq 1 ];then
      rm -rf $installLockFile
    fi

    if [ ! -f $installLockFile ];then #need to install
      initConfig
      if [ $onlineInstall -eq 1 ];then #auto install
        importDb
      else
        rm -rf $installLockFile #need visis http://your-server-ip:port/install.php and then fill info for install FeehiCMS
      fi

      if [ $downloadUploadFiles -eq 1 ];then
        ${FeehiCMSPath}/yii feehi/download-upload-files
      fi
    fi

    start
    ;;
  sh)
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

      examples:
        docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e FrontendUri=//your-server-ip -e DbDSN=sqlite:/data/feehi.db feehi/cms #auto import database sqlite
        docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e FrontendUri=//your-server-ip -e DbDSN=sqlite:/data/feehi.db feehi/cms -o start #start server then visit http://your-server-ip/install.php
        docker run -it -name feehi -p 80:80 -v /data:/data -e Listening=0.0.0.0:80 -e FrontendUri=//your-server-ip -e DbDSN=mysql:host=x.x.x.x;dbname=feehi -e DbUser=xxx -e DbPassword=xxxxxx feehi/cms #auto import database mysql
    "
    exit 1
esac