# NeoxDoctrineSecureBundle { Symfony 6/7 }
This bundle provides Encrypt/Decrypt data sensible in a Db system in your application.
Its main goal is to make it simple for you to manage encrypt & decrypt sensible data into Db!
This bundle is to refresh the old bundle [DoctrineEncryptBundle](https://github.com/absolute-quantum/DoctrineEncryptBundle)

## Installation BETA VERSION !!
Install the bundle for Composer !! as is still on beta version !!

````
  composer require xorgxx/neox-doctrine-secure-bundle
  or 
  composer require xorgxx/neox-doctrine-secure-bundle:0.*
````
🚨 You will heva to add in your projet 🚨
````
  composer require paragonie/halite
````
.env file
````
  ....
  NEOX_ENCRY_DSN=Halite://login:[password]@stndalone
  ....
````
**We have only implemented Halite service to Crypt / unCrypt**

**NOTE:** _You may need to use [ symfony composer dump-autoload ] to reload autoloading_

 ..... Done 🎈

## Usage !
In entity, you want to secure field (data) 
````php

  use NeoxDoctrineSecure\NeoxDoctrineSecureBundle\Attribute\neoxEncryptor; 
  ....
  
  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[neoxEncryptor]
  private ?string $content = null;
  
  ....
````
## Important !
Consider the size / length of field you want to crypt !! ex: length:10 
````php

  #[neoxEncryptor]
  #[ORM\Column(length: 20)]
  private ?string $name = null;
  
  "john doe" <- decrypt / encrypt -> "MUIFAOpLp21iX1Dy2ZNkYbby6zo7ADYgVs-hGkNaWR2OF5AbQUMcBKZHigtFVxZiIFWyOTV8Ts-9q_pNAHBxCKcAPZNJjfPgVQglMLAKi0bZicmPlCQKJpRpX2k5IAjAqawOlFsPpD9KikIEFRhuy"
  
````

## Contributing
If you want to contribute \(thank you!\) to this bundle, here are some guidelines:

* Please respect the [Symfony guidelines](http://symfony.com/doc/current/contributing/code/standards.html)
* Test everything! Please add tests cases to the tests/ directory when:
    * You fix a bug that wasn't covered before
    * You add a new feature
    * You see code that works but isn't covered by any tests \(there is a special place in heaven for you\)
## Todo
* Add a Remote system for storage Hash => Key
* to be able to encrypt/decrypt, according to a propriety type | int, string, phone ....
* Custom provider class Encrypt/decrypt.
* Dispatcher to custom code.
* Commands to manage more easily status, encrypt, decrypt ....

## Thanks