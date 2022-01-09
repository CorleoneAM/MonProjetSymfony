<?php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Category;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/user")
 */
class ApiUserController extends AbstractController
{
    
    /**
     * @Route("/", name="api_user_index", format="json", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $serializer = new Serializer ([new ObjectNormalizer()]);
        foreach ($userRepository->findAll() as $user) {
            $users[] = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['firstName','email', 'roles', 'password']]); // au lieu de le faire manuelle $users[]= ['name'=> $user->gatName(),...] if faut convertir arraylist(resultat find all) to array et object user to array cepourcela on utilise serialization
        }
        
         return new JsonResponse($users);
     }

      /**
     * @Route("/new", name="api_user_new", methods={"POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user); // liez l'objet form a la requette reçu
        $form->handleRequest($request);

        $form->submit($request->request->all());
        $serializer = new Serializer([new ObjectNormalizer()]);

        if ($form->isValid()) {
            
            $password=$passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            /***/ 
            $user_json = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['firstName','email', 'roles', 'password']]); // au lieu de le faire manuelle $users[]= ['name'=> $user->gatName(),...] if faut convertir arraylist(resultat find all) to array et object user to array cepourcela on utilise serialization
        

            return new JsonResponse($user_json);
        }

        return new JsonResponse($serializer->normalize($form->getErrors()));
        
    }

}