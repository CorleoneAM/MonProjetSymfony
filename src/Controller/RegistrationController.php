<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use  App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
     
     /**
     * @Route("/registration")
     */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }

    /**
     * @Route("/register", name="registration_new")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user=new User();
        $form=$this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password=$passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_login');

        }
        return $this->render('registration/register.html.twig',
        array('form'=>$form->createView())
    );
    }
}
