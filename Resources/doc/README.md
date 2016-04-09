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
    connection:
        scheme: http        # optional
        host: localhost     # optional
        port: 7474          # optional
        timeout: 60         # optional
        username: neo4j     # optional
        password: neo4j     # optional
    types: []               # optional, Classes implementing Innmind\Neo4j\ONM\TypeInterface
    persister: innmind_neo4j.persister.delegation # optional, The service name to use in the unit of work to persist the entity container
    metadata_configuration: innmind_neo4j.metadata_builder.configuration # optional, The service to use to validate a metadata configuration
```

## Configuration

Once the bundle is installed you need to start configuring your entities. In each of your bundles where you want to create your entities, you need to create the file `neo4j.yml` (or file in a folder named `neo4j`) inside the folder `Resources/config`.

Each configuration file must follow the structure described in the [ONM library](https://github.com/Innmind/neo4j-onm/blob/master/README.md#configuration).

## Usage

Once the bundle and entities configuration are done, you can now access the manager via different the service `innmind_neo4j.manager`.

Example:
```php
$manager = $container->get('innmind_neo4j.registry');
$repository = $manager->repository(MyEntity::class);
```

For complete understanding of what you can do please refer to the [ONM documentation](https://github.com/Innmind/neo4j-onm/blob/master/README.md#documentation).
