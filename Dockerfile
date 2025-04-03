FROM php:8.2-apache
LABEL maintainer="Moath Zaghdad"

RUN apt-get update && apt-get install -y \
    libaio1 \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*


# https://www.oracle.com/database/technologies/instant-client/downloads.html
# Download link https://www.oracle.com/database/technologies/instant-client/linux-arm-aarch64-downloads.html
# >>> SDK and Basic

WORKDIR /opt/oracle

RUN curl -O https://download.oracle.com/otn_software/linux/instantclient/2370000/instantclient-sdk-linux.arm64-23.7.0.25.01.zip
RUN curl -O https://download.oracle.com/otn_software/linux/instantclient/2370000/instantclient-basic-linux.arm64-23.7.0.25.01.zip

RUN unzip instantclient-sdk-linux.arm64-*.zip
RUN unzip -o instantclient-basic-linux.arm64-*.zip

#RUN export LD_LIBRARY_PATH=/opt/oracle/instantclient_23_7:$LD_LIBRARY_PATH
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_23_7$LD_LIBRARY_PATH
ENV PATH=/opt/oracle/instantclient_23_7:$PATH



RUN echo "instantclient,/opt/oracle/instantclient_23_7" | pecl install oci8

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN echo "extension=oci8.so" >> /usr/local/etc/php/php.ini


RUN echo "/opt/oracle/instantclient_23_7" > /etc/ld.so.conf.d/oracle-instantclient.conf
RUN ldconfig
RUN export LD_LIBRARY_PATH=/opt/oracle/instantclient_23_7


RUN echo "ServerName php-app" >> /etc/apache2/apache2.conf
RUN apachectl restart

WORKDIR /var/www/html


COPY ./client/ /var/www/html/

# RUN chown -R www-data:www-data /var/www/html/
# RUN chmod -R 755 /var/www/html/

EXPOSE 80
