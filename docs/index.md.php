<title>Documentation</title>

# Documentation

EmbeDi allows us to configure objects properties transparently, without explicitly setting them. It implements
pattern similar to flyweight pattern, but new instance of configured *might* be created.
Object instance will be configured with optional configuration ID.

### Installing

To start using EmbeDi, standard composer installation procedure is required:

```
composer install maslosoft/embedi
```

### Providing configuration

Next step is to inform EmbeDi, about available configurations. These are defined by array with instance ID as a key, 
and appropriate class name as a value, or `class` key with said class name. This can also be achieved by framework adapters,
so that EmbeDi will use existing config files.

In most simple example, create flyweight instance of EmbeDi and add configuration with `ArrayAdapter`. In this example,
we will configure [addendum project](/addendum/). Default configuration ID for addendum is `addendum`, so we will use it.
We want to configure some extra properties, so besides class name, we will pass extra options. In the end we will add
adapter.

```
$config = [
	'addendum' => [
		// Addendum main class - required
		'class' => Addendum::class,
		// Check for modifications - other properties
		'checkMTime' => true,
	]
];
EmbeDi::fly()->addAdapter(new ArrayAdapter($config));
```

Now when creating instance of addendum, it will have pre-configured `checkMTime`
property:

```
$ad = new Addendum;
$ad->checkMTime; // Has value `true`
```

Check [this repository for working example](https://github.com/MaslosoftGuides/embedi.quick-start).
