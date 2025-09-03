<?php

namespace App\Controller;

use App\Entity\GroupUser;
use App\Form\GroupUserType;
use App\Repository\GroupUserRepository; // Make sure to import the repository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupUserController extends AbstractController
{
    #[Route('/admin/groups', name: 'app_group_user_index')]
    public function index(GroupUserRepository $groupUserRepository): Response
    {
        // Get all GroupUser objects from the database
        $groups = $groupUserRepository->findAll();

        return $this->render('group_user/index.html.twig', [
            'groups' => $groups, 
        ]);
    }

      #[Route('/admin/groups/new', name: 'app_group_user_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupUser = new GroupUser();
        $form = $this->createForm(GroupUserType::class, $groupUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupUser);
            $entityManager->flush();

            $this->addFlash('success', '¡Grupo creado exitosamente!');

            return $this->redirectToRoute('app_group_user_index');
        }

        return $this->render('group_user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     #[Route('/admin/groups/{id}/edit', name: 'app_group_user_edit')]
    public function edit(Request $request, GroupUser $groupUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupUserType::class, $groupUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', '¡Grupo actualizado exitosamente!');

            return $this->redirectToRoute('app_group_user_index');
        }

        return $this->render('group_user/edit.html.twig', [
            'group' => $groupUser,
            'form' => $form->createView(),
        ]);
    }

}