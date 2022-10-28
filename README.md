# CodeigniterStart

### Setup the requirements

Run a local apache server


### PHP 7.3 or later
Ensure you have php 7.3 or later locally installed

```bash
    php -v
```

If you do not have php yet...

On macOS, you can use brew:
```bash
    brew install php@7.3
```

On linux, you can use apt-get:
```bash
    sudo apt-get install php7.3
```

### MySQL 5.7

We'll provide a docker-compose to setup mysql5.7.

But you can also install it yourself.

On macOS, you can use brew:
```bash
    brew install mysql@5.7
```

### Installation

```bash
    Set-Location -Path  .....
    git clone https://github.com/Bodarev/CodeigniterStart.git
    cd CodeigniterStart
    composer install
    yarn install
    gulp default
```
