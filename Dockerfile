FROM dunglas/frankenphp

# Install required packages and utilities
RUN apt update && apt install -y supervisor \
    curl \
    gnupg \
    nano \
    bash \
    npm \
    libpng-dev \
    libzip-dev \
    unixodbc-dev \
    util-linux \
    git \
    zsh \
    procps

# Extensions
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    imap \
    bcmath \
    redis \
    curl \
    exif \
    hash \
    iconv \
    json \
    mbstring \
    mysqli \
    mysqlnd \
    pcntl \
    pcre \
    xml \
    libxml \
    zlib \
    zip \
    pdo

# Set up Oh My Zsh for root user
RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended && \
    # Install desired Oh My Zsh plugins and themes
    git clone https://github.com/zsh-users/zsh-syntax-highlighting.git ${ZSH_CUSTOM:-/root/.oh-my-zsh/custom}/plugins/zsh-syntax-highlighting && \
    git clone https://github.com/zsh-users/zsh-autosuggestions ${ZSH_CUSTOM:-/root/.oh-my-zsh/custom}/plugins/zsh-autosuggestions && \
    # Configure plugins in .zshrc
    sed -i 's/plugins=(git)/plugins=(git z zsh-syntax-highlighting zsh-autosuggestions)/' /root/.zshrc && \
    # Change default shell to Zsh
    chsh -s $(which zsh)

# Set work directory and copy project files
WORKDIR /var/www
COPY . .

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure supervisord
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create supervisord log folder
RUN mkdir -p /var/log/supervisor

# Link public folder to html
RUN rm -rf /var/www/html && ln -s public html

# Expose ports for FrankenPHP
EXPOSE 80
EXPOSE 443
EXPOSE 443/udp
EXPOSE 2019
EXPOSE 8080
EXPOSE 3306

# Define the entrypoint to run
ENTRYPOINT ["./docker/entrypoint.sh"]
