## VWO OpenFeature Provider PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/vwo/vwo-openfeature-provider-php.svg)](https://packagist.org/packages/vwo/vwo-openfeature-provider-php)
[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

### Requirements

> PHP >= 7.4

### Installation

Install the latest version with

```bash
composer require vwo/vwo-openfeature-provider-php
```

## Basic Usage

```php
use OpenFeature\OpenFeatureAPI;
use OpenFeature\implementation\flags\EvaluationContext;
use VWOOpenFeatureProvider\VWOProvider;
use vwo\VWO;

class OpenFeatureTest {
  public static function main() {
    // Initialize the VWO client options
    $vwoInitOptions = [
        'sdkKey' => 'your-sdk-key-here',     // Replace with your SDK Key
        'accountId' => 123456,               // Replace with your VWO Account ID
    ];

    // Initialize VWO Client
    $vwoClient = VWO::init($vwoInitOptions);
    if ($vwoClient === null) {
        echo "Failed to initialize VWO Client\n";
        return;
    }

    // Initialize the VWO provider
    $vwoProvider = new VWOProvider($vwoClient);

    // Set the provider using OpenFeature API
    $api = OpenFeatureAPI::getInstance();
    $api->setProvider($vwoProvider);

    // Call the test flags method to evaluate different flag types
    self::testFlags($api);
  }

  public static function testFlags(OpenFeatureAPI $api) {
    // Create custom variables for the context
    $customVariables = [
        'name' => 'Ashley'
    ];

    // Manually creating EvaluationContext with targetingKey and additional attributes
    $attributes = new OpenFeature\implementation\flags\Attributes([
        'key' => 'variable-key',
        'customVariables' => $customVariables, // Custom variables
    ]);

    $context = new EvaluationContext('userId1', $attributes);

    // Get the client from OpenFeature API
    $client = $api->getClient();

    // Test object flag
    $objectResult = $client->getObjectValue('f1',$customVariables, $context);
    echo "OBJECT result: " . json_encode($objectResult) . "\n";
  }
}

// Run the OpenFeatureTest script
OpenFeatureTest::main();
```

## Changelog

Refer [CHANGELOG.md](https://github.com/wingify/vwo-openfeature-provider-php/blob/master/CHANGELOG.md)

## Setting up development environment

```bash
composer run-script start
```

### Contributing

Please go through our [contributing guidelines](https://github.com/wingify/vwo-openfeature-provider-php/blob/master/CONTRIBUTING.md)

### Code of Conduct

[Code of Conduct](https://github.com/wingify/vwo-openfeature-provider-php/blob/master/CODE_OF_CONDUCT.md)

### License

[Apache License, Version 2.0](https://github.com/wingify/vwo-openfeature-provider-php/blob/master/LICENSE)

Copyright 2024-2025 Wingify Software Pvt. Ltd.
