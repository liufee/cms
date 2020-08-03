FROM php:7.4-fpm
MAINTAINER liufee job@feehi.com


RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo mysqli pdo_mysql pdo_pgsql


#RUN set -eux; \
  #curl --fail --location --retry 3 --output /usr/bin/composer https://getcomposer.org/download/${COMPOSER_VER}/composer.phar


RUN cd /usr/src \
    && curl --fail --location --retry 3 --output /usr/src/vendor.zip https://resource-1251086492.cos.ap-shanghai.myqcloud.com/vendor.zip \
    && unzip /usr/src/vendor.zip && rm -rf /usr/src/vendor.zip


ENV FeehiCMSPath="/usr/local/feehicms"
ENV DbDSN="sqlite:/data/feehi.db"
ENV DbUser="root"
ENV DbPassword=""
ENV DbCharset="utf8"
ENV FrontendUri="//127.0.0.1/"
ENV Listening="0.0.0.0:80"
ENV Env="Development"


COPY . ${FeehiCMSPath}
RUN rm -rf ${FeehiCMSPath}/install/install.lock \
    && cp -rf /usr/src/vendor ${FeehiCMSPath} && rm -rf /usr/src/vendor \
    && cp ${FeehiCMSPath}/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh


ENV PATH=$PATH:$FeehiCMSPath


WORKDIR ${FeehiCMSPath}


EXPOSE 9091


ENTRYPOINT ["/bin/bash", "/usr/local/bin/docker-entrypoint.sh"]


CMD ["start"]