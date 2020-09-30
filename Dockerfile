FROM ubuntu:20.04

# Fix timezone issue
ENV TZ=Europe/London

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && apt-get update \
    && apt-get dist-upgrade -y \
    && apt-get install -y vim python3-pip python3-pymysql php7.4-cli php7.4-mysql python3-setuptools composer \
    && pip3 install flask_sqlalchemy

# Add and install the swaps module
COPY phe-swaps-module /root/phe-swaps-module
WORKDIR /root/phe-swaps-module/phe_recommender
RUN python3 setup.py install

# Add our wrapper code
COPY src /root/src

# Run composer install to install packages
WORKDIR /root/src
RUN composer install

# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/root/src/startup.sh"]
