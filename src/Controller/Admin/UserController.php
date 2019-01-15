<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 12/31/2018
 * Time: 2:48 AM
 */


namespace App\Controller\Admin;

use App\Entity\User;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/admin/users" ,name="list_users",methods="GET")
     */
    public function getUsersList()
    {

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users/list.html.twig', array('users' => $users));
    }


    /**
     * @Route("/admin/users/add_user",name="add_user",methods="GET|POST")
     */
    public function add_user(Request $request) : Response
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

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('list_users');
                    $response = new RedirectResponse($url);
                }

//                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                $this->addFlash('success','The user added successfully');
                return $response;
            }
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('admin/users/add.html.twig', array(
            'form' => $form->createView(),
        ));

    }


    /**
     * @Route("/admin/users/edit_user/{id}",name="edit_user", methods="GET|POST")
     */
    public function edit_user(Request $request,$id,UserPasswordEncoderInterface $passwordEncoder) : Response
    {


        $user = new User();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $password = $user->getPassword();
        $user_form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class, array('attr' => array('class' => 'form_control'),'required' => false))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form_control')))
            ->add('edit', SubmitType::class, ['label' => 'Edit User', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();



        $user_form->handleRequest($request);

        if ($user_form->isSubmitted() && $user_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if(!empty($user_form->get('password')->getData())){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $user_form->get('password')->getData()
                    )
                );
            }
            else{
                $user->setPassword($password);
            }



            $entityManager->flush();

            $this->addFlash('success','The user updated successfully');
            return $this->redirectToRoute('list_users');
        }

        return $this->render('admin/users/edit.html.twig', array('form' => $user_form->createView()));
    }


    /**
     * @Route("/admin/users/delete_user/{id}",name="delete_user", methods="DELETE")
     */
    public function delete_user(Request $request, $id) :Response
    {

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success','The user deleted successfully');

        $response = new Response();
        $response->send();


    }

    /**
     * @Route("/admin/users/show_user/{id}",name="show_user", methods="GET")
     */
    public function show_user(Request $request, $id) :Response
    {

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        return $this->render('admin/users/show.html.twig', array('user' => $user));


    }
}