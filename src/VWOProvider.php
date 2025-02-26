<?php

/**
 * Copyright 2024-2025 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VWOOpenFeatureProvider;

use OpenFeature\interfaces\common\Metadata as IMetadata;
use OpenFeature\interfaces\flags\EvaluationContext;
use OpenFeature\implementation\provider\ResolutionDetailsBuilder;
use OpenFeature\interfaces\hooks\Hook;
use OpenFeature\interfaces\provider\Provider;
use OpenFeature\interfaces\provider\ResolutionDetails;
use OpenFeature\implementation\common\Metadata;
use vwo\VWOClient;
use Psr\Log\LoggerInterface;

class VWOProvider implements Provider
{

    private VWOClient $client;
    private array $hooks = [];
    private LoggerInterface $logger;

    /**
     * Constructor to initialize the VWO client
     * 
     * @param VWOClient $vwoClient VWO Client instance to access various APIs
     */
    public function __construct(VWOClient $vwoClient) {
        $this->client = $vwoClient;
    }

    /**
     * Method to set the logger for the provider
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

    /**
     * Method to get the metadata
     * @return IMetadata
     */
    public function getMetadata(): IMetadata {
        return new Metadata(self::class);
    }

    /**
     * Returns the hooks attached to the provider
     * @return Hook[]
     */
    public function getHooks(): array {
        return $this->hooks;
    }

    /**
     * Method to resolve the boolean flag value for the given key
     * @param string $flagKey
     * @param bool $defaultValue
     * @param EvaluationContext|null $context
     * @return ResolutionDetails
     */
    public function resolveBooleanValue(string $flagKey, bool $defaultValue, ?EvaluationContext $context = null): ResolutionDetails {
        try {
            // Convert EvaluationContext to VWOContext
            $vwoContext = $this->convertToVWOContext($context);

            $getFlag = $this->client->getFlag($flagKey, $vwoContext);
            $variables = $getFlag->getVariables();

            $value = $defaultValue;
            if ($context && $context->getAttributes()->get('key')) {
                foreach ($variables as $variableKey => $variableValue) {
                    if (is_bool($variableValue) && $context->getAttributes()->get('key') === $variableKey) {
                        $value = $variableValue;
                        break;
                    }
                }
            } else {
                $value = $getFlag->isEnabled();
            }
            return $this->buildResolutionDetails($value);
        } catch (\Exception $e) {
            return $this->buildResolutionDetails($defaultValue, $e->getMessage());
        }
    }

    /**
     * Method to resolve the string flag value for the given key
     * @param string $flagKey
     * @param string $defaultValue
     * @param EvaluationContext|null $context
     * @return ResolutionDetails
     */
    public function resolveStringValue(string $flagKey, string $defaultValue, ?EvaluationContext $context = null): ResolutionDetails {
        try {
            // Convert EvaluationContext to VWOContext
            $vwoContext = $this->convertToVWOContext($context);

            $getFlag = $this->client->getFlag($flagKey, $vwoContext);
            $variables = $getFlag->getVariables();

            $value = $defaultValue;
            if ($context && $context->getAttributes()->get('key')) {
                foreach ($variables as $variableKey => $variableValue) {
                    if (is_string($variableValue) && $context->getAttributes()->get('key') === $variableKey) {
                        $value = $variableValue;
                        break;
                    }
                }
            }

            return $this->buildResolutionDetails($value);
        } catch (\Exception $e) {
            return $this->buildResolutionDetails($defaultValue, $e->getMessage());
        }
    }

    /**
     * Method to resolve the integer flag value for the given key
     * @param string $flagKey
     * @param int $defaultValue
     * @param EvaluationContext|null $context
     * @return ResolutionDetails
     */
    public function resolveIntegerValue(string $flagKey, int $defaultValue, ?EvaluationContext $context = null): ResolutionDetails {
        try {
            // Convert EvaluationContext to VWOContext
            $vwoContext = $this->convertToVWOContext($context);

            $getFlag = $this->client->getFlag($flagKey, $vwoContext);
            $variables = $getFlag->getVariables();

            $value = $defaultValue;
            if ($context && $context->getAttributes()->get('key')) {
                foreach ($variables as $variableKey => $variableValue) {
                    if (is_integer($variableValue) && $context->getAttributes()->get('key') === $variableKey) {
                        $value = $variableValue;
                        break;
                    }
                }
            }

            return $this->buildResolutionDetails($value);
        } catch (\Exception $e) {
            return $this->buildResolutionDetails($defaultValue, $e->getMessage());
        }
    }

    /**
     * Method to resolve the float flag value for the given key
     * @param string $flagKey
     * @param float $defaultValue
     * @param EvaluationContext|null $context
     * @return ResolutionDetails
     */
    public function resolveFloatValue(string $flagKey, float $defaultValue, ?EvaluationContext $context = null): ResolutionDetails {
        try {
            // Convert EvaluationContext to VWOContext
            $vwoContext = $this->convertToVWOContext($context);

            $getFlag = $this->client->getFlag($flagKey, $vwoContext);
            $variables = $getFlag->getVariables();

            $value = $defaultValue;
            if ($context && $context->getAttributes()->get('key')) {
                foreach ($variables as $variableKey => $variableValue) {
                    if (is_float($variableValue) && $context->getAttributes()->get('key') === $variableKey) {
                        $value = $variableValue;
                        break;
                    }
                }
            }

            return $this->buildResolutionDetails($value);
        } catch (\Exception $e) {
            return $this->buildResolutionDetails($defaultValue, $e->getMessage());
        }
    }

    /**
     * Method to resolve the object flag value for the given key
     * @param string $flagKey
     * @param mixed $defaultValue
     * @param EvaluationContext|null $context
     * @return ResolutionDetails
     */
    public function resolveObjectValue(string $flagKey, $defaultValue, ?EvaluationContext $context = null): ResolutionDetails {
        try {
            // Convert EvaluationContext to VWOContext
            $vwoContext = $this->convertToVWOContext($context);
    
            // Get the flag details
            $getFlag = $this->client->getFlag($flagKey, $vwoContext);
            $variables = $getFlag->getVariables() ?: $defaultValue;
            // Extract the key from the context if available
            $variableKey = $context && $context->getAttributes()->get('key') ? $context->getAttributes()->get('key') : null;

            // Case 1: Return a single variable if a key is provided
            if (!empty($variableKey)) {
                if (isset($variables[$variableKey])) {
                    $variable = $variables[$variableKey];

                    if (is_object($variable)) {
                        return $this->buildResolutionDetails($variable);
                    } else {
                        // If the variable is not an object, return it as is
                        return $this->buildResolutionDetails($variable);
                    }
                }
    
                // Return default value if no matching key is found
                return $this->buildResolutionDetails($defaultValue);
            }
    
            // Case 2: Return all variables
            return $this->buildResolutionDetails($variables);
    
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return $this->buildResolutionDetails($defaultValue, $e->getMessage());
        }
    }

    /**
     * Return VWO Client instance used by this OpenFeature provider.
     * @return VWOClient
     */
    public function getClient(): VWOClient {
        return $this->client;
    }

    /**
     * Helper method to convert EvaluationContext to VWOContext
     * @param EvaluationContext|null $evaluationContext
     * @return array
     */
    private function convertToVWOContext(?EvaluationContext $evaluationContext) {
        // Initialize the VWOContext (ContextModel in your SDK)
        $vwoContext = [];

        // If the evaluation context is null or has no targeting key, return an empty context
        if ($evaluationContext === null || $evaluationContext->getTargetingKey() === null) {
            return $vwoContext;
        }
    
        // Set the 'id' from the evaluation context (equivalent to userId)
        if ($evaluationContext->getTargetingKey()) {
            $vwoContext['id'] = $evaluationContext->getTargetingKey(); // Add the targeting key as 'id'
        }
    
        $attributes = $evaluationContext->getAttributes();

        // Extract user_agent, ip_address, and other custom variables if present
        $vwoContext['userAgent'] = $attributes->get('userAgent') ?? "";

        // Extract ip_address and set a default value if not present
        $vwoContext['ipAddress'] = $attributes->get('ipAddress') ?? "";


        $customVariables = $attributes->get('customVariables');
        if ($customVariables !== null) {
            $vwoContext['customVariables'] = $customVariables; // Add custom variables to the context array
        } 
    
        $variationTargetingVariables = $attributes->get('variationTargetingVariables');
        if ($variationTargetingVariables !== null) {
            $vwoContext['variationTargetingVariables'] = $variationTargetingVariables; // Add variation targeting variables to the context array
        }

        return $vwoContext;
    }

    private function buildResolutionDetails($value, $error = null, $reason = null): ResolutionDetails {
        $resolutionDetails = new ResolutionDetailsBuilder();

        // Convert stdClass to array if needed
        if (is_object($value) && $value instanceof \stdClass) {
            $value = (array)$value; // Convert to associative array
        }
    
        $resolutionDetails->withValue($value);
        if ($error !== null) {
            $resolutionDetails->withError($error);
        }
        if ($reason !== null) {
            $resolutionDetails->withReason($reason);
        }
        return $resolutionDetails->build();
    }
}
