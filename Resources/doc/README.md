# Getting started with InnmindNeo4jBundle

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
composer require innmind/neo4j-bundle
```

### Step 2: Enable the bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Innmind\Neo4jBundle\InnmindNeo4jBundle,
        );
        // ...
    }
    // ...
}
```

### Step 3: Add the configuration

Finally add the bundle configuration in the appropriate `config` file (ie: `app/config/config.yml`, or any of the env specific ones).

```yaml
innmind_neo4j:
    connections:
        my_conn:
            scheme: http        # optional
            host: localhost     # optional
            port: 7474          # optional
            timeout: 60         # optional
            username: neo4j
            password: neo4j
    managers:
        my_manager:
            connection: my_conn
            reader: yaml
            bundles: []         #optional
    default_manager: my_manager # default to : default
    disbale_aliases: false      # optional
```

## Configuration

Once the bundle is installed you need to start configuring your entities. In each of your bundles where you want to create your entities, you need to create the file `neo4j.yml` (or file in a folder named `neo4j`) inside the folder `Resources/config`.

Each configuration file must follow the structure described in the [ONM library](https://github.com/Innmind/neo4j-onm/blob/master/docs/README.md#configuration).

### Advanced

In case you want to use different manager per bundle, you need to specify the list of bundles the manager will handle. I.e:

```yaml
innmin_neo4j:
    managers:
        my_manager:
            ...
            bundles: [AcmeFooBundle]
```

The above `my_manager` will only handle entities from the `AcmeFooBundle`, in the `bundles` array you always need to use the bundle short naming used by Symfony.

## Behaviour

Once the bundle and entities configuration done, you can now access the managers via different services.

To access the registry of managers, retrieve the service `innmind_neo4j.registry` (aliased by `neo4j` or `graph`). Then you can access each manager declared via the method `getManager`.

Example:
```php
$registry = $container->get('innmind_neo4j.registry');
$defaultManager = $registry->getManager();
$anotherManager = $registry->getManager('another_manager');
```

**Note**: aliases can be disabled via the bundle configuration key `disable_aliases`.

To see what you can do with a manager, please refer to the [ONM library](https://github.com/Innmind/neo4j-onm/blob/master/docs/README.md#entity-manipulation).

If you want to inject a manager inside another service, you can directly access them via a service name following this pattern: `innmind_neo4j.manager.{manager name}`. For the example used above, you would have the following services: `innmind_neo4j.manager.default` and `innmind_neo4j.manager.another_manager`.

## Commands

This bundle also provides a command `neo4j:generate:entities` to generate the PHP code corresponding to the entities you have configured. It will generate both entities and repositories; in case an entity file already exists, it will move the old file by appending `~` to its filename and generate a new one (repositories are not overriden).
