FROM php:7.4-cli-alpine
LABEL org.opencontainers.image.source="https://github.com/joakimwinum/php-turingmachine"
LABEL org.opencontainers.image.licenses="MIT"
WORKDIR /usr/src/php-turingmachine
COPY . .
CMD ["php", "./turingmachine.php"]
