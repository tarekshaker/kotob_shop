<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 1/14/2019
 * Time: 1:17 AM
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserService
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var  TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $storage
     */
    public function __construct(TokenStorageInterface $storage, UserRepository $userRepository)
    {
        $this->tokenStorage = $storage;
        $this->userRepository = $userRepository;
    }

    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {

            /** @var User $user */
            $user = $token->getUser();
            return $user;

        } else {
            return null;
        }
    }

    public function getAllUsersCount()
    {
        return $this->userRepository->getAllUsersCount();
    }


}