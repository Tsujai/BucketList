<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\CreateWishType;
use App\Repository\UserRepository;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/wish',name: 'app_wish')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(criteria: [], orderBy: ['dateCreated'=>'DESC']);
        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ['id'=>'\d+'])]
    public function details(int $id, WishRepository $wishRepository, Request $request): Response
    {
        $profile = $request->get('profile');
        $wish = $wishRepository->find($id);
        return $this->render('wish/detail.html.twig', [
            'wish' => $wish,
            'picturePath' => $this->getParameter('picture_path'),
            'profile' => $profile,
        ]);
    }

    #[Route('/create', name: '_create')]
    #[Route('/modify/{id}', name: '_modify', requirements: ['id'=>'\d+'])]
    public function create(?Wish $wish, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED')){
            $this->addFlash('danger','Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $profile = $request->get('profile');
        $isEditMode = $wish ? true : false;

        if (!$isEditMode){
            $wish = new Wish();
            $wish->setAuteur($userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]));
        } elseif (($this->getUser() != $wish->getAuteur()) && (!$this->isGranted('ROLE_ADMIN'))){
            $this->addFlash('danger', 'Accès interdit ! TRUAND !!!');
            return $this->redirectToRoute('app_wish_list');
        }

        $form = $this->createForm(CreateWishType::class, $wish, ['pseudo' => $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()])->getPseudo()]);
        $form->handleRequest($request);
        $path = $this->getParameter('picture_path');

        if ($form->isSubmitted() && $form->isValid()){
            $wish->setAuteur($userRepository->findOneBy(['pseudo' => $form->get('auteur')->getData()]));
            if (!$isEditMode){
                if ($form->get('picture')->getData() instanceof UploadedFile){
                    $picture = $form->get('picture')->getData();
                    $pictureName = $slugger->slug($wish->getTitle()).'-'.uniqid().'.'.$picture->guessExtension();
                    $picture->move($path, $pictureName);
                }

                $em->persist($wish);
                $em->flush();

                $this->addFlash('success','Votre souhait a été enregistré');
                return $this->redirectToRoute('app_wish_list');
            } else {
                if ($form->get('picture')->getData() instanceof UploadedFile){
                    $picture = $form->get('picture')->getData();
                    $pictureName = $slugger->slug($wish->getTitle()).'-'.uniqid().'.'.$picture->guessExtension();
                    $picture->move($path, $pictureName);

                    if ($wish->getPicture() && \file_exists($path . $wish->getPicture())){
                        unlink($path . $wish->getPicture());
                    }
                    $wish->setPicture($pictureName);

                } else if ($form->get('isDeleted')->getData()){
                    unlink($path.$wish->getPicture());
                    $wish->setPicture(null);
                }

                $em->persist($wish);
                $em->flush();

                $this->addFlash('success','Votre souhait a été modifié');
                return $this->redirectToRoute('app_wish_detail',['id' => $wish->getId(), 'profile' => $profile]);
            }

        }
        return $this->render('wish/create.html.twig', [
            'form'=>$form,
            'isEditMode'=>$isEditMode,
            'wish'=>$wish,
            'path'=>$path,
            'profile'=>$profile,
        ]);
    }

    #[Route('/detail/delete/{id}', name: '_delete', requirements: ['id'=>'\d+'])]
    public function delete(int $id, WishRepository $wishRepository, EntityManagerInterface $em, Request $request, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('danger','Vous n\'êtes pas autorisé à faire cette action');
            return $this->redirectToRoute('app_wish_detail', ['id'=>$id]);
        }

        $profile = $request->get('profile');
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $wish = $wishRepository->find($id);
        $em->remove($wish);
        $em->flush();

        $this->addFlash('success','Votre souhait a été supprimé');
        if ($profile){
            return $this->redirectToRoute('app_profile',['id' => $user->getId()]);
        }else{
            return $this->redirectToRoute('app_wish_list');
        }

    }
}
