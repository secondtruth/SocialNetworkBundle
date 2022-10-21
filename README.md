# Getting Started with SocialNetworkBundle

The SocialNetworkBundle adds support to make a social network on your Symfony 5 project.

***HELP WANTED for porting this Bundle to Symfony 5.***

## Prerequisites

### Translations

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/2.0/book/translation.html).

## Installation

The bundle uses [FOSUser](https://github.com/FriendsOfSymfony/FOSUserBundle).
Configuration of this bundle is also included on this document.

Installation is a quick 3 step process:

1. Install KibokoSocialNetworkBundle using composer
2. Enable the Bundle
3. Configure your application's security.yml
4. Configure the FOSUserBundle
5. Configure the bundle
6. Import KibokoSocialNetworkBundle routing
7. Update your database schema

### Step 1: Install via Composer

[Install Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) if you don't already have it present on your system.

To install the bundle, run the following command and you will get the latest version:

    $ composer require secondtruth/social-network-bundle

### Step 2: Enable the bundles

Finally, enable the bundles in the kernel:

``` php
<?php
// config/bundles.php

return [
    // ...
    FOS\UserBundle\FOSUserBundle::class => ['all' => true],
    Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
    Kiboko\Bundle\SocialNetworkBundle\KibokoSocialNetworkBundle::class => ['all' => true],
];
```

### Step 3: Configure your application's security.yml

In order for Symfony's security component to use the FOSUserBundle, you must
tell it to do so in the `security.yml` file. The `security.yml` file is where the
basic configuration for the security for your application is contained.

Below is a minimal example of the configuration necessary to use the FOSUserBundle
in your application:

``` yaml
# app/config/security.yml
security:
    providers:
        fos_userbundle:
            id: fos_user.user_manager

    encoders:
        FOS\UserBundle\Model\UserInterface: sha512

    firewalls:
        main:
            pattern: ^/
            form_login:
                provider:      fos_userbundle
                csrf_provider: form.csrf_provider
                remember_me:   true
            logout:       true
            anonymous:    true
            remember_me:
                key:      "%secret%"
                lifetime: 31536000 # 365 days, in seconds
                path:     /
                domain:   ~

    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/friends/, role: IS_AUTHENTICATED_FULLY }
        - { path: ^/messenger/, role: IS_AUTHENTICATED_FULLY }
        - { path: ^/admin/, role: ROLE_ADMIN }

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN
```

Under the `providers` section, you are making the bundle's packaged user provider
service available via the alias `fos_userbundle`. The id of the bundle's user
provider service is `fos_user.user_manager`.

Next, take a look at examine the `firewalls` section. Here we have declared a
firewall named `main`. By specifying `form_login`, you have told the Symfony2
framework that any time a request is made to this firewall that leads to the
user needing to authenticate himself, the user will be redirected to a form
where he will be able to enter his credentials. It should come as no surprise
then that you have specified the user provider we declared earlier as the
provider for the firewall to use as part of the authentication process.

### Step 4: Configure the FOSUserBundle

Now that you have properly configured your application's `security.yml` to work
with the FOSUserBundle, the next step is to configure the bundle to work with
the specific needs of your application.

Add the following configuration to your `config.yml` file according to which type
of datastore you are using.

``` yaml
# app/config/config.yml
fos_user:
    db_driver:     orm
    firewall_name: main
    user_class:    Kiboko\SocialNetworkBundle\Entity\User
    registration:
        form:
            type:  kiboko_social_network_registration_type
    resetting:
        form:
            type:  kiboko_social_network_resetting_type
    profile:
        form:
            type:  kiboko_social_network_profile_type

stof_doctrine_extensions:
    orm:
        default:
            timestampable: true
            sluggable: true
```

### Step 5: Configure the bundle

Now configure the bundle, just set contact email (it's the "From:" email of bundle sent email)

``` yaml
# app/config/config.yml
kiboko_social_network:
    contact:
        admin:
            email:
                address: contact@example.com
                sender_name: Contact
    avatar:
        admin:
            remove:
                email:
                    address: contact@example.com
```

### Step 6: Import KibokoSocialNetworkBundle routing

Now that you have activated and configured the bundle, all that is left to do is
import the KibokoSocialNetworkBundle routing files.

By importing the routing files you will have ready made pages for things such as
logging in, creating users, etc.

In YAML:

``` yaml
# config/routing.yml
fos_user_security:
    resource: "@KibokoSocialNetworkBundle/Resources/config/routing.yml"
```

Or if you prefer XML:

``` xml
<!-- config/routing.xml -->
<import resource="@KibokoSocialNetworkBundle/Resources/config/routing.yml"/>
```

### Step 7: Update your database schema

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a new entity, the `User` class which you
created in Step 4.

For ORM run the following command.

``` bash
$ php bin/console doctrine:schema:update --force
```
