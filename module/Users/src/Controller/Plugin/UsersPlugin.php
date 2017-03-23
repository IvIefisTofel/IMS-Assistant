<?php

namespace Users\Controller\Plugin;

use Users\Entity\Users;
use MCms\Controller\Plugin\MCmsPlugin;

class UsersPlugin extends MCmsPlugin
{
    /**
     * @param Users[] $users
     * @param array $options
     * @return array|null
     * @throws \Exception
     */
    public function toArray($users = null, $options = [])
    {
        if ($users !== null) {
            $onlyNames = isset($options['onlyNames']) ? $options['onlyNames'] : false;
            $saveIds = isset($options['clearIds']) ? $options['clearIds'] : false;

            $result = [];
            if (!is_array($users)) {
                $users = [$users];
            }
            
            foreach ($users as $key => $user) {
                if (get_class($user) == Users::class) {
                    if ($onlyNames) {
                        $result[$user->getId()] = $user->getName();
                    } else {
                        $result[$user->getId()] = $user->toArray();
                        $result[$user->getId()]['registrationDate'] = $user->getRegistrationDateFormat('Y-m-d');
                        $result[$user->getId()]['grAvatar'] = $user->getGrAvatar();
                        $result[$user->getId()]['roleId'] = $user->getRoleID();
                        if ($user->getId() == ($identity = $this->controller->identity())->getId()) {
                            $result[$user->getId()]['currentRole'] = $identity->getCurrentRole();
                        }
                    }
                } else {
                    throw new \Exception('Array elements must be User class.');
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