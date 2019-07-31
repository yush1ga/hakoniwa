FROM skiychan/nginx-php7

RUN curl -sL https://rpm.nodesource.com/setup_8.x | bash -
RUN yum install -y nodejs gcc-c++ make

ENV PATH /usr/local/php/bin:$PATH
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /data/www
COPY . .

RUN npm install
RUN composer dumpautoload

WORKDIR /
EXPOSE 80 443
ENTRYPOINT ["/start.sh"]
