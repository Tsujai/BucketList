<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\WishRepository;
use App\Services\Sender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', requirements: ['id'=>'\d+'])]
    public function profile(UserRepository $userRepository, Request $request, WishRepository $wishRepository): Response
    {
        $isEditMode = false;
        $userConnected = $userRepository->find($request->get('id'));

        $userWishList = $wishRepository->findBy(['auteur'=>$userConnected->getId()]);

        return $this->render('security/profile.html.twig', [
            'user' => $userConnected,
            'isEditMode' => $isEditMode,
            'userWishList' => $userWishList
        ]);
    }


    #[Route('/register', name: 'app_register')]
    #[Route('/profile/edit', name: 'app_profile_edit', requirements: ['id'=>'\d+'])]
    public function edit(WishRepository $wishRepository, UserRepository $userRepository, Sender $sender, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $isEditMode = $request->get('id');

        if ($isEditMode){
            $user = $userRepository->find($request->get('id'));
            $form = $this->createForm(ModifPasswordType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                if ($userPasswordHasher->isPasswordValid($user,$form->get('oldPassword')->getData())){
                    if ($form->get('pseudo')->getData() != $user->getPseudo()){
                        $entityManager->persist($user->setPseudo($form->get('pseudo')->getData()));
                    }
                    if ($form->get('newPassword')->getData() != null){
                        $newPasswordHashed = $userPasswordHasher->hashPassword($user, $form->get('newPassword')->getData());
                        if ($form->get('oldPassword')->getData() != $form->get('newPassword')->getData()){
                            $user->setPassword($newPasswordHashed);

                            $entityManager->persist($user);

                        } else {
                            $form->get('newPassword')->get('first')->addError(new FormError('Le nouveau mot de passe doit être différent de l\'ancien'));
                        }
                    }
                    $entityManager->flush();
                    $this->addFlash('sucess','Profil mis à jour');
                    return $this->redirectToRoute('app_profile',['id' => $user->getId()]);
                } else {
                    $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect'));
                }
            }
            return $this->render('security/profile.html.twig', [
                'form' => $form,
                'user' => $user,
                'isEditMode' => $isEditMode
            ]);
        } else {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $user->setRoles(['ROLE_USER']);

                $entityManager->persist($user);
                $entityManager->flush();

                $sender->sendEmail('New user','Un nouvel utilisateur : '.$user->getEmail().' vient de s\'inscrire', 'admin@bucket-list.fr');

                $this->addFlash('sucess','Inscription réussie');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('registration/register.html.twig', [
                'form' => $form,
                'user' => $user,
            ]);
        }
    }
}
