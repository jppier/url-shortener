# JP URL Shortener

An easy way to create your own URL Shortening service like http://goo.gl or http://bit.ly.

## Getting Started

This will help get you up and running in no time

### Installation

1. clone the project to your webhost
```
    > git clone https://github.com/jppier/url-shortener.git
```
2. Update the app/config/parameters.yml file with the correct hosting domain
```
    redirect_base_url: http://your.base.domain
```
3. Run Composer install
```
    > cd url-shortener/
    > composer install
```
4. Clear your Symfony cache
```
    > php bin/console cache:clear
```

...and you're ready to roll

