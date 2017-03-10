<?php

namespace MCms\Controller\Plugin;

class ErrorsPlugin extends MCmsPlugin
{
    public function toArray($errors = null, $options = [])
    {
        if ($errors !== null) {
            $saveIds = isset($options['clearIds']) ? $options['clearIds'] : false;

            $result = [];

            if (!is_array($errors)) {
                $errors = [$errors];
            }

            foreach ($errors as $key => $error) {
                if (get_class($error) == 'MCms\Entity\Errors') {
                    /* @var $error \MCms\Entity\Errors */
                    $result[$error->getId()] = $error->toArray();
                    $result[$error->getId()]['date'] = $error->getDateFormat('Y-m-d');
                    unset($result[$error->getId()]['hash']);
                } else {
                    throw new \Exception('Array elements must be MCms\Entity\Errors class.');
                }
            }

            if ($saveIds) {
                return $result;
            } else {
                return array_values($result);
            }
        }

        return null;
    }
}