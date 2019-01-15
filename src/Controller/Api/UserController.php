<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 1/6/2019
 * Time: 10:16 AM
 */

namespace App\Controller\Api;

use App\Helper\ControllerHelper;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends BaseController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;


    /**
     * UserController constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param FactoryInterface $formFactory
     * @param UserManagerInterface $userService
     * @param TokenStorage $tokenStorage
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorage $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Rest\Post("/register")
     * @param Request $request
     * @return mixed
     */
    public function registrationAction(Request $request) : View
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $form = $this->formFactory->createForm(array('csrf_protection' => false));
        $form->setData($user);
        $this->processForm($request, $form);
        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            $this->userManager->updateUser($user);
            $response = View::create(array ('success'=>'User added successfully'), Response::HTTP_OK);
        } else {
            $errors = $this->getErrorsFromForm($form);
            $response =  View::create(array('errors'=>$errors), Response::HTTP_OK);
        }

        $response->setFormat('json');
        $response->setHeader('Cache-Control','no-cache');
        $response->setHeader('Pragma','no-cache');
        $response->setHeader('Expires', '0');

        return $response;

    }
    /**
     * @param Request $request
     * @param FormInterface $form
     */
    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttserializepException();
        }
        $form->submit($data);
    }

    /**
     * Returns form errors.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessage();
            $parameters = $error->getMessageParameters();
            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            $errors[$key] = $template;
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}