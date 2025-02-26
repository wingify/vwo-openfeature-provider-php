# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-02-26

### Added

- **First release of VWO OpenFeature Provider PHP**

  ```php
  <?php

  require './vendor/autoload.php';

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
